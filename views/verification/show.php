<?php
// views/verification/show.php
use App\Helpers\View;
use App\Config\App;
?>

<div class="space-y-6 text-slate-800 text-center py-4">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 text-amber-700 text-2xl font-black mb-1 border-2 border-amber-300 shadow-sm">
        ℹ
    </div>
    <span class="inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold uppercase tracking-wider mb-1">
        Fitur Dinonaktifkan Sementara
    </span>
    <h1 class="text-xl font-black text-slate-900">Verifikasi Scan Surat Sedang Dinonaktifkan</h1>
    <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
        Fitur pemindaian QR Code dan verifikasi publik saat ini dinonaktifkan sementara karena sistem sedang difokuskan penuh untuk alur <strong>Pengiriman Surat</strong> dan <strong>Pengarsipan Digital</strong> di lingkungan GMNI UNISDA.
    </p>

    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-xs text-slate-600 max-w-sm mx-auto">
        Untuk memeriksa arsip surat atau status pengajuan, silakan masuk ke portal SIMAS.
    </div>

    <div class="pt-2">
        <a href="<?= App::baseUrl('login') ?>" class="inline-flex items-center px-5 py-2.5 bg-[#8B0000] hover:bg-[#6B0000] text-white text-xs font-bold rounded-xl shadow transition">
            &larr; Masuk ke Portal Administrasi SIMAS
        </a>
    </div>
</div>

