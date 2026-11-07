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

        // Jika DPK, tampilkan surat DPK-nya sendiri DAN seluruh surat keluar resmi dari Komisariat Besar
        if (Auth::isDpk()) {
            $filters['user_dpk_id'] = (string)Auth::dpkId();
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

        $targetDpkId = null;
        if (Auth::isDpk()) {
            $komisariatId = Auth::dpkId();
            $komisariat = (new Komisariat())->find($komisariatId);
            $sender = $komisariat['name'] ?? 'DPK GMNI Fakultas';
            $direction = 'masuk'; // Dari DPK ke Komisariat Besar
        } else {
            // Pengurus Komisariat Besar
            if (!empty($_POST['target_dpk_id']) && is_numeric($_POST['target_dpk_id'])) {
                $tid = (int)$_POST['target_dpk_id'];
                $targetDpkId = $tid > 0 ? $tid : null; // 0 atau null = masuk ke seluruh DPK se-UNISDA
            }

            if ($direction === 'keluar') {
                $sender = 'Komisariat Besar GMNI UNISDA';
                $komisariatId = (new Komisariat())->findByCode('KOM-BESAR')['id'] ?? 1;
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
            'target_dpk_id' => $targetDpkId,
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

    public static function canAccessLetter(array $letter): bool
    {
        if (Auth::isKomisariatBesar()) {
            return true;
        }

        if (Auth::isDpk()) {
            $dpkId = Auth::dpkId();
            // Surat dibuat oleh DPK sendiri
            if ((int)($letter['komisariat_id'] ?? 0) === $dpkId) {
                return true;
            }
            // Surat dari Komisariat Besar (ditujukan ke semua DPK atau khusus DPK ini)
            $isFromKombes = ($letter['direction'] === 'keluar') || 
                            str_contains($letter['sender'] ?? '', 'Komisariat Besar') ||
                            (int)($letter['komisariat_id'] ?? 0) === 1;
            
            if ($isFromKombes) {
                $targetId = !empty($letter['target_dpk_id']) ? (int)$letter['target_dpk_id'] : null;
                if ($targetId === null || $targetId === 0 || $targetId === $dpkId) {
                    return true;
                }
                if (str_contains($letter['recipient'] ?? '', 'Seluruh DPK') || str_contains($letter['recipient'] ?? '', 'Semua DPK')) {
                    return true;
                }
            }
        }

        return false;
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

        // Cek izin akses
        if (!self::canAccessLetter($letter)) {
            View::flash('error', 'Anda tidak memiliki hak akses untuk melihat surat ini.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
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

        // Cek izin akses
        if (!self::canAccessLetter($letter)) {
            View::flash('error', 'Anda tidak memiliki hak akses untuk mencetak surat ini.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        $verifUrl = App::baseUrl('verifikasi?token=' . $letter['verification_token']);
        $qrSvg = QrCode::svg($verifUrl, 100);

        View::render('letters.print', [
            'title' => 'Cetak Surat Resmi - ' . ($letter['reference_number'] ?: $letter['tracking_code']),
            'letter' => $letter,
            'verifUrl' => $verifUrl,
            'qrSvg' => $qrSvg,
        ], null);
    }

    public function download(): void
    {
        Auth::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $letter = (new Letter())->findWithDetails($id);

        if (!$letter || empty($letter['attachment_path'])) {
            View::flash('error', 'Berkas lampiran tidak ditemukan.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        // Cek izin akses
        if (!self::canAccessLetter($letter)) {
            View::flash('error', 'Anda tidak memiliki hak akses untuk mengunduh berkas surat ini.');
            header('Location: ' . App::baseUrl('letters'));
            exit;
        }

        $filePath = __DIR__ . '/../../public/' . ltrim($letter['attachment_path'], '/');
        if (!file_exists($filePath)) {
            $filePath = __DIR__ . '/../../' . ltrim($letter['attachment_path'], '/');
        }

        if (!file_exists($filePath)) {
            View::flash('error', 'File berkas fisik tidak ditemukan di server.');
            header('Location: ' . App::baseUrl('letters/show?id=' . $id));
            exit;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];
        $contentType = $mimes[$extension] ?? 'application/octet-stream';
        $filename = basename($filePath);

        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: inline; filename="' . $filename . '"');
        readfile($filePath);
        exit;
    }
}

