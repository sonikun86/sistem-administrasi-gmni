<?php
// Controllers/AuthController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            header('Location: ' . App::baseUrl('dashboard'));
            exit;
        }

        View::render('auth.login', [
            'title' => 'Masuk - SIMAS GMNI UNISDA'
        ], 'auth');
    }

    public function login(): void
    {
        Csrf::verify();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            View::flash('error', 'Email dan password wajib diisi.');
            header('Location: ' . App::baseUrl('login'));
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            View::flash('error', 'Email atau password yang Anda masukkan salah.');
            header('Location: ' . App::baseUrl('login'));
            exit;
        }

        if (empty($user['is_active'])) {
            View::flash('error', 'Akun Anda dinonaktifkan oleh administrator.');
            header('Location: ' . App::baseUrl('login'));
            exit;
        }

        // Ambil data detail beserta komisariat
        $fullUser = $userModel->findWithKomisariat((int)$user['id']);
        Auth::login($fullUser ?: $user);

        View::flash('success', 'Selamat datang kembali, ' . ($user['name'] ?? 'Kamerad') . '!');
        header('Location: ' . App::baseUrl('dashboard'));
        exit;
    }

    public function quickLogin(): void
    {
        // Demo switcher for testing convenience
        $role = $_GET['role'] ?? 'ketua_besar';
        $userModel = new User();

        $email = match ($role) {
            'ketua_besar' => 'ketua.besar@gmni-unisda.or.id',
            'sekretaris_besar' => 'sekretaris.besar@gmni-unisda.or.id',
            'dpk_hatta' => 'dpk.hatta@gmni-unisda.or.id',
            'dpk_ficus' => 'dpk.ficus@gmni-unisda.or.id',
            'dpk_brahmana' => 'dpk.brahmana@gmni-unisda.or.id',
            default => 'ketua.besar@gmni-unisda.or.id'
        };

        $user = $userModel->findByEmail($email);
        if ($user) {
            $fullUser = $userModel->findWithKomisariat((int)$user['id']);
            Auth::login($fullUser ?: $user);
            View::flash('success', 'Beralih ke akun: ' . $user['name']);
        }

        header('Location: ' . App::baseUrl('dashboard'));
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . App::baseUrl('login'));
        exit;
    }
}

