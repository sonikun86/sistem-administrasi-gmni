<?php
// views/verification/show.php
use App\Helpers\View;
use App\Config\App;
?>

<div class="space-y-6 text-slate-800">
    <?php if ($letter): ?>
        <!-- Valid Document Badge -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 text-3xl font-black mb-3 border-2 border-emerald-300 shadow-sm">
                ✓
            </div>
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-black uppercase tracking-wider mb-1">
                Dokumen Resmi Terverifikasi
            </span>
            <h1 class="text-xl font-black text-slate-900">Keaslian Surat Terkonfirmasi</h1>
            <p class="text-xs text-slate-500 mt-1">
                Surat ini tercatat secara sah dan resmi pada database administrasi Komisariat Besar GMNI UNISDA.
            </p>
        </div>

        <!-- Detail Certificate Table -->
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 text-xs space-y-3">
            <div class="pb-2 border-b border-slate-200">
                <span class="text-slate-400 block font-semibold text-[10px] uppercase">Nomor Surat Resmi</span>
                <span class="font-mono text-sm font-black text-[#8B0000]"><?= View::e($letter['reference_number'] ?: $letter['tracking_code']) ?></span>
            </div>

            <div>
                <span class="text-slate-400 block font-semibold text-[10px] uppercase">Perihal / Judul Surat</span>
                <span class="font-bold text-slate-900 text-sm"><?= View::e($letter['title']) ?></span>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200">
                <div>
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Pengirim</span>
                    <span class="font-bold text-slate-800"><?= View::e($letter['sender']) ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Tujuan / Kepada</span>
                    <span class="font-bold text-slate-800"><?= View::e($letter['recipient']) ?></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200">
                <div>
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Tanggal Surat</span>
                    <span class="font-medium text-slate-800"><?= View::formatDate($letter['letter_date']) ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Status Administrasi</span>
                    <span class="font-bold text-emerald-700"><?= strtoupper($letter['status']) ?></span>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-200">
                <span class="text-slate-400 block font-semibold text-[10px] uppercase">Mengesahkan Secara Digital</span>
                <span class="font-bold text-slate-900"><?= View::e($letter['approver_name'] ?? 'Ketua Komisariat Besar GMNI UNISDA') ?></span>
                <span class="text-[10px] text-slate-500 block">Dewan Pimpinan Komisariat Besar GMNI UNISDA</span>
            </div>
        </div>

        <?php if (!empty($letter['attachment_path'])): ?>
            <div class="pt-2">
                <a href="<?= App::baseUrl($letter['attachment_path']) ?>" target="_blank" 
                   class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Unduh Berkas Lampiran Resmi</span>
                </a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Invalid Document -->
        <div class="text-center py-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 text-3xl font-black mb-3 border-2 border-red-300">
                ✕
            </div>
            <h2 class="text-lg font-black text-slate-900">Dokumen Tidak Ditemukan / Tidak Sah</h2>
            <p class="text-xs text-slate-500 mt-2">
                Token verifikasi tidak terdaftar pada sistem administrasi GMNI UNISDA. Pastikan Anda memindai kode QR dari surat resmi yang dikeluarkan oleh Komisariat Besar GMNI UNISDA.
            </p>
        </div>
    <?php endif; ?>

    <div class="text-center pt-4 border-t border-slate-100">
        <a href="<?= App::baseUrl('login') ?>" class="text-xs font-bold text-[#8B0000] hover:underline">
            &larr; Masuk ke Portal Administrasi SIMAS
        </a>
    </div>
</div>

