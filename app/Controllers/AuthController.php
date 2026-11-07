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
        // Fitur quick login telah dinonaktifkan demi keamanan akun pimpinan
        View::flash('warning', 'Fitur beralih cepat telah dinonaktifkan demi keamanan akun organisasi.');
        header('Location: ' . App::baseUrl('login'));
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . App::baseUrl('login'));
        exit;
    }
}

