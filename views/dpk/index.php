<?php
// views/dpk/index.php
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;
?>

<div class="space-y-6" x-data="{ showAddModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Komisariat Tingkat Fakultas (DPK se-UNISDA)</h1>
            <p class="text-xs text-slate-500 mt-1">
                Dewan Pengurus Komisariat (DPK) tingkat fakultas di bawah naungan Komisariat Besar GMNI UNISDA.
            </p>
        </div>

        <?php if (Auth::isKomisariatBesar()): ?>
            <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2.5 bg-[#8B0000] hover:bg-[#6B0000] text-white text-sm font-bold rounded-xl shadow transition space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah DPK Baru</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- DPK Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($dpkList as $item): ?>
            <?php $dpk = $item['item']; ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition flex flex-col justify-between <?= $dpk['code'] === 'KOM-BESAR' ? 'border-red-300 bg-red-50/20' : '' ?>">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black <?= $dpk['code'] === 'KOM-BESAR' ? 'bg-red-800 text-white' : 'bg-red-50 text-[#8B0000] border border-red-100' ?>">
                            <?= View::e($dpk['code']) ?>
                        </span>
                        <span class="text-xs font-bold text-slate-400">
                            <?= $item['letters_count'] ?> Surat
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mt-3"><?= View::e($dpk['name']) ?></h3>
                    <p class="text-xs text-slate-600 mt-1 font-medium"><?= View::e($dpk['faculty'] ?? 'Seluruh Fakultas UNISDA') ?></p>
                    
                    <?php if (!empty($dpk['description'])): ?>
                        <p class="text-xs text-slate-500 mt-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <?= View::e($dpk['description']) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 space-y-2">
                    <div class="text-xs text-slate-600 flex items-center justify-between">
                        <span>Kontak / PIC:</span>
                        <span class="font-bold text-slate-800"><?= View::e($dpk['contact_person'] ?? '-') ?></span>
                    </div>
                    <?php if (!empty($dpk['phone'])): ?>
                        <div class="text-xs text-slate-600 flex items-center justify-between">
                            <span>No. Telp/WA:</span>
                            <span class="font-mono text-slate-700"><?= View::e($dpk['phone']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="pt-2">
                        <a href="<?= App::baseUrl('letters?komisariat_id=' . $dpk['id']) ?>" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 bg-slate-100 hover:bg-red-50 text-slate-800 hover:text-[#8B0000] font-bold text-xs rounded-xl transition">
                            Lihat Semua Arsip Surat DPK &rarr;
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal Add New DPK (For Komisariat Besar) -->
    <?php if (Auth::isKomisariatBesar()): ?>
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Tambah DPK Tingkat Fakultas Baru</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form action="<?= App::baseUrl('dpk') ?>" method="POST" class="space-y-3">
                    <?= Csrf::field() ?>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama DPK:</label>
                        <input type="text" name="name" required placeholder="Contoh: DPK GMNI Kartini" 
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Singkatan (Huruf Kapital):</label>
                        <input type="text" name="code" required placeholder="Contoh: KARTINI" 
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 uppercase font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Naungan Fakultas:</label>
                        <input type="text" name="faculty" placeholder="Contoh: Fakultas Ekonomi dan Bisnis (FEB)" 
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Ketua / Kontak:</label>
                            <input type="text" name="contact_person" placeholder="Nama pengurus" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp:</label>
                            <input type="text" name="phone" placeholder="08..." 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat:</label>
                        <textarea name="description" rows="2" placeholder="Keterangan singkat..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs text-slate-600">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-[#8B0000] text-white text-xs font-bold rounded-xl shadow">Simpan DPK Baru</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

