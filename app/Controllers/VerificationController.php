<?php
// Controllers/VerificationController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Letter;
use App\Helpers\View;

class VerificationController
{
    public function show(): void
    {
        $token = trim($_GET['token'] ?? '');
        $letter = null;

        if ($token !== '') {
            $letterModel = new Letter();
            $letter = $letterModel->findByVerificationToken($token);
        }

        View::render('verification.show', [
            'title' => 'Verifikasi Keaslian Surat - GMNI UNISDA',
            'letter' => $letter,
            'token' => $token
        ], 'auth');
    }
}

