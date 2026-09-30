<?php
// Controllers/LetterController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Letter;
use App\Models\LetterCategory;
use App\Models\Komisariat;
use App\Models\LetterTimeline;
use App\Models\Disposition;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Helpers\QrCode;
use App\Config\App;

class LetterController
{
    public function index(): void
    {
        Auth::requireAuth();

        $letterModel = new Letter();
        $catModel = new LetterCategory();
        $komisariatModel = new Komisariat();

        $filters = [
            'direction' => $_GET['direction'] ?? '',
            'status' => $_GET['status'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'komisariat_id' => $_GET['komisariat_id'] ?? '',
            'search' => trim($_GET['q'] ?? ''),
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
        ];

        // Jika DPK, filter hanya surat DPK-nya sendiri ATAU surat keluar resmi Komisariat Besar
        if (Auth::isDpk()) {
            $filters['komisariat_id'] = (string)Auth::dpkId();
        }

        $letters = $letterModel->getFiltered($filters, 100);
        $categories = $catModel->getActive();
        $komisariats = $komisariatModel->getActive();

        View::render('letters.index', [
            'title' => 'Daftar Surat - SIMAS GMNI UNISDA',
            'letters' => $letters,
            'categories' => $categories,
            'komisariats' => $komisariats,
            'filters' => $filters
        ]);
    }

    public function create(): void
    {
        Auth::requireAuth();

        $catModel = new LetterCategory();
        $komisariatModel = new Komisariat();

        $categories = $catModel->getActive();
        $komisariats = $komisariatModel->getActive();

        View::render('letters.create', [
            'title' => 'Buat Pengajuan Surat / Rekomendasi Baru',
            'categories' => $categories,
            'komisariats' => $komisariats,
            'user' => Auth::user()
        ]);
    }

    public function store(): void
    {
        Auth::requireAuth();
        Csrf::verify();

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $letterDate = $_POST['letter_date'] ?? date('Y-m-d');
        $recipient = trim($_POST['recipient'] ?? 'Komisariat Besar GMNI UNISDA');
        $direction = $_POST['direction'] ?? 'masuk';

        if ($title === '' || $categoryId === 0) {
            View::flash('error', 'Perihal/Judul Surat dan Kategori wajib diisi.');
            header('Location: ' . App::baseUrl('letters/create'));
            exit;
        }

        $user = Auth::user();
        $komisariatId = null;
        $sender = '';

        if (Auth::isDpk()) {
            $komisariatId = Auth::dpkId();
            $komisariat = (new Komisariat())->find($komisariatId);
            $sender = $komisariat['name'] ?? 'DPK GMNI Fakultas';
            $direction = 'masuk'; // Dari DPK ke Komisariat Besar
        } else {
            // Pengurus Komisariat Besar
            if ($direction === 'keluar') {
                $sender = 'Komisariat Besar GMNI UNISDA';
                $komisariatId = (new Komisariat())->findByCode('KOM-BESAR')['id'] ?? null;
            } else {
                $sender = trim($_POST['sender'] ?? 'Eksternal Kampus / DPC');
                $komisariatId = !empty($_POST['komisariat_id']) ? (int)$_POST['komisariat_id'] : null;
            }
        }

        // Upload attachment berkas draf / proposal
        $attachmentPath = null;
        if (!empty($_FILES['attachment']['name'])) {
            $file = $_FILES['attachment'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                if (in_array($ext, $allowed, true)) {
                    $uploadDir = __DIR__ . '/../../public/uploads/documents/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $newFileName = 'DOC_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $attachmentPath = 'uploads/documents/' . $newFileName;
                    }
                } else {
                    View::flash('error', 'Format file tidak didukung. Harap unggah format PDF, DOC, DOCX, atau Gambar.');
                    header('Location: ' . App::baseUrl('letters/create'));
                    exit;
                }
            }
        }

        $letterModel = new Letter();
        $trackingCode = 'SRT-' . date('Ymd') . '-' . sprintf('%03d', rand(100, 999));
        $verificationToken = bin2hex(random_bytes(16));

        // Tentukan initial status
        $status = 'diajukan';
        $refNumber = null;

        // Jika dibuat langsung oleh Komisariat Besar sebagai surat keluar resmi:
        if (Auth::isKomisariatBesar() && $direction === 'keluar') {
            $refNumber = $letterModel->generateOfficialNumber($categoryId, null, $letterDate);
            $status = Auth::isKetuaBesar() ? 'disetujui' : 'diverifikasi';
        }

        $letterId = $letterModel->create([
            'tracking_code' => $trackingCode,
            'reference_number' => $refNumber,
            'category_id' => $categoryId,
            'komisariat_id' => $komisariatId,
            'direction' => $direction,
            'sender' => $sender,
            'recipient' => $recipient,
            'title' => $title,
            'description' => $description,
            'letter_date' => $letterDate,
            'attachment_path' => $attachmentPath,
            'status' => $status,
            'verification_token' => $verificationToken,
            'created_by' => $user['id'],
            'approved_by' => ($status === 'disetujui' && Auth::isKetuaBesar()) ? $user['id'] : null,
            'approved_at' => ($status === 'disetujui' && Auth::isKetuaBesar()) ? date('Y-m-d H:i:s') : null,
        ]);

        // Catat di timeline
        LetterTimeline::log(
            $letterId,
            $user['id'],
            'Pengajuan Berkas Diserahkan ke Sistem',
            $status,
            'Surat berhasil diunggah secara online ke sistem manajemen surat.'
        );

        View::flash('success', "Surat berhasil diajukan dengan Kode Tracking: {$trackingCode}");
        header('Location: ' . App::baseUrl('letters/show?id=' . $letterId));
        exit;
    }

