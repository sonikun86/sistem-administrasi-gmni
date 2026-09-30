<?php
// Controllers/ApprovalController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Letter;
use App\Models\LetterTimeline;
use App\Models\Disposition;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;

class ApprovalController
{
    public function index(): void
    {
        Auth::requireKomisariatBesar();

        $letterModel = new Letter();

        // Antrean Verifikasi Sekretaris (status 'diajukan')
        $pendingVerification = $letterModel->getFiltered(['status' => 'diajukan'], 50);

        // Antrean Persetujuan Ketua (status 'diverifikasi')
        $pendingApproval = $letterModel->getFiltered(['status' => 'diverifikasi'], 50);

        View::render('approvals.index', [
            'title' => 'Meja Verifikasi & Persetujuan Surat',
            'pendingVerification' => $pendingVerification,
            'pendingApproval' => $pendingApproval,
            'user' => Auth::user()
        ]);
    }

    public function process(): void
    {
        Auth::requireKomisariatBesar();
        Csrf::verify();

        $letterId = (int)($_POST['letter_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        $instruction = trim($_POST['instruction'] ?? 'Tindak lanjuti dan arsipkan');

        $letterModel = new Letter();
        $letter = $letterModel->findWithDetails($letterId);

        if (!$letter) {
            View::flash('error', 'Surat tidak ditemukan.');
            header('Location: ' . App::baseUrl('approvals'));
            exit;
        }

        $user = Auth::user();

        if ($action === 'verify') {
            // Tindakan Sekretaris: Verifikasi & Terbitkan Nomor Surat Resmi
            $newRefNumber = trim($_POST['reference_number'] ?? '');
            if ($newRefNumber === '') {
                $newRefNumber = $letterModel->generateOfficialNumber(
                    (int)$letter['category_id'],
                    $letter['komisariat_id'] ? (int)$letter['komisariat_id'] : null,
                    $letter['letter_date']
                );
            }

            $letterModel->update($letterId, [
                'reference_number' => $newRefNumber,
                'status' => 'diverifikasi',
                'revision_notes' => null,
            ]);

            LetterTimeline::log(
                $letterId,
                $user['id'],
                'Diverifikasi oleh Sekretariat Besar',
                'diverifikasi',
                $notes ?: "Berkas administrasi dinyatakan valid. Nomor Surat resmi dialokasikan: {$newRefNumber}. Diteruskan kepada Ketua Komisariat Besar untuk pengesahan."
            );

            View::flash('success', "Surat berhasil diverifikasi dengan nomor resmi: {$newRefNumber}");
        } elseif ($action === 'request_revision') {
            // Tindakan Sekretaris: Minta revisi berkas ke DPK
            if ($notes === '') {
                View::flash('error', 'Harap sertakan catatan revisi yang jelas untuk pengurus DPK.');
                header('Location: ' . App::baseUrl('letters/show?id=' . $letterId));
                exit;
            }

            $letterModel->update($letterId, [
                'status' => 'perlu_revisi',
                'revision_notes' => $notes,
            ]);

            LetterTimeline::log(
                $letterId,
                $user['id'],
                'Catatan Revisi Berkas',
                'perlu_revisi',
                "Catatan Sekretariat: {$notes}"
            );

            View::flash('warning', 'Catatan revisi telah dikirimkan ke DPK terkait.');
        } elseif ($action === 'approve') {
            // Tindakan Ketua: Sahkan Surat & Terbitkan Lembar Disposisi
            $letterModel->update($letterId, [
                'status' => 'disetujui',
                'approved_by' => $user['id'],
                'approved_at' => date('Y-m-d H:i:s'),
            ]);

            // Buat Lembar Disposisi
            $dispModel = new Disposition();
            $dispModel->create([
                'letter_id' => $letterId,
                'from_user_id' => $user['id'],
                'to_komisariat_id' => $letter['komisariat_id'],
                'instruction' => $instruction,
                'notes' => $notes ?: 'Surat telah disahkan dan disetujui secara resmi oleh Ketua Komisariat Besar GMNI UNISDA.',
                'due_date' => date('Y-m-d', strtotime('+7 days')),
                'status' => 'selesai'
            ]);

            LetterTimeline::log(
                $letterId,
                $user['id'],
                'Disahkan & Disetujui Ketua Komisariat Besar',
                'disetujui',
                "Instruksi Disposisi: \"{$instruction}\". Dokumen resmi telah disahkan dengan stempel dan QR Code verifikasi keaslian digital."
            );

            View::flash('success', 'Surat berhasil disetujui dan disahkan secara resmi!');
        } elseif ($action === 'reject') {
            // Tindakan Ketua / Sekretaris: Tolak surat
            if ($notes === '') {
                View::flash('error', 'Harap berikan alasan penolakan surat.');
                header('Location: ' . App::baseUrl('letters/show?id=' . $letterId));
                exit;
            }

            $letterModel->update($letterId, [
                'status' => 'ditolak',
                'revision_notes' => $notes,
            ]);

            LetterTimeline::log(
                $letterId,
                $user['id'],
                'Pengajuan Ditolak',
                'ditolak',
                "Alasan Penolakan: {$notes}"
            );

            View::flash('error', 'Pengajuan surat telah ditolak.');
        }

        header('Location: ' . App::baseUrl('letters/show?id=' . $letterId));
        exit;
    }
}

