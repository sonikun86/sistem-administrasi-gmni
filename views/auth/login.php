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

    <!-- Informational Card: Official Accounts -->
    <div class="mt-6 pt-5 border-t border-slate-100">
        <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200 text-xs">
            <div class="flex items-center space-x-2 text-slate-700 font-bold mb-2">
                <svg class="w-4 h-4 text-[#8B0000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Akses Masuk Terlindungi & Terpisah</span>
            </div>
            <p class="text-[11px] text-slate-500 mb-2 leading-relaxed">
                Setiap ketua dan pengurus memiliki akses akun masing-masing. Masukkan email dan kata sandi Anda untuk melanjutkan.
            </p>
            <div class="space-y-1 text-[11px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200">
                <div class="font-semibold text-slate-700">Daftar Akun Resmi:</div>
                <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                    <span>Ketua Komisariat Besar:</span>
                    <button type="button" onclick="document.getElementById('email').value='ketua.besar@gmni-unisda.or.id'; document.getElementById('password').focus();" class="text-[#8B0000] hover:underline font-mono text-[10px]">ketua.besar@gmni-unisda.or.id</button>
                </div>
                <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                    <span>Sekretaris Besar:</span>
                    <button type="button" onclick="document.getElementById('email').value='sekretaris.besar@gmni-unisda.or.id'; document.getElementById('password').focus();" class="text-[#8B0000] hover:underline font-mono text-[10px]">sekretaris.besar@gmni-unisda.or.id</button>
                </div>
                <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                    <span>DPK GMNI Hatta:</span>
                    <button type="button" onclick="document.getElementById('email').value='dpk.hatta@gmni-unisda.or.id'; document.getElementById('password').focus();" class="text-[#8B0000] hover:underline font-mono text-[10px]">dpk.hatta@gmni-unisda.or.id</button>
                </div>
                <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                    <span>DPK GMNI Ficus:</span>
                    <button type="button" onclick="document.getElementById('email').value='dpk.ficus@gmni-unisda.or.id'; document.getElementById('password').focus();" class="text-[#8B0000] hover:underline font-mono text-[10px]">dpk.ficus@gmni-unisda.or.id</button>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span>DPK GMNI Brahmana:</span>
                    <button type="button" onclick="document.getElementById('email').value='dpk.brahmana@gmni-unisda.or.id'; document.getElementById('password').focus();" class="text-[#8B0000] hover:underline font-mono text-[10px]">dpk.brahmana@gmni-unisda.or.id</button>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 text-center">Klik email di atas untuk mengisi formulir, lalu ketik kata sandi akun Anda.</p>
        </div>
    </div>
</div>

