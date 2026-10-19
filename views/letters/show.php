<?php
// views/letters/show.php
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;

$statusSteps = ['diajukan', 'diverifikasi', 'disetujui', 'selesai'];
$currentStatus = $letter['status'];
?>

<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="<?= App::baseUrl('letters') ?>" class="text-xs text-[#8B0000] font-bold hover:underline flex items-center mb-1">
                &larr; Kembali ke Arsip Surat
            </a>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                    <?= View::e($letter['tracking_code']) ?>
                </h1>
                <?= View::statusBadge($letter['status']) ?>
            </div>
        </div>

        <div class="flex items-center space-x-2 flex-wrap gap-2">
            <!-- Edit button — hanya jika status belum disahkan -->
            <?php if (!in_array($letter['status'], ['disetujui', 'selesai']) && (Auth::isKomisariatBesar() || (Auth::isDpk() && (int)($letter['komisariat_id'] ?? 0) === Auth::dpkId()))): ?>
                <a href="<?= App::baseUrl('letters/edit?id=' . $letter['id']) ?>"
                   class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs rounded-xl transition flex items-center space-x-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Perbaiki / Edit</span>
                </a>
            <?php endif; ?>

            <!-- Unduh Berkas Surat Lampiran -->
            <?php if (!empty($letter['attachment_path'])): ?>
                <a href="<?= App::baseUrl($letter['attachment_path']) ?>" target="_blank" download
                   class="px-4 py-2 bg-[#8B0000] hover:bg-[#6B0000] text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Unduh Berkas Surat</span>
                </a>
            <?php endif; ?>

            <!-- Delete button hanya untuk Komisariat Besar atau DPK pemilik yang belum disahkan -->
            <?php if (!in_array($letter['status'], ['disetujui', 'selesai']) && (Auth::isKomisariatBesar() || (Auth::isDpk() && (int)($letter['komisariat_id'] ?? 0) === Auth::dpkId()))): ?>
                <form action="<?= App::baseUrl('letters/delete') ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus surat ini? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" value="<?= $letter['id'] ?>">
                    <button type="submit" class="px-3.5 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold text-xs rounded-xl transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Hapus</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tracking Progress Bar (Alur Pengiriman Surat) -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Pelacakan Alur Pengiriman & Verifikasi Surat</h3>
        
        <div class="grid grid-cols-4 gap-2 relative">
            <!-- Step 1: Diajukan -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['diajukan', 'diverifikasi', 'disetujui', 'selesai']) ? 'bg-[#8B0000] text-white shadow-md' : 'bg-slate-100 text-slate-400' ?>">
                    1
                </div>
                <span class="block text-xs font-bold text-slate-800">Diajukan / Dikirim</span>
                <span class="block text-[10px] text-slate-400">Berkas masuk sistem</span>
            </div>

            <!-- Step 2: Diverifikasi -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['diverifikasi', 'disetujui', 'selesai']) ? 'bg-blue-600 text-white shadow-md' : ($currentStatus === 'perlu_revisi' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-400') ?>">
                    2
                </div>
                <span class="block text-xs font-bold text-slate-800">Verifikasi Sekretariat</span>
                <span class="block text-[10px] text-slate-400">Pemeriksaan & No. Surat</span>
            </div>

            <!-- Step 3: Disahkan / Disetujui -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['disetujui', 'selesai']) ? 'bg-emerald-600 text-white shadow-md' : ($currentStatus === 'ditolak' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-400') ?>">
                    3
                </div>
                <span class="block text-xs font-bold text-slate-800">Persetujuan Ketua</span>
                <span class="block text-[10px] text-slate-400">Pengesahan & Disposisi</span>
            </div>

            <!-- Step 4: Selesai / Arsip -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['disetujui', 'selesai']) ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-100 text-slate-400' ?>">
                    4
                </div>
                <span class="block text-xs font-bold text-slate-800">Terdokumentasi</span>
                <span class="block text-[10px] text-slate-400">Arsip Resmi SIMAS</span>
            </div>
        </div>

        <?php if ($letter['status'] === 'perlu_revisi'): ?>
            <div class="mt-5 p-4 rounded-xl bg-orange-50 border border-orange-200 text-orange-900 text-xs">
                <span class="font-bold text-sm block mb-1">⚠ Catatan Revisi dari Sekretariat Besar:</span>
                <p><?= nl2br(View::e($letter['revision_notes'] ?? 'Harap melengkapi dokumen lampiran sesuai format administrasi organisasi.')) ?></p>
            </div>
        <?php elseif ($letter['status'] === 'ditolak'): ?>
            <div class="mt-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 text-xs">
                <span class="font-bold text-sm block mb-1">✕ Pengajuan Surat Ditolak:</span>
                <p><?= nl2br(View::e($letter['revision_notes'] ?? 'Pengajuan ditolak oleh pimpinan.')) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Action Desk for Komisariat Besar (Secretary & Chairman) -->
    <?php if (Auth::isKomisariatBesar()): ?>
        <div class="bg-white rounded-2xl p-6 border-2 border-red-200 shadow-sm space-y-4" x-data="{ openAction: '' }">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-[#8B0000]"></span>
                        <span>Meja Tindakan Pengurus Komisariat Besar GMNI UNISDA</span>
                    </h3>
                    <p class="text-xs text-slate-500">Pilih tindakan untuk memproses surat pengajuan.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <!-- Action 1: Sekretaris Verifikasi & Terbitkan Nomor Surat -->
                <?php if (in_array($letter['status'], ['diajukan', 'perlu_revisi']) && (Auth::isSekretarisBesar() || Auth::isKetuaBesar())): ?>
                    <button @click="openAction = 'verify'" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        ✓ Verifikasi & Alokasikan No. Surat
                    </button>
                    <button @click="openAction = 'revision'" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        ✎ Minta Revisi Berkas ke DPK
                    </button>
                <?php endif; ?>

                <!-- Action 2: Ketua Besar - Pengesahan & Disposisi -->
                <?php if (in_array($letter['status'], ['diverifikasi', 'diajukan']) && Auth::isKetuaBesar()): ?>
                    <button @click="openAction = 'approve'" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        ★ Sahkan & Buat Disposisi
                    </button>
                    <button @click="openAction = 'reject'" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        ✕ Tolak Pengajuan
                    </button>
                <?php endif; ?>

                <!-- Pesan jika tidak ada aksi tersedia (surat sudah final) -->
                <?php if (in_array($letter['status'], ['disetujui', 'selesai', 'ditolak'])): ?>
                    <div class="text-xs text-slate-500 italic">
                        Surat ini sudah dalam status akhir (<strong><?= strtoupper($letter['status']) ?></strong>). Tidak ada tindakan lebih lanjut yang diperlukan.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Form 1: Verifikasi Sekretaris -->
            <div x-show="openAction === 'verify'" class="pt-4 border-t border-slate-100" x-cloak>
                <form action="<?= App::baseUrl('approvals/process') ?>" method="POST" class="space-y-3 bg-blue-50/60 p-4 rounded-xl border border-blue-200">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="letter_id" value="<?= $letter['id'] ?>">
                    <input type="hidden" name="action" value="verify">

                    <h4 class="text-xs font-bold text-blue-900 uppercase">Verifikasi Berkas & Alokasikan Nomor Surat Resmi</h4>
                    <p class="text-xs text-blue-800">Periksa kelengkapan administrasi surat DPK dan tentukan nomor surat resmi organisasi.</p>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Surat Resmi Organisasi:</label>
                        <input type="text" name="reference_number" 
                               value="<?= View::e($letter['reference_number'] ?: (new \App\Models\Letter())->generateOfficialNumber($letter['category_id'], $letter['komisariat_id'], $letter['letter_date'])) ?>" 
                               required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Validasi Sekretariat (Opsional):</label>
                        <textarea name="notes" rows="2" placeholder="Catatan hasil verifikasi berkas..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="openAction = ''" class="px-3 py-1.5 text-xs text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg">Simpan & Teruskan ke Ketua</button>
                    </div>
                </form>
            </div>

            <!-- Form 2: Minta Revisi -->
            <div x-show="openAction === 'revision'" class="pt-4 border-t border-slate-100" x-cloak>
                <form action="<?= App::baseUrl('approvals/process') ?>" method="POST" class="space-y-3 bg-orange-50/60 p-4 rounded-xl border border-orange-200">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="letter_id" value="<?= $letter['id'] ?>">
                    <input type="hidden" name="action" value="revision">

                    <h4 class="text-xs font-bold text-orange-900 uppercase">Minta Perbaikan / Revisi Berkas ke DPK</h4>
                    <p class="text-xs text-orange-800">Tuliskan secara jelas dokumen atau bagian apa yang perlu disempurnakan oleh pengurus DPK.</p>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Uraikan Hal yang Perlu Diperbaiki:</label>
                        <textarea name="notes" required rows="3" placeholder="Contoh: Lampirkan susunan kepanitiaan lengkap dengan nomor registrasi kader / proposal mohon disesuaikan jadwalnya..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="openAction = ''" class="px-3 py-1.5 text-xs text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-orange-600 text-white text-xs font-bold rounded-lg">Kirim Catatan Revisi</button>
                    </div>
                </form>
            </div>

            <!-- Form 3: Persetujuan Ketua (Disposisi) -->
            <div x-show="openAction === 'approve'" class="pt-4 border-t border-slate-100" x-cloak>
                <form action="<?= App::baseUrl('approvals/process') ?>" method="POST" class="space-y-3 bg-emerald-50/60 p-4 rounded-xl border border-emerald-200">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="letter_id" value="<?= $letter['id'] ?>">
                    <input type="hidden" name="action" value="approve">

                    <h4 class="text-xs font-bold text-emerald-900 uppercase">Pengesahan Ketua Komisariat Besar & Lembar Disposisi</h4>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Instruksi Disposisi:</label>
                        <select name="instruction" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 font-medium">
                            <option value="Disetujui. Terbitkan Surat Rekomendasi Resmi.">Disetujui. Terbitkan Surat Rekomendasi Resmi.</option>
                            <option value="Disetujui. Sahkan SK Kepengurusan / Panitia.">Disetujui. Sahkan SK Kepengurusan / Panitia.</option>
                            <option value="Tindak lanjuti dan koordinasikan dengan pihak dekanat.">Tindak lanjuti dan koordinasikan dengan pihak dekanat.</option>
                            <option value="Dihadiri dan wakili Komisariat Besar UNISDA.">Dihadiri dan wakili Komisariat Besar UNISDA.</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Pimpinan (Opsional):</label>
                        <textarea name="notes" rows="2" placeholder="Pesan pengesahan pimpinan..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="openAction = ''" class="px-3 py-1.5 text-xs text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg">Sahkan & Setujui Sekarang</button>
                    </div>
                </form>
            </div>

            <!-- Form 4: Tolak -->
            <div x-show="openAction === 'reject'" class="pt-4 border-t border-slate-100" x-cloak>
                <form action="<?= App::baseUrl('approvals/process') ?>" method="POST" class="space-y-3 bg-red-50/60 p-4 rounded-xl border border-red-200">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="letter_id" value="<?= $letter['id'] ?>">
                    <input type="hidden" name="action" value="reject">

                    <h4 class="text-xs font-bold text-red-900 uppercase">Tolak Permohonan Surat</h4>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Alasan Penolakan:</label>
                        <textarea name="notes" required rows="2" placeholder="Jelaskan alasan mengapa surat tidak dapat disetujui..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="openAction = ''" class="px-3 py-1.5 text-xs text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg">Tolak Surat</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two-Column Layout: Details & Documents (Left) & Archive & Timeline (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Main Information & Uploaded Letter Document -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Berkas Dokumen Terlampir Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 flex items-center space-x-2">
                            <span class="text-base">📁</span>
                            <span>Berkas Dokumen Surat Fisik / Digital</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Dokumen resmi yang dilampirkan dalam pengiriman surat ini.</p>
                    </div>
                    <?php if (!empty($letter['attachment_path'])): ?>
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 text-[11px] font-bold rounded-lg border border-emerald-200">
                            ✓ Berkas Terlampir
                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[11px] font-semibold rounded-lg">
                            Tanpa Lampiran File
                        </span>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <?php if (!empty($letter['attachment_path'])): ?>
                        <?php 
                            $ext = strtolower(pathinfo($letter['attachment_path'], PATHINFO_EXTENSION));
                            $isPdf = $ext === 'pdf';
                            $isImg = in_array($ext, ['jpg', 'jpeg', 'png']);
                        ?>
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center space-x-3 overflow-hidden">
                                <div class="w-10 h-10 rounded-lg bg-red-100 text-[#8B0000] flex items-center justify-center font-bold text-sm shrink-0">
                                    <?= strtoupper($ext) ?>
                                </div>
                                <div class="truncate">
                                    <span class="text-xs font-bold text-slate-800 block truncate"><?= basename($letter['attachment_path']) ?></span>
                                    <span class="text-[11px] text-slate-400">Format: Dokumen <?= strtoupper($ext) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <a href="<?= App::baseUrl($letter['attachment_path']) ?>" target="_blank"
                                   class="px-3.5 py-2 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold rounded-lg transition shadow-sm">
                                    Lihat / Buka Berkas &rarr;
                                </a>
                                <a href="<?= App::baseUrl($letter['attachment_path']) ?>" download
                                   class="px-3.5 py-2 bg-[#8B0000] hover:bg-[#6B0000] text-white text-xs font-bold rounded-lg transition shadow-sm flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh</span>
                                </a>
                            </div>
                        </div>

                        <?php if ($isPdf): ?>
                            <div class="mt-4 border border-slate-200 rounded-xl overflow-hidden bg-slate-100">
                                <div class="px-4 py-2 bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-between">
                                    <span>Pratinjau Berkas PDF:</span>
                                    <a href="<?= App::baseUrl($letter['attachment_path']) ?>" target="_blank" class="text-[#8B0000] hover:underline">Buka di Tab Baru</a>
                                </div>
                                <iframe src="<?= App::baseUrl($letter['attachment_path']) ?>" class="w-full h-96 border-none"></iframe>
                            </div>
                        <?php elseif ($isImg): ?>
                            <div class="mt-4 border border-slate-200 rounded-xl overflow-hidden text-center p-3 bg-slate-100">
                                <img src="<?= App::baseUrl($letter['attachment_path']) ?>" alt="Pratinjau Berkas" class="max-h-96 mx-auto rounded-lg shadow-sm">
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <span class="text-2xl block mb-1">📄</span>
                            <span class="text-xs font-semibold text-slate-600 block">Surat ini tidak memiliki berkas dokumen terlampir.</span>
                            <span class="text-[11px] text-slate-400">Pengirim hanya mencatat metadata surat tanpa mengunggah file.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Data Pengiriman & Arsip Surat -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 pb-3 border-b border-slate-100 flex items-center space-x-2">
                        <span class="text-base">📋</span>
                        <span>Informasi & Rincian Pengarsipan Surat</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Nomor Surat Resmi</span>
                        <span class="font-mono text-sm font-bold text-slate-900">
                            <?= View::e($letter['reference_number'] ?: '(Menunggu Penomoran Resmi)') ?>
                        </span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Klasifikasi & Kategori</span>
                        <span class="font-bold text-slate-800">
                            [<?= View::e($letter['category_code']) ?>] <?= View::e($letter['category_name']) ?>
                        </span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Arah Surat</span>
                        <span class="font-bold <?= $letter['direction'] === 'masuk' ? 'text-blue-700' : 'text-emerald-700' ?>">
                            <?= $letter['direction'] === 'masuk' ? '📥 Surat Masuk' : '📤 Surat Keluar' ?>
                        </span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Tanggal Surat</span>
                        <span class="font-bold text-slate-800">
                            <?= View::formatDate($letter['letter_date']) ?>
                        </span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Asal Pengirim</span>
                        <span class="font-bold text-slate-800">
                            <?= View::e($letter['sender']) ?>
                        </span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Tujuan / Kepada</span>
                        <span class="font-bold text-slate-800">
                            <?= View::e($letter['recipient']) ?>
                        </span>
                    </div>
                </div>

                <div class="space-y-1 text-xs">
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Perihal / Judul Surat:</span>
                    <p class="text-sm font-bold text-slate-900 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <?= View::e($letter['title']) ?>
                    </p>
                </div>

                <div class="space-y-1 text-xs">
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Ringkasan Isi / Catatan Pengantar:</span>
                    <div class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed">
                        <?= nl2br(View::e($letter['description'] ?: 'Tidak ada ringkasan atau catatan pengantar tambahan.')) ?>
                    </div>
                </div>

                <!-- Petugas Administrasi / Pengesahan -->
                <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Diajukan / Dicatat Oleh:</span>
                        <span class="font-bold text-slate-800 block mt-0.5"><?= View::e($letter['creator_name'] ?? 'Pengurus DPK') ?></span>
                        <span class="text-[10px] text-slate-400"><?= View::formatDateTime($letter['created_at']) ?></span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Status Pengesahan Pimpinan:</span>
                        <?php if (in_array($letter['status'], ['disetujui', 'selesai'])): ?>
                            <span class="font-bold text-emerald-700 block mt-0.5">Disahkan oleh <?= View::e($letter['approver_name'] ?? 'Ketua Komisariat Besar') ?></span>
                            <span class="text-[10px] text-slate-400"><?= !empty($letter['approved_at']) ? View::formatDateTime($letter['approved_at']) : 'Disetujui' ?></span>
                        <?php else: ?>
                            <span class="text-slate-500 italic block mt-0.5">Menunggu Pengesahan Pimpinan</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Disposisi History Card -->
            <?php if (!empty($dispositions)): ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Lembar Disposisi Resmi</h3>
                    <div class="space-y-3">
                        <?php foreach ($dispositions as $disp): ?>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900">Dari: <?= View::e($disp['from_user_name']) ?></span>
                                    <span class="text-slate-400"><?= View::formatDateTime($disp['created_at']) ?></span>
                                </div>
                                <div class="text-slate-800 font-semibold pt-1">
                                    Instruksi: <span class="text-[#8B0000]"><?= View::e($disp['instruction']) ?></span>
                                </div>
                                <?php if (!empty($disp['notes'])): ?>
                                    <div class="text-slate-600 mt-1"><?= nl2br(View::e($disp['notes'])) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Col: Informasi Pengarsipan & Timeline -->
        <div class="space-y-6">
            
            <!-- Info Arsip Box -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Arsip SIMAS</h3>
                
                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kode Tracking:</span>
                        <span class="font-mono font-bold text-slate-800 text-sm"><?= View::e($letter['tracking_code']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Status Pengarsipan:</span>
                        <span class="font-bold text-slate-800 capitalize"><?= View::e($letter['status']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Waktu Pencatatan:</span>
                        <span class="text-slate-600"><?= View::formatDateTime($letter['created_at']) ?></span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 text-[11px] text-amber-800 bg-amber-50/70 p-3 rounded-xl border border-amber-200/60 leading-relaxed">
                    💡 <strong>Catatan Sistem:</strong><br>
                    Fitur pembuatan template surat dan scan verifikasi QR dinonaktifkan sementara untuk perancangan ulang. Sistem beroperasi penuh untuk <strong>pengiriman surat</strong> dan <strong>pengarsipan digital</strong>.
                </div>
            </div>

            <!-- Letter Timeline & Audit Trail -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Riwayat Jejak Surat (Audit Trail)</h3>
                
                <div class="relative pl-4 space-y-4 before:absolute before:left-1.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 text-xs">
                    <?php foreach ($timeline as $t): ?>
                        <div class="relative">
                            <div class="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full bg-[#8B0000] border-2 border-white ring-2 ring-red-100"></div>
                            <div>
                                <span class="font-bold text-slate-900 block"><?= View::e($t['action']) ?></span>
                                <span class="text-[10px] text-slate-400 block"><?= View::formatDateTime($t['created_at']) ?> &bull; <?= View::e($t['user_name'] ?? 'Sistem') ?></span>
                                <?php if (!empty($t['notes'])): ?>
                                    <p class="text-slate-600 mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100 text-[11px]">
                                        <?= nl2br(View::e($t['notes'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>
