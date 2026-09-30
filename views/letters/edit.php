<?php
// views/letters/edit.php
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="<?= App::baseUrl('letters/show?id=' . $letter['id']) ?>" class="text-xs text-[#8B0000] font-bold hover:underline flex items-center mb-1">
                &larr; Kembali ke Detail Surat
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Perbaiki Berkas & Perbarui Pengajuan Surat
            </h1>
            <p class="text-xs text-slate-500 mt-0.5 font-mono">
                Kode Tracking: <?= View::e($letter['tracking_code']) ?> &bull; Status Saat Ini: <?= strtoupper($letter['status']) ?>
            </p>
        </div>
    </div>

    <!-- Revision Notes Banner if status is perlu_revisi -->
    <?php if ($letter['status'] === 'perlu_revisi' && !empty($letter['revision_notes'])): ?>
        <div class="bg-orange-50 border-2 border-orange-300 rounded-2xl p-5 text-orange-950 shadow-sm">
            <div class="flex items-center space-x-2 text-sm font-bold text-orange-900 mb-1">
                <span class="text-lg">⚠</span>
                <span>Catatan Perbaikan dari Sekretariat Besar:</span>
            </div>
            <p class="text-xs leading-relaxed bg-white/80 p-3 rounded-xl border border-orange-200 mt-2 font-medium">
                <?= nl2br(View::e($letter['revision_notes'])) ?>
            </p>
            <p class="text-[11px] text-orange-800 mt-2">
                * Silakan perbarui data atau unggah berkas draf yang telah disempurnakan di bawah ini. Setelah disimpan, status akan otomatis kembali menjadi <strong>Diajukan</strong> untuk ditinjau ulang oleh Sekretariat Besar.
            </p>
        </div>
    <?php endif; ?>

    <!-- Main Edit Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="<?= App::baseUrl('letters/update') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= $letter['id'] ?>">

            <!-- Row 1: Kategori & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kategori Klasifikasi Surat <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (int)$letter['category_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                                [<?= View::e($c['code']) ?>] <?= View::e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="letter_date" required value="<?= View::e($letter['letter_date']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                </div>
            </div>

            <!-- Row 2: Perihal & Penerima -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Perihal / Judul Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required value="<?= View::e($letter['title']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Penerima / Tujuan Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="recipient" required value="<?= View::e($letter['recipient']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                </div>
            </div>

            <!-- Row 3: Ringkasan Isi Surat -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Ringkasan Isi / Catatan Pengantar
                </label>
                <textarea name="description" rows="4" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none"><?= View::e($letter['description']) ?></textarea>
            </div>

            <!-- Row 4: Berkas Dokumen -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Berkas Dokumen (Draf Surat / Proposal)
                </label>
                
                <?php if (!empty($letter['attachment_path'])): ?>
                    <div class="mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="text-base">📄</span>
                            <span class="font-bold text-slate-800">Berkas Tersimpan:</span>
                            <span class="text-slate-600 font-mono truncate max-w-xs"><?= basename($letter['attachment_path']) ?></span>
                        </div>
                        <a href="<?= App::baseUrl($letter['attachment_path']) ?>" target="_blank" class="text-[#8B0000] font-bold hover:underline">
                            Unduh / Pratinjau &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-5 text-center hover:border-red-400 transition bg-slate-50">
                    <input type="file" name="attachment" id="attachment" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           onchange="document.getElementById('file-chosen').textContent = this.files[0] ? this.files[0].name : 'Belum ada file baru dipilih'">
                    <label for="attachment" class="cursor-pointer inline-flex flex-col items-center">
                        <span class="text-xs font-bold text-[#8B0000] hover:underline">Unggah Berkas Pengganti / Hasil Perbaikan</span>
                        <span class="text-[11px] text-slate-400 mt-0.5">Format: PDF, DOC, DOCX, atau Gambar (Biarkan kosong jika tetap memakai file lama)</span>
                    </label>
                    <p id="file-chosen" class="mt-2 text-xs font-bold text-slate-700">Belum ada file baru dipilih</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <?php if (!in_array($letter['status'], ['disetujui', 'selesai'])): ?>
                        <button type="button" onclick="if(confirm('Yakin ingin menghapus surat ini?')) document.getElementById('delete-form').submit();" 
                                class="px-4 py-2 text-xs text-red-600 hover:bg-red-50 rounded-xl font-bold transition">
                            ✕ Hapus Pengajuan
                        </button>
                    <?php endif; ?>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="<?= App::baseUrl('letters/show?id=' . $letter['id']) ?>" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-[#8B0000] hover:bg-[#6B0000] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center space-x-2">
                        <span>Simpan & Serahkan Kembali</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" action="<?= App::baseUrl('letters/delete') ?>" method="POST" class="hidden">
            <?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= $letter['id'] ?>">
        </form>
    </div>
</div>

