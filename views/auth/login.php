<?php
// views/auth/login.php
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;
?>

<div>
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-slate-900">Masuk ke Portal Administrasi</h3>
        <p class="text-xs text-slate-500 mt-1">Silakan masuk menggunakan akun Komisariat Besar atau akun DPK Fakultas Anda.</p>
    </div>

    <form action="<?= App::baseUrl('login') ?>" method="POST" class="space-y-4">
        <?= Csrf::field() ?>

        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Akun</label>
            <input type="email" name="email" id="email" required placeholder="nama.kader@gmni-unisda.or.id"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] focus:border-[#8B0000] outline-none transition">
        </div>

        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi</label>
            <input type="password" name="password" id="password" required placeholder="••••••••"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#8B0000] focus:border-[#8B0000] outline-none transition">
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#8B0000] hover:bg-[#6B0000] text-white font-bold text-sm shadow-md transition duration-150 flex items-center justify-center space-x-2">
            <span>Masuk Sistem</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
    </form>

    <!-- Quick Access Demo Buttons -->
    <div class="mt-6 pt-6 border-t border-slate-100">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider text-center mb-3">Pilih Cepat Akun Demo (1-Klik Masuk):</p>
        <div class="grid grid-cols-1 gap-2 text-xs">
            <a href="<?= App::baseUrl('auth/quick?role=ketua_besar') ?>" class="p-2.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-950 font-semibold flex items-center justify-between transition">
                <span>★ Ketua Komisariat Besar</span>
                <span class="text-[10px] bg-red-200 text-red-800 px-2 py-0.5 rounded">Disposisi & SK</span>
            </a>
            <a href="<?= App::baseUrl('auth/quick?role=sekretaris_besar') ?>" class="p-2.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-950 font-semibold flex items-center justify-between transition">
                <span>✦ Sekretaris Besar</span>
                <span class="text-[10px] bg-amber-200 text-amber-800 px-2 py-0.5 rounded">Verifikasi & No. Surat</span>
            </a>
            <div class="grid grid-cols-3 gap-1.5 pt-1">
                <a href="<?= App::baseUrl('auth/quick?role=dpk_hatta') ?>" class="p-2 text-center rounded-lg border border-slate-200 hover:bg-slate-100 font-medium text-slate-800 transition">
                    DPK Hatta
                </a>
                <a href="<?= App::baseUrl('auth/quick?role=dpk_ficus') ?>" class="p-2 text-center rounded-lg border border-slate-200 hover:bg-slate-100 font-medium text-slate-800 transition">
                    DPK Ficus
                </a>
                <a href="<?= App::baseUrl('auth/quick?role=dpk_brahmana') ?>" class="p-2 text-center rounded-lg border border-slate-200 hover:bg-slate-100 font-medium text-slate-800 transition">
                    DPK Brahmana
                </a>
            </div>
        </div>
        <p class="text-[11px] text-center text-slate-400 mt-2">Password default: <code class="text-slate-600 font-semibold">password123</code></p>
    </div>
</div>

