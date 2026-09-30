<?php
// Controllers/DashboardController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Letter;
use App\Models\Komisariat;
use App\Helpers\Auth;
use App\Helpers\View;

class DashboardController
{
    public function index(): void
    {
        Auth::requireAuth();

        $letterModel = new Letter();
        $komisariatModel = new Komisariat();
        $user = Auth::user();

        $filters = [];
        if (Auth::isDpk()) {
            $filters['komisariat_id'] = Auth::dpkId();
        }

        // Statistik
        if (Auth::isDpk()) {
            $dpkId = (int)Auth::dpkId();
            $totalLetters = $letterModel->count("komisariat_id = {$dpkId}");
            $pendingReview = $letterModel->count("komisariat_id = {$dpkId} AND status IN ('diajukan', 'diverifikasi')");
            $approvedLetters = $letterModel->count("komisariat_id = {$dpkId} AND status IN ('disetujui', 'selesai')");
            $revisionNeeded = $letterModel->count("komisariat_id = {$dpkId} AND status = 'perlu_revisi'");
        } else {
            $totalLetters = $letterModel->count();
            $pendingReview = $letterModel->count("status IN ('diajukan', 'diverifikasi')");
            $approvedLetters = $letterModel->count("status IN ('disetujui', 'selesai')");
            $revisionNeeded = $letterModel->count("status = 'perlu_revisi'");
        }

        // Ambil data 3 DPK aktif dan statistik surat masing-masing
        $allDpk = $komisariatModel->where('code', 'KOM-BESAR', '!=');
        $dpkStats = [];
        foreach ($allDpk as $dpk) {
            $dId = (int)$dpk['id'];
            $dpkStats[] = [
                'dpk' => $dpk,
                'total' => $letterModel->count("komisariat_id = {$dId}"),
                'pending' => $letterModel->count("komisariat_id = {$dId} AND status IN ('diajukan', 'diverifikasi')"),
                'approved' => $letterModel->count("komisariat_id = {$dId} AND status IN ('disetujui', 'selesai')"),
            ];
        }

        // Surat terbaru
        $recentLetters = $letterModel->getFiltered($filters, 8, 0);

        View::render('dashboard.index', [
            'title' => 'Dashboard Administrasi - SIMAS GMNI UNISDA',
            'user' => $user,
            'stats' => [
                'total' => $totalLetters,
                'pending' => $pendingReview,
                'approved' => $approvedLetters,
                'revision' => $revisionNeeded,
            ],
            'dpkStats' => $dpkStats,
            'recentLetters' => $recentLetters
        ]);
    }
}