    public function show(): void
    {
        Auth::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $letterModel = new Letter();
        $letter = $letterModel->findWithDetails($id);

        if (!$letter) {
            View::flash('error', 'Surat tidak ditemukan.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        // Cek izin akses jika user adalah DPK
        if (Auth::isDpk()) {
            $dpkId = Auth::dpkId();
            if ($letter['komisariat_id'] && (int)$letter['komisariat_id'] !== $dpkId && $letter['direction'] === 'masuk') {
                View::flash('error', 'Anda tidak memiliki akses ke surat DPK lain.');
                header('Location: ' . App::baseUrl('letters'));
                exit;
            }
        }

        $timeline = (new LetterTimeline())->getByLetter($id);
        $dispositions = (new Disposition())->getByLetter($id);

        // Generate QR Code SVG for verification
        $verifUrl = App::baseUrl('verifikasi?token=' . $letter['verification_token']);
        $qrSvg = QrCode::svg($verifUrl, 160);

        View::render('letters.show', [
            'title' => 'Detail Surat: ' . $letter['title'],
            'letter' => $letter,
            'timeline' => $timeline,
            'dispositions' => $dispositions,
            'qrSvg' => $qrSvg,
            'verifUrl' => $verifUrl,
            'user' => Auth::user()
        ]);
    }

    public function edit(): void
    {
        Auth::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $letterModel = new Letter();
        $letter = $letterModel->findWithDetails($id);

        if (!$letter) {
            View::flash('error', 'Surat tidak ditemukan.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        // Cek wewenang edit: Komisariat Besar atau DPK pembuat saat status belum disahkan
        if (Auth::isDpk()) {
            if ((int)$letter['komisariat_id'] !== Auth::dpkId() || in_array($letter['status'], ['disetujui', 'selesai'])) {
                View::flash('error', 'Surat yang telah disahkan resmi tidak dapat diedit.');
                header('Location: ' . App::baseUrl('letters/show?id=' . $id));
                exit;
            }
        }

        $catModel = new LetterCategory();
        $categories = $catModel->getActive();

        View::render('letters.edit', [
            'title' => 'Perbaiki / Edit Surat: ' . $letter['tracking_code'],
            'letter' => $letter,
            'categories' => $categories,
            'user' => Auth::user()
        ]);
    }

    public function update(): void
    {
        Auth::requireAuth();
        Csrf::verify();

        $id = (int)($_POST['id'] ?? 0);
        $letterModel = new Letter();
        $letter = $letterModel->findWithDetails($id);

        if (!$letter) {
            View::flash('error', 'Surat tidak ditemukan.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $letterDate = $_POST['letter_date'] ?? $letter['letter_date'];
        $recipient = trim($_POST['recipient'] ?? $letter['recipient']);

        if ($title === '' || $categoryId === 0) {
            View::flash('error', 'Perihal/Judul Surat dan Kategori wajib diisi.');
            header('Location: ' . App::baseUrl('letters/edit?id=' . $id));
            exit;
        }

        // Handle re-upload file attachment jika ada file baru diunggah
        $attachmentPath = $letter['attachment_path'];
        if (!empty($_FILES['attachment']['name'])) {
            $file = $_FILES['attachment'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                if (in_array($ext, $allowed, true)) {
                    $uploadDir = __DIR__ . '/../../public/uploads/documents/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $newFileName = 'DOC_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $attachmentPath = 'uploads/documents/' . $newFileName;
                    }
                }
            }
        }

        // Jika status sebelumnya 'perlu_revisi', ubah kembali menjadi 'diajukan'
        $newStatus = $letter['status'];
        $user = Auth::user();
        $logAction = 'Perubahan Data Surat';
        $logNotes = 'Data dan berkas surat diperbarui.';

        if ($letter['status'] === 'perlu_revisi') {
            $newStatus = 'diajukan';
            $logAction = 'Revisi Berkas Diserahkan Kembali';
            $logNotes = 'Pengurus telah memperbaiki berkas sesuai catatan revisi. Berkas kembali diajukan untuk verifikasi.';
        }

        $letterModel->update($id, [
            'title' => $title,
            'category_id' => $categoryId,
            'description' => $description,
            'letter_date' => $letterDate,
            'recipient' => $recipient,
            'attachment_path' => $attachmentPath,
            'status' => $newStatus,
            'revision_notes' => ($newStatus === 'diajukan') ? null : $letter['revision_notes']
        ]);

        LetterTimeline::log($id, $user['id'], $logAction, $newStatus, $logNotes);

        View::flash('success', 'Surat berhasil diperbarui dan diserahkan kembali.');
        header('Location: ' . App::baseUrl('letters/show?id=' . $id));
        exit;
    }

    public function delete(): void
    {
        Auth::requireAuth();
        Csrf::verify();

        $id = (int)($_POST['id'] ?? 0);
        $letterModel = new Letter();
        $letter = $letterModel->findWithDetails($id);

        if (!$letter) {
            View::flash('error', 'Surat tidak ditemukan.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        // Hanya boleh hapus jika belum disetujui, atau jika user adalah admin Komisariat Besar
        if (!Auth::isKomisariatBesar() && in_array($letter['status'], ['disetujui', 'selesai'])) {
            View::flash('error', 'Surat yang telah disahkan resmi tidak dapat dihapus.');
            header('Location: ' . App::baseUrl('letters/show?id=' . $id));
            exit;
        }

        $letterModel->delete($id);

        View::flash('success', "Surat {$letter['tracking_code']} berhasil dihapus dari sistem.");
        header('Location: ' . App::baseUrl('letters'));
        exit;
    }

    public function print(): void
    {
        Auth::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $letterModel = new Letter();
        $letter = $letterModel->findWithDetails($id);

        if (!$letter) {
            View::flash('error', 'Surat tidak ditemukan.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        $verifUrl = App::baseUrl('verifikasi?token=' . $letter['verification_token']);
        $qrSvg = QrCode::svg($verifUrl, 140);

        View::render('letters.print', [
            'title' => 'Cetak Surat Resmi: ' . ($letter['reference_number'] ?: $letter['tracking_code']),
            'letter' => $letter,
            'qrSvg' => $qrSvg,
            'verifUrl' => $verifUrl
        ], null);
    }
}

