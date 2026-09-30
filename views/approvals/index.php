<?php
// views/approvals/index.php
use App\Helpers\Auth;
use App\Helpers\View;
use App\Config\App;
?>

<div class="space-y-6" x-data="{ tab: '<?= Auth::isSekretarisBesar() ? 'verification' : 'approval' ?>' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Meja Verifikasi & Persetujuan Surat</h1>
            <p class="text-xs text-slate-500 mt-1">
                Alur percepatan birokrasi persuratan Komisariat Besar GMNI UNISDA dan 3 DPK Fakultas tanpa tatap muka langsung.
            </p>
        </div>
    </div>

    <!-- Tab Selector -->
    <div class="flex border-b border-slate-200 space-x-4 text-xs font-bold">
        <button @click="tab = 'verification'" 
                :class="tab === 'verification' ? 'border-[#8B0000] text-[#8B0000] border-b-2 pb-2' : 'text-slate-500 hover:text-slate-800 pb-2'"
                class="flex items-center space-x-2 transition">
            <span>Antrean Verifikasi Sekretariat</span>
            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-[10px] font-extrabold">
                <?= count($pendingVerification) ?>
            </span>
        </button>

        <button @click="tab = 'approval'" 
                :class="tab === 'approval' ? 'border-[#8B0000] text-[#8B0000] border-b-2 pb-2' : 'text-slate-500 hover:text-slate-800 pb-2'"
                class="flex items-center space-x-2 transition">
            <span>Antrean Persetujuan & Disposisi Ketua</span>
            <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-[10px] font-extrabold">
                <?= count($pendingApproval) ?>
            </span>
        </button>
    </div>

    <!-- Tab 1 Content: Verifikasi Sekretariat -->
    <div x-show="tab === 'verification'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-blue-50/50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-blue-950">Surat Masuk Menunggu Pemeriksaan Berkas</h3>
                    <p class="text-[11px] text-blue-800/80">Sekretaris memeriksa draf, mengalokasikan nomor surat resmi GMNI, atau mengembalikan berkas jika perlu revisi.</p>
                </div>
            </div>

            <?php if (empty($pendingVerification)): ?>
                <div class="p-10 text-center text-slate-400 text-xs">
                    <div class="text-3xl mb-2">🎉</div>
                    Tidak ada antrean surat yang menunggu verifikasi saat ini.
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($pendingVerification as $l): ?>
                        <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 hover:bg-slate-50 transition">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-slate-900 text-sm"><?= View::e($l['tracking_code']) ?></span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold"><?= View::e($l['category_name']) ?></span>
                                    <span class="text-slate-400">&bull; <?= View::formatDate($l['letter_date']) ?></span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm"><?= View::e($l['title']) ?></h4>
                                <p class="text-slate-500">Pengirim: <span class="font-semibold text-slate-800"><?= View::e($l['sender']) ?></span></p>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <a href="<?= App::baseUrl('letters/show?id=' . $l['id']) ?>" 
                                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                    Periksa & Alokasikan No. Surat &rarr;
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2 Content: Persetujuan Ketua -->
    <div x-show="tab === 'approval'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-emerald-50/50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-emerald-950">Surat Menunggu Pengesahan Ketua Besar</h3>
                    <p class="text-[11px] text-emerald-800/80">Berkas telah divalidasi Sekretariat. Ketua memberikan pengesahan digital, disposisi, dan menerbitkan QR Code.</p>
                </div>
            </div>

            <?php if (empty($pendingApproval)): ?>
                <div class="p-10 text-center text-slate-400 text-xs">
                    <div class="text-3xl mb-2">✨</div>
                    Semua berkas yang diverifikasi telah disetujui.
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($pendingApproval as $l): ?>
                        <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 hover:bg-slate-50 transition">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-[#8B0000] font-mono text-sm"><?= View::e($l['reference_number']) ?></span>
                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-bold border border-blue-200">Siap Disahkan</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm"><?= View::e($l['title']) ?></h4>
                                <p class="text-slate-500">Asal: <span class="font-semibold text-slate-800"><?= View::e($l['sender']) ?></span> &bull; Tgl: <?= View::formatDate($l['letter_date']) ?></p>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <a href="<?= App::baseUrl('letters/show?id=' . $l['id']) ?>" 
                                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                    Tinjau & Beri Disposisi &rarr;
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

