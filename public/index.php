<?php
// public/index.php
declare(strict_types=1);

// Set timezone to Western Indonesia Time (WIB)
date_default_timezone_set('Asia/Jakarta');

// Autoloader for App\ namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Config\App;
use App\Database\Migrations;
use App\Database\Seeders;
use App\Helpers\Auth;

// Load Environment variables
App::loadEnv(__DIR__ . '/../.env');

// Ensure Database and Seeders are initialized
try {
    Migrations::run();
    Seeders::run();
} catch (\Throwable $e) {
    die("Kesalahan Inisialisasi Database: " . $e->getMessage());
}

// Start Session
Auth::init();

// Simple Request Router
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Clean leading/trailing slashes
$path = trim($uri, '/');

// Base path offset if running inside a subfolder
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (str_ends_with($scriptName, '.php')) {
    $scriptDir = dirname($scriptName);
    if ($scriptDir !== '/' && $scriptDir !== '\\' && str_starts_with($path, trim($scriptDir, '/'))) {
        $path = trim(substr($path, strlen(trim($scriptDir, '/'))), '/');
    }
}

// Remove index.php prefix if present (for servers without mod_rewrite)
if (str_starts_with($path, 'index.php/')) {
    $path = trim(substr($path, 10), '/');
} elseif ($path === 'index.php') {
    $path = '';
}

// Route mapping
switch ($path) {
    case '':
    case 'dashboard':
        (new \App\Controllers\DashboardController())->index();
        break;

    case 'login':
        if ($method === 'POST') {
            (new \App\Controllers\AuthController())->login();
        } else {
            (new \App\Controllers\AuthController())->showLogin();
        }
        break;

    case 'logout':
        (new \App\Controllers\AuthController())->logout();
        break;

    case 'auth/quick':
        (new \App\Controllers\AuthController())->quickLogin();
        break;

    case 'letters':
        (new \App\Controllers\LetterController())->index();
        break;

    case 'letters/create':
        (new \App\Controllers\LetterController())->create();
        break;

    case 'letters/store':
        if ($method === 'POST') {
            (new \App\Controllers\LetterController())->store();
        } else {
            header('Location: ' . App::baseUrl('letters/create'));
        }
        break;

    case 'letters/show':
        (new \App\Controllers\LetterController())->show();
        break;

    case 'letters/edit':
        (new \App\Controllers\LetterController())->edit();
        break;

    case 'letters/update':
        if ($method === 'POST') {
            (new \App\Controllers\LetterController())->update();
        } else {
            header('Location: ' . App::baseUrl('letters'));
        }
        break;

    case 'letters/delete':
        if ($method === 'POST') {
            (new \App\Controllers\LetterController())->delete();
        } else {
            header('Location: ' . App::baseUrl('letters'));
        }
        break;

    case 'letters/print':
        (new \App\Controllers\LetterController())->print();
        break;

    case 'approvals':
        (new \App\Controllers\ApprovalController())->index();
        break;

    case 'approvals/process':
        if ($method === 'POST') {
            (new \App\Controllers\ApprovalController())->process();
        } else {
            header('Location: ' . App::baseUrl('approvals'));
        }
        break;

    case 'agenda':
        (new \App\Controllers\AgendaController())->index();
        break;

    case 'agenda/print':
        (new \App\Controllers\AgendaController())->print();
        break;

    case 'dpk':
        if ($method === 'POST') {
            (new \App\Controllers\DpkController())->store();
        } else {
            (new \App\Controllers\DpkController())->index();
        }
        break;

    case 'verifikasi':
        (new \App\Controllers\VerificationController())->show();
        break;

    default:
        var_dump($path, $uri, $scriptName, $_SERVER["SCRIPT_NAME"]);
        http_response_code(404);
        echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'><h1>404 - Halaman Tidak Ditemukan</h1><p>Halaman yang Anda tuju tidak tersedia.</p><a href='" . App::baseUrl('') . "'>Kembali ke Dashboard</a></div>";
        break;
}
