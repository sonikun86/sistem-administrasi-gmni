<?php
// views/letters/index.php
use App\Helpers\Auth;
use App\Helpers\View;
use App\Config\App;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Arsip Surat</h1>
            <p class="text-xs text-slate-500 mt-1">
                <?= Auth::isDpk() 
                    ? 'Kelola pengajuan surat dan dokumen resmi DPK Anda yang terafiliasi dengan Komisariat Besar.' 
                    : 'Kelola seluruh korespondensi surat masuk dari DPK Fakultas dan surat keluar resmi Komisariat Besar.' ?>
            </p>
        </div>
        <div>
            <a href="<?= App::baseUrl('letters/create') ?>" class="inline-flex items-center px-4 py-2.5 bg-[#8B0000] hover:bg-[#6B0000] text-white text-sm font-bold rounded-xl shadow transition space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span><?= Auth::isDpk() ? 'Ajukan Surat Baru' : 'Input Surat Masuk / Keluar' ?></span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="<?= App::baseUrl('letters') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Kata Kunci / No. Surat</label>
                <input type="text" name="q" value="<?= View::e($filters['search']) ?>" placeholder="Cari perihal, nomor, kode tracking..."
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-[#8B0000] outline-none">
            </div>

            <!-- Direction -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Arah Surat</label>
                <select name="direction" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-[#8B0000] outline-none">
                    <option value="">Semua Arah</option>
                    <option value="masuk" <?= $filters['direction'] === 'masuk' ? 'selected' : '' ?>>Surat Masuk</option>
                    <option value="keluar" <?= $filters['direction'] === 'keluar' ? 'selected' : '' ?>>Surat Keluar</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status Surat</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-[#8B0000] outline-none">
                    <option value="">Semua Status</option>
                    <option value="diajukan" <?= $filters['status'] === 'diajukan' ? 'selected' : '' ?>>🟡 Diajukan</option>
                    <option value="diverifikasi" <?= $filters['status'] === 'diverifikasi' ? 'selected' : '' ?>>🔵 Diverifikasi</option>
                    <option value="perlu_revisi" <?= $filters['status'] === 'perlu_revisi' ? 'selected' : '' ?>>🟠 Perlu Revisi</option>
                    <option value="disetujui" <?= $filters['status'] === 'disetujui' ? 'selected' : '' ?>>🟢 Disetujui</option>
                    <option value="ditolak" <?= $filters['status'] === 'ditolak' ? 'selected' : '' ?>>🔴 Ditolak</option>
                    <option value="selesai" <?= $filters['status'] === 'selesai' ? 'selected' : '' ?>>✓ Selesai</option>
                </select>
            </div>

            <!-- DPK Selector (For Komisariat Besar) -->
            <?php if (!Auth::isDpk()): ?>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Asal DPK</label>
                <select name="komisariat_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-1 focus:ring-[#8B0000] outline-none">
                    <option value="">Semua DPK</option>
                    <?php foreach ($komisariats as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (string)$filters['komisariat_id'] === (string)$k['id'] ? 'selected' : '' ?>>
                            <?= View::e($k['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition">
                    Terapkan Filter
                </button>
            </div>
            <?php endif; ?>

            <?php if (!Auth::isDpk()): ?>
            <div class="lg:col-span-5 flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                <a href="<?= App::baseUrl('letters') ?>" class="px-4 py-1.5 text-xs text-slate-500 hover:text-slate-800 font-medium">Reset Filter</a>
                <button type="submit" class="px-5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Cari & Saring Surat
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Letter Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <?php if (empty($letters)): ?>
            <div class="p-12 text-center text-slate-400">
                <div class="text-4xl mb-3">📭</div>
                <h3 class="text-base font-bold text-slate-700">Tidak ada surat ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau buat pengajuan surat baru.</p>
                <a href="<?= App::baseUrl('letters/create') ?>" class="inline-block mt-4 px-4 py-2 bg-[#8B0000] text-white text-xs font-bold rounded-xl shadow">
                    + Buat Pengajuan Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Kode Tracking</th>
                            <th class="px-5 py-3.5">Nomor Surat Resmi</th>
                            <th class="px-5 py-3.5">Arah</th>
                            <th class="px-5 py-3.5">Pengirim</th>
                            <th class="px-5 py-3.5">Tujuan</th>
                            <th class="px-5 py-3.5">Perihal</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        <?php foreach ($letters as $l): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 block"><?= View::e($l['tracking_code']) ?></span>
                                    <span class="text-[10px] text-slate-400"><?= View::e($l['category_name'] ?? 'Surat') ?></span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-mono text-[11px]">
                                    <?php if (!empty($l['reference_number'])): ?>
                                        <span class="font-bold text-[#8B0000]"><?= View::e($l['reference_number']) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[10px]">(Menunggu Pengesahan)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $l['direction'] === 'masuk' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-teal-50 text-teal-700 border border-teal-200' ?>">
                                        <?= $l['direction'] === 'masuk' ? '↓ Masuk' : '↑ Keluar' ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="font-semibold text-slate-900"><?= View::e($l['sender']) ?></span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-slate-600">
                                    <?= View::e($l['recipient']) ?>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs truncate">
                                    <a href="<?= App::baseUrl('letters/show?id=' . $l['id']) ?>" class="font-bold text-slate-900 hover:text-[#8B0000] truncate block">
                                        <?= View::e($l['title']) ?>
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-slate-500">
                                    <?= View::formatDate($l['letter_date']) ?>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <?= View::statusBadge($l['status']) ?>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right space-x-1">
                                    <a href="<?= App::baseUrl('letters/show?id=' . $l['id']) ?>" 
                                       class="inline-flex items-center px-2.5 py-1 bg-[#8B0000] hover:bg-[#6B0000] text-white rounded-lg text-xs font-bold shadow-sm transition">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

