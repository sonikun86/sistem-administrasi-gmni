<?php
// views/dashboard/index.php
use App\Helpers\Auth;
use App\Helpers\View;
use App\Config\App;
?>

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-[#8B0000] via-[#A30000] to-red-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-black/20 px-3 py-1 rounded-full text-xs text-amber-300 font-semibold mb-2">
                <span>★</span>
                <span>Merdeka! Pejuang Pemikir - Pemikir Pejuang</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Selamat Datang, <?= View::e($user['name'] ?? 'Kamerad') ?>!
            </h1>
            <p class="text-red-100 text-sm mt-1 max-w-2xl">
                <?= Auth::isKomisariatBesar() 
                    ? 'Portal Manajemen Administrasi & Tata Kelola Persuratan Komisariat Besar GMNI UNISDA. Pantau dan proses pengajuan surat dari seluruh DPK Fakultas secara efisien dan transparan.' 
                    : 'Portal Administrasi ' . View::e($user['komisariat_name'] ?? 'DPK') . '. Ajukan surat rekomendasi, permohonan SK, dan pantau status pengesahan tanpa harus bertatap muka langsung.' ?>
            </p>
        </div>
        <div class="flex items-center space-x-3 shrink-0">
            <a href="<?= App::baseUrl('letters/create') ?>" class="px-5 py-2.5 bg-[#D4AF37] hover:bg-yellow-500 text-slate-950 font-bold text-sm rounded-xl shadow transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span><?= Auth::isDpk() ? 'Ajukan Surat Baru' : 'Input Surat Baru' ?></span>
            </a>
        </div>
    </div>
</div>

<!-- 4 Key Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total Surat -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Surat</p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1"><?= $stats['total'] ?></h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Semua arsip surat</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg">
            📑
        </div>
    </div>

    <!-- Menunggu Review / Verifikasi -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Review</p>
            <h3 class="text-2xl font-extrabold text-amber-600 mt-1"><?= $stats['pending'] ?></h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Antrean verifikasi / persetujuan</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
            ⏳
        </div>
    </div>

    <!-- Surat Disetujui / Selesai -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Telah Disahkan</p>
            <h3 class="text-2xl font-extrabold text-emerald-600 mt-1"><?= $stats['approved'] ?></h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Resmi & ber-QR Code</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
            ✓
        </div>
    </div>

    <!-- Perlu Revisi -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-orange-600 uppercase tracking-wider">Perlu Revisi</p>
            <h3 class="text-2xl font-extrabold text-orange-600 mt-1"><?= $stats['revision'] ?></h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Memerlukan perbaikan draf</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-lg">
            ✎
        </div>
    </div>
</div>

<!-- Section: Status 3 DPK Fakultas UNISDA -->
<div class="space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#8B0000]"></span>
                <span>3 Komisariat Tingkat Fakultas (DPK se-UNISDA)</span>
            </h2>
            <p class="text-xs text-slate-500">Koordinasi administrasi surat-menyurat dengan Komisariat Besar</p>
        </div>
        <a href="<?= App::baseUrl('dpk') ?>" class="text-xs font-bold text-[#8B0000] hover:underline flex items-center">
            Lihat Profil Lengkap DPK &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php foreach ($dpkStats as $item): ?>
            <?php $dpk = $item['dpk']; ?>
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-red-200 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-block px-2.5 py-1 bg-red-50 text-[#8B0000] rounded-lg text-xs font-extrabold border border-red-100">
                            <?= View::e($dpk['code']) ?>
                        </span>
                        <h3 class="text-base font-bold text-slate-900 mt-2"><?= View::e($dpk['name']) ?></h3>
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1"><?= View::e($dpk['faculty'] ?? '-') ?></p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="bg-slate-50 p-2 rounded-xl">
                        <span class="block text-slate-400 font-semibold text-[10px] uppercase">Total</span>
                        <span class="font-extrabold text-slate-800 text-sm"><?= $item['total'] ?></span>
                    </div>
                    <div class="bg-amber-50 p-2 rounded-xl">
                        <span class="block text-amber-600 font-semibold text-[10px] uppercase">Proses</span>
                        <span class="font-extrabold text-amber-700 text-sm"><?= $item['pending'] ?></span>
                    </div>
                    <div class="bg-emerald-50 p-2 rounded-xl">
                        <span class="block text-emerald-600 font-semibold text-[10px] uppercase">Sah</span>
                        <span class="font-extrabold text-emerald-700 text-sm"><?= $item['approved'] ?></span>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>PIC: <?= View::e($dpk['contact_person'] ?? '-') ?></span>
                    <a href="<?= App::baseUrl('letters?komisariat_id=' . $dpk['id']) ?>" class="text-[#8B0000] font-semibold hover:underline">
                        Buka Surat &rarr;
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Section: Surat Terbaru & Aktivitas Alur Surat -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Aktivitas Surat Terkini</h2>
            <p class="text-xs text-slate-500">Daftar pengajuan surat dan rekomendasi terbaru</p>
        </div>
        <a href="<?= App::baseUrl('letters') ?>" class="text-xs font-bold text-[#8B0000] hover:underline">
            Lihat Semua Surat &rarr;
        </a>
    </div>

    <?php if (empty($recentLetters)): ?>
        <div class="p-8 text-center text-slate-400">
            <p class="text-sm">Belum ada surat yang terdaftar.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Kode / No. Surat</th>
                        <th class="px-5 py-3">Pengirim (DPK)</th>
                        <th class="px-5 py-3">Perihal / Judul</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    <?php foreach ($recentLetters as $l): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block"><?= View::e($l['tracking_code']) ?></span>
                                <?php if (!empty($l['reference_number'])): ?>
                                    <span class="text-[11px] text-[#8B0000] font-semibold block font-mono"><?= View::e($l['reference_number']) ?></span>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-400 italic">Nomor belum terbit</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-semibold text-slate-800"><?= View::e($l['sender']) ?></span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs truncate">
                                <span class="font-semibold text-slate-900 block truncate"><?= View::e($l['title']) ?></span>
                                <span class="text-[11px] text-slate-400 truncate block"><?= View::e($l['recipient']) ?></span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-semibold">
                                    <?= View::e($l['category_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-500">
                                <?= View::formatDate($l['letter_date']) ?>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <?= View::statusBadge($l['status']) ?>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right space-x-2">
                                <a href="<?= App::baseUrl('letters/show?id=' . $l['id']) ?>" 
                                   class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-[#8B0000] font-bold text-xs transition">
                                    Detail & Tracking &rarr;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

