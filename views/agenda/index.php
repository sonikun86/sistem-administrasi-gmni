<?php
// views/agenda/index.php
use App\Helpers\Auth;
use App\Helpers\View;
use App\Config\App;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Buku Agenda Surat Digital</h1>
            <p class="text-xs text-slate-500 mt-1">
                Rekapitulasi resmi surat masuk dan surat keluar untuk keperluan dokumentasi, arsip, dan LPJ organisasi.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="<?= App::baseUrl('agenda/print?direction=' . $direction . '&komisariat_id=' . ($_GET['komisariat_id'] ?? '')) ?>" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Rekap Agenda / PDF</span>
            </a>
        </div>
    </div>

    <!-- Direction Selector Pills -->
    <div class="flex items-center space-x-2">
        <a href="<?= App::baseUrl('agenda?direction=masuk') ?>" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $direction === 'masuk' ? 'bg-[#8B0000] text-white shadow' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
            📥 Buku Agenda Surat Masuk
        </a>
        <a href="<?= App::baseUrl('agenda?direction=keluar') ?>" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $direction === 'keluar' ? 'bg-[#8B0000] text-white shadow' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
            📤 Buku Agenda Surat Keluar
        </a>
    </div>

    <!-- Agenda Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                Daftar Agenda: <?= $direction === 'masuk' ? 'Surat Masuk ke Komisariat Besar' : 'Surat Keluar Resmi' ?> (<?= count($letters) ?> Dokumen)
            </span>
        </div>

        <?php if (empty($letters)): ?>
            <div class="p-10 text-center text-slate-400 text-xs">
                Tidak ada data agenda surat pada kategori ini.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">No. Urut</th>
                            <th class="px-4 py-3">Nomor Surat Resmi</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3"><?= $direction === 'masuk' ? 'Pengirim (DPK)' : 'Tujuan' ?></th>
                            <th class="px-4 py-3">Perihal / Isi Ringkas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        <?php $no = 1; foreach ($letters as $l): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-bold text-slate-400">
                                    <?= sprintf('%03d', $no++) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px] font-bold text-[#8B0000]">
                                    <?= View::e($l['reference_number'] ?: $l['tracking_code']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                                    <?= View::formatDate($l['letter_date']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-semibold text-slate-800">
                                    <?= View::e($direction === 'masuk' ? $l['sender'] : $l['recipient']) ?>
                                </td>
                                <td class="px-4 py-3 max-w-sm">
                                    <span class="font-bold text-slate-900 block"><?= View::e($l['title']) ?></span>
                                    <span class="text-[11px] text-slate-400 line-clamp-1"><?= View::e($l['description'] ?? '') ?></span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?= View::statusBadge($l['status']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="<?= App::baseUrl('letters/show?id=' . $l['id']) ?>" class="font-bold text-[#8B0000] hover:underline">
                                        Lihat Surat &rarr;
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

