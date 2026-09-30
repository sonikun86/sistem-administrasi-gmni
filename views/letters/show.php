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

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center space-x-1.5">
        <div class="flex items-center space-x-2 flex-wrap gap-2">
            <!-- Edit button — hanya jika status belum disahkan -->
            <?php if (!in_array($letter['status'], ['disetujui', 'selesai']) && (Auth::isKomisariatBesar() || (Auth::isDpk() && (int)($letter['komisariat_id'] ?? 0) === Auth::dpkId()))): ?>
                <a href="<?= App::baseUrl('letters/edit?id=' . $letter['id']) ?>"
                   class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs rounded-xl transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Perbaiki/Edit</span>
                </a>
            <?php endif; ?>
            <!-- Print link — buka halaman cetak resmi ber-kop surat GMNI -->
            <a href="<?= App::baseUrl('letters/print?id=' . $letter['id']) ?>" target="_blank"
               class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Dokumen</span>
            </button>
                <span>Cetak Surat Resmi</span>
            </a>
            <?php if (!empty($letter['attachment_path'])): ?>
                <a href="<?= App::baseUrl($letter['attachment_path']) ?>" target="_blank" 
                   class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Unduh Berkas Lampiran</span>
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

    <!-- Tracking Progress Bar (Mencegah Pertemuan Tatap Muka Fisik Berulang Kali) -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Pelacakan Status Alur Surat (Digital Workflow)</h3>
        
        <div class="grid grid-cols-4 gap-2 relative">
            <!-- Step 1: Diajukan -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['diajukan', 'diverifikasi', 'disetujui', 'selesai']) ? 'bg-[#8B0000] text-white shadow-md' : 'bg-slate-100 text-slate-400' ?>">
                    1
                </div>
                <span class="block text-xs font-bold text-slate-800">Diajukan DPK</span>
                <span class="block text-[10px] text-slate-400">Berkas masuk sistem</span>
            </div>

            <!-- Step 2: Diverifikasi -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['diverifikasi', 'disetujui', 'selesai']) ? 'bg-blue-600 text-white shadow-md' : ($currentStatus === 'perlu_revisi' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-400') ?>">
                    2
                </div>
                <span class="block text-xs font-bold text-slate-800">Verifikasi Sekretariat</span>
                <span class="block text-[10px] text-slate-400">Cek berkas & nomor surat</span>
            </div>

            <!-- Step 3: Disahkan / Disetujui -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['disetujui', 'selesai']) ? 'bg-emerald-600 text-white shadow-md' : ($currentStatus === 'ditolak' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-400') ?>">
                    3
                </div>
                <span class="block text-xs font-bold text-slate-800">Persetujuan Ketua</span>
                <span class="block text-[10px] text-slate-400">Pengesahan & disposisi</span>
            </div>

            <!-- Step 4: Terbit & QR Code -->
            <div class="text-center">
                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2 transition <?= in_array($currentStatus, ['disetujui', 'selesai']) ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-100 text-slate-400' ?>">
                    4
                </div>
                <span class="block text-xs font-bold text-slate-800">Terbit Resmi</span>
                <span class="block text-[10px] text-slate-400">Siap unduh & QR valid</span>
            </div>
        </div>

        <?php if ($letter['status'] === 'perlu_revisi'): ?>
            <div class="mt-5 p-4 rounded-xl bg-orange-50 border border-orange-200 text-orange-900 text-xs">
                <span class="font-bold text-sm block mb-1">⚠ Catatan Revisi dari Sekretariat Besar:</span>
                <p><?= nl2br(View::e($letter['revision_notes'] ?? 'Harap melengkapi dokumen lampiran sesuai format administrasi organisasi.')) ?></p>
            </div>
        <?php elseif ($letter['status'] === 'ditolak'): ?>
            <div class="mt-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 text-xs">
                <span class="font-bold text-sm block mb-1">✕ Surat Ditolak:</span>
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
                    <p class="text-xs text-slate-500">Pilih tindakan untuk mempercepat proses birokrasi surat tanpa tatap muka.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <!-- Action 1: Sekretaris Verifikasi & Terbitkan Nomor Surat -->
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
                        ★ Setujui & Sahkan Surat Resmi
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

            <!-- Dynamic Form Modals based on clicked action -->
            <!-- Form 1: Verifikasi Sekretaris -->
            <div x-show="openAction === 'verify'" class="pt-4 border-t border-slate-100" x-cloak>
                <form action="<?= App::baseUrl('approvals/process') ?>" method="POST" class="space-y-3 bg-blue-50/60 p-4 rounded-xl border border-blue-200">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="letter_id" value="<?= $letter['id'] ?>">
                    <input type="hidden" name="action" value="verify">

                    <h4 class="text-xs font-bold text-blue-900 uppercase">Verifikasi Administrasi & Penomoran Resmi</h4>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Surat Resmi (Otomatis / Kustom):</label>
                        <input type="text" name="reference_number" value="<?= View::e($letter['reference_number'] ?: '') ?>" 
                               placeholder="Biarkan kosong untuk otomatisasi nomor sesuai standar GMNI UNISDA"
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Validasi Sekretaris:</label>
                        <textarea name="notes" rows="2" placeholder="Catatan berkas telah lengkap dan sesuai aturan organisasi..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="openAction = ''" class="px-3 py-1.5 text-xs text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg">Kirim Verifikasi</button>
                    </div>
                </form>
            </div>

            <!-- Form 2: Minta Revisi -->
            <div x-show="openAction === 'revision'" class="pt-4 border-t border-slate-100" x-cloak>
                <form action="<?= App::baseUrl('approvals/process') ?>" method="POST" class="space-y-3 bg-orange-50/60 p-4 rounded-xl border border-orange-200">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="letter_id" value="<?= $letter['id'] ?>">
                    <input type="hidden" name="action" value="request_revision">

                    <h4 class="text-xs font-bold text-orange-900 uppercase">Instruksi Revisi untuk Pengurus DPK</h4>
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

    <!-- Two-Column Layout: Official Document Preview (Left) & QR/Verification/Timeline (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Official Letter Card / Kop Surat GMNI UNISDA -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 print:border-none print:shadow-none print:p-0">
                
                <!-- Kop Surat GMNI UNISDA -->
                <div class="border-b-4 border-double border-slate-900 pb-4 mb-6 text-center">
                    <div class="flex items-center justify-center space-x-3 mb-1">
                        <div class="w-12 h-12 rounded-full bg-[#8B0000] text-white flex items-center justify-center font-black text-2xl border-2 border-[#D4AF37]">
                            ★
                        </div>
                        <div>
                            <h2 class="text-lg font-black tracking-wider text-slate-900 uppercase">
                                GERAKAN MAHASISWA NASIONAL INDONESIA
                            </h2>
                            <h3 class="text-base font-extrabold text-[#8B0000] tracking-wide uppercase">
                                KOMISARIAT BESAR GMNI UNISDA LAMONGAN
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium">
                                Universitas Islam Darul 'Ulum Lamongan &bull; Jawa Timur
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Letter Header Data -->
                <div class="grid grid-cols-2 text-xs mb-6 gap-2">
                    <div class="space-y-1">
                        <div><span class="text-slate-400 font-semibold">Nomor Surat:</span> <span class="font-mono font-bold text-slate-900"><?= View::e($letter['reference_number'] ?: '(Menunggu Penomoran Resmi)') ?></span></div>
                        <div><span class="text-slate-400 font-semibold">Lampiran:</span> <span class="text-slate-800 font-medium"><?= !empty($letter['attachment_path']) ? '1 (Satu) Berkas Terlampir' : '-' ?></span></div>
                        <div><span class="text-slate-400 font-semibold">Perihal:</span> <span class="font-bold text-slate-900"><?= View::e($letter['title']) ?></span></div>
                    </div>
                    <div class="text-right space-y-1">
                        <div><span class="text-slate-400 font-semibold">Lamongan,</span> <span class="font-medium text-slate-800"><?= View::formatDate($letter['letter_date']) ?></span></div>
                        <div><span class="text-slate-400 font-semibold">Pengirim:</span> <span class="font-bold text-slate-900"><?= View::e($letter['sender']) ?></span></div>
                        <div><span class="text-slate-400 font-semibold">Kepada Yth:</span> <span class="font-bold text-slate-900"><?= View::e($letter['recipient']) ?></span></div>
                    </div>
                </div>

                <!-- Letter Body Standard GMNI -->
                <div class="text-xs space-y-4 text-slate-800 leading-relaxed border-t border-slate-100 pt-4">
                    <p class="font-bold text-[#8B0000] text-sm italic">
                        Merdeka!
                    </p>
                    
                    <p>
                        Salam pejuang pemikir - pemikir pejuang. Teriring doa semoga seluruh jajaran kader Gerakan Mahasiswa Nasional Indonesia senantiasa dalam lindungan Tuhan Yang Maha Esa dan konsisten dalam garis perjuangan kaum Marhaen.
                    </p>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <p class="font-semibold text-slate-900 mb-1">Maksud & Keterangan Dokumen:</p>
                        <p class="text-slate-700"><?= nl2br(View::e($letter['description'] ?: 'Tidak ada keterangan tambahan.')) ?></p>
                    </div>

                    <p>
                        Demikian surat ini kami sampaikan secara terpusat melalui Sistem Informasi Manajemen Administrasi Surat (SIMAS) Komisariat Besar GMNI UNISDA. Atas perhatian, kerja sama, dan koordinasi segenap pihak, kami ucapkan terima kasih.
                    </p>

                    <p class="font-bold text-[#8B0000] text-sm italic">
                        GMNI Jaya! Marhaen Menang!
                    </p>
                </div>

                <!-- Digital Signatures & Seal Section -->
                <div class="mt-8 pt-6 border-t border-slate-200 grid grid-cols-2 gap-4 text-xs text-center">
                    <div>
                        <p class="text-slate-500 font-semibold">Mengetahui / Mengajukan,</p>
                        <p class="font-bold text-slate-800 mt-1"><?= View::e($letter['sender']) ?></p>
                        <div class="h-16 flex items-center justify-center">
                            <span class="px-3 py-1 rounded bg-slate-100 text-slate-500 text-[10px] font-medium border border-slate-200">
                                Terverifikasi Sistem
                            </span>
                        </div>
                        <p class="font-bold text-slate-900 underline"><?= View::e($letter['creator_name'] ?? 'Pengurus DPK') ?></p>
                    </div>

                    <div>
                        <p class="text-slate-500 font-semibold">Mengesahkan,</p>
                        <p class="font-bold text-slate-800 mt-1">Komisariat Besar GMNI UNISDA</p>
                        <div class="h-16 flex items-center justify-center">
                            <?php if (in_array($letter['status'], ['disetujui', 'selesai'])): ?>
                                <div class="px-3 py-1 rounded bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-300 flex items-center space-x-1">
                                    <span>★ DISAHKAN DIGITAL</span>
                                </div>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded bg-slate-100 text-slate-400 text-[10px] italic">
                                    Menunggu Tanda Tangan
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="font-bold text-slate-900 underline"><?= View::e($letter['approver_name'] ?? 'Ketua Komisariat Besar') ?></p>
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

        <!-- Right Col: QR Code Verification & Timeline -->
        <div class="space-y-6">
            
            <!-- QR Code Authenticity Seal -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center space-y-3">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Verifikasi Keaslian Dokumen</h3>
                
                <div class="flex justify-center my-2">
                    <?= $qrSvg ?>
                </div>

                <div class="text-xs text-slate-500 space-y-1">
                    <p class="font-mono text-[10px] text-slate-400 truncate">Token: <?= View::e($letter['verification_token']) ?></p>
                    <p class="text-[11px] text-slate-600">Scan QR Code ini menggunakan kamera smartphone untuk memvalidasi keaslian surat secara publik.</p>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <a href="<?= $verifUrl ?>" target="_blank" class="text-xs font-bold text-[#8B0000] hover:underline flex items-center justify-center space-x-1">
                        <span>Buka Tautan Verifikasi Publik</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
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

