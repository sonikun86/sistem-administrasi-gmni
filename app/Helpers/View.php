<?php
// Helpers/View.php
declare(strict_types=1);

namespace App\Helpers;

use App\Config\App;

class View
{
    public static function render(string $viewPath, array $data = [], ?string $layout = 'main'): void
    {
        extract($data);
        
        $viewFile = __DIR__ . '/../../views/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($viewFile)) {
            die("View template tidak ditemukan: " . htmlspecialchars($viewFile));
        }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = __DIR__ . '/../../views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            die("Layout template tidak ditemukan: " . htmlspecialchars($layoutFile));
        }

        include $layoutFile;
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public static function url(string $path = ''): string
    {
        return App::baseUrl($path);
    }

    public static function formatDate(?string $dateString): string
    {
        if (!$dateString) {
            return '-';
        }
        $timestamp = strtotime($dateString);
        if (!$timestamp) {
            return $dateString;
        }

        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $day = date('j', $timestamp);
        $month = $months[(int)date('n', $timestamp)];
        $year = date('Y', $timestamp);

        return "$day $month $year";
    }

    public static function formatDateTime(?string $dateString): string
    {
        if (!$dateString) {
            return '-';
        }
        $timestamp = strtotime($dateString);
        if (!$timestamp) {
            return $dateString;
        }

        return self::formatDate($dateString) . ' ' . date('H:i', $timestamp) . ' WIB';
    }

    public static function statusBadge(string $status): string
    {
        return match ($status) {
            'draft' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">Draf</span>',
            'diajukan' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">🟡 Diajukan</span>',
            'diverifikasi' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">🔵 Diverifikasi</span>',
            'perlu_revisi' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">🟠 Perlu Revisi</span>',
            'disetujui' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">🟢 Disetujui</span>',
            'ditolak' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">🔴 Ditolak</span>',
            'selesai' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">✓ Selesai & Terbit</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' . htmlspecialchars(ucfirst($status)) . '</span>'
        };
    }

    public static function flash(string $type, string $message): void
    {
        Auth::init();
        $_SESSION['flashes'][] = [
            'type' => $type, // success, error, warning, info
            'message' => $message
        ];
    }

    public static function getFlashes(): array
    {
        Auth::init();
        $flashes = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);
        return $flashes;
    }
}

