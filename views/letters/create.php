<?php
// views/letters/create.php
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="<?= App::baseUrl('letters') ?>" class="text-xs text-[#8B0000] font-bold hover:underline flex items-center mb-1">
                &larr; Kembali ke Daftar Surat
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                <?= Auth::isDpk() ? 'Formulir Pengajuan Surat / Rekomendasi' : 'Pencatatan Surat Masuk & Keluar' ?>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Surat yang diajukan akan diproses secara digital oleh pengurus Komisariat Besar GMNI UNISDA.
            </p>
        </div>
    </div>

    <!-- Information Card -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900 flex items-start space-x-3">
        <span class="text-lg">💡</span>
        <div>
            <span class="font-bold block mb-0.5">Alur Administrasi Digital GMNI UNISDA:</span>
            Pengurus DPK cukup mengunggah draf surat dan berkas pendukung (proposal/lampiran). Sekretariat Komisariat Besar akan memvalidasi format administrasi dan Ketua Komisariat Besar akan membubuhkan pengesahan resmi ber-QR Code tanpa perlu tatap muka langsung.
        </div>
    </div>

    <!-- Main Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="<?= App::baseUrl('letters/store') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= Csrf::field() ?>

            <!-- Row 1: Kategori & Arah -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kategori Klasifikasi Surat <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                        <option value="">-- Pilih Jenis Surat --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                [<?= View::e($c['code']) ?>] <?= View::e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Arah / Tipe Surat <span class="text-red-500">*</span>
                    </label>
                    <?php if (Auth::isDpk()): ?>
                        <input type="text" readonly value="Surat Masuk (Pengajuan dari DPK ke Komisariat Besar)" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-sm font-medium">
                        <input type="hidden" name="direction" value="masuk">
                    <?php else: ?>
                        <select name="direction" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                            <option value="masuk">Surat Masuk (Dari DPK / Pihak Luar ke Komisariat Besar)</option>
                            <option value="keluar">Surat Keluar (Resmi diterbitkan Komisariat Besar ke DPK / Eksternal)</option>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Row 2: Pengirim & Tujuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Pengirim Dokumen
                    </label>
                    <?php if (Auth::isDpk()): ?>
                        <input type="text" readonly value="<?= View::e($user['komisariat_name'] ?? 'DPK GMNI') ?>" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-bold text-sm">
                    <?php else: ?>
                        <input type="text" name="sender" placeholder="Komisariat Besar GMNI UNISDA / Nama Pengirim"
                               value="Komisariat Besar GMNI UNISDA"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Penerima / Tujuan Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="recipient" required 
                           value="<?= Auth::isDpk() ? 'Komisariat Besar GMNI UNISDA' : 'Seluruh DPK GMNI se-UNISDA' ?>"
                           placeholder="Contoh: Komisariat Besar GMNI UNISDA / Dekan FAI / Rektor UNISDA"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                </div>
            </div>

            <!-- Row 3: Perihal & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Perihal / Judul Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required 
                           placeholder="Contoh: Permohonan Rekomendasi Kegiatan Kajian Ideologi / Pengesahan Panitia PPAB"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="letter_date" required value="<?= date('Y-m-d') ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none">
                </div>
            </div>

            <!-- Row 4: Ringkasan Isi Surat -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Ringkasan Isi / Catatan Pengantar
                </label>
                <textarea name="description" rows="3" placeholder="Jelaskan secara singkat latar belakang permohonan, nama kegiatan, waktu pelaksanaan, atau daftar nama pengurus yang diajukan..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] outline-none"></textarea>
            </div>

            <!-- Row 5: Upload Berkas Dokumen (PDF/DOCX) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Unggah Berkas Dokumen (Draf Surat / Proposal / Berkas Lampiran)
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-red-400 transition bg-slate-50">
                    <input type="file" name="attachment" id="attachment" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           onchange="document.getElementById('file-chosen').textContent = this.files[0] ? this.files[0].name : 'Belum ada file dipilih'">
                    <label for="attachment" class="cursor-pointer inline-flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-red-100 text-[#8B0000] flex items-center justify-center text-xl mb-2 font-bold">
                            📎
                        </div>
                        <span class="text-sm font-bold text-[#8B0000] hover:underline">Pilih Berkas dari Perangkat</span>
                        <span class="text-xs text-slate-400 mt-0.5">Format: PDF, DOC, DOCX, atau Gambar (Maks 10MB)</span>
                    </label>
                    <p id="file-chosen" class="mt-2 text-xs font-bold text-slate-700">Belum ada file dipilih</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="<?= App::baseUrl('letters') ?>" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#8B0000] hover:bg-[#6B0000] text-white text-sm font-bold rounded-xl shadow-md transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Ajukan & Simpan ke Sistem</span>
                </button>
            </div>
        </form>
    </div>
</div>

