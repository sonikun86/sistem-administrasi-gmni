<?php
// views/layouts/main.php
use App\Helpers\Auth;
use App\Helpers\View;
use App\Config\App;

$currentUser = Auth::user();
$flashes = View::getFlashes();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($title ?? 'SIMAS GMNI UNISDA') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gmni: {
                            red: '#8B0000',
                            dark: '#6B0000',
                            light: '#A30000',
                            gold: '#D4AF37',
                            yellow: '#FFCC00'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js for dropdowns and tabs -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <!-- Top Navigation Bar -->
    <header class="bg-[#8B0000] text-white sticky top-0 z-40 shadow-md border-b-2 border-[#D4AF37]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand & Logo -->
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg text-red-100 hover:text-white hover:bg-[#6B0000] focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <a href="<?= App::baseUrl('dashboard') ?>" class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow font-black text-[#8B0000] text-lg border-2 border-[#D4AF37]">
                            ★
                        </div>
                        <div>
                            <span class="font-extrabold text-lg tracking-tight block leading-tight">SIMAS GMNI UNISDA</span>
                            <span class="text-xs text-amber-200 tracking-wider font-semibold block">KOMISARIAT BESAR & DPK FAKULTAS</span>
                        </div>
                    </a>
                </div>

                <!-- Right Menu: Quick Account Switcher & User Profile -->
                <div class="flex items-center space-x-3">
                    <!-- Quick Demo Role Switcher -->
                    <div class="hidden lg:flex items-center bg-[#6B0000] px-2 py-1 rounded-lg text-xs border border-red-900/40">
                        <span class="text-amber-300 font-semibold mr-2">Simulasi Akun:</span>
                        <a href="<?= App::baseUrl('auth/quick?role=ketua_besar') ?>" class="px-2 py-0.5 rounded hover:bg-red-800 text-white <?= (Auth::isKetuaBesar() ? 'bg-[#8B0000] font-bold text-amber-200 shadow' : 'text-gray-300') ?>">Ketua</a>
                        <a href="<?= App::baseUrl('auth/quick?role=sekretaris_besar') ?>" class="px-2 py-0.5 rounded hover:bg-red-800 text-white <?= (Auth::isSekretarisBesar() ? 'bg-[#8B0000] font-bold text-amber-200 shadow' : 'text-gray-300') ?>">Sekretaris</a>
                        <a href="<?= App::baseUrl('auth/quick?role=dpk_hatta') ?>" class="px-2 py-0.5 rounded hover:bg-red-800 text-white <?= ($currentUser['email'] ?? '') === 'dpk.hatta@gmni-unisda.or.id' ? 'bg-[#8B0000] font-bold text-amber-200 shadow' : 'text-gray-300' ?>">DPK Hatta</a>
                        <a href="<?= App::baseUrl('auth/quick?role=dpk_ficus') ?>" class="px-2 py-0.5 rounded hover:bg-red-800 text-white <?= ($currentUser['email'] ?? '') === 'dpk.ficus@gmni-unisda.or.id' ? 'bg-[#8B0000] font-bold text-amber-200 shadow' : 'text-gray-300' ?>">DPK Ficus</a>
                        <a href="<?= App::baseUrl('auth/quick?role=dpk_brahmana') ?>" class="px-2 py-0.5 rounded hover:bg-red-800 text-white <?= ($currentUser['email'] ?? '') === 'dpk.brahmana@gmni-unisda.or.id' ? 'bg-[#8B0000] font-bold text-amber-200 shadow' : 'text-gray-300' ?>">DPK Brahmana</a>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-[#6B0000] transition">
                            <div class="w-8 h-8 rounded-full bg-amber-400 text-red-950 font-bold flex items-center justify-center text-sm shadow">
                                <?= strtoupper(substr($currentUser['name'] ?? 'G', 0, 1)) ?>
                            </div>
                            <div class="hidden sm:block text-left text-xs">
                                <div class="font-bold leading-tight"><?= View::e($currentUser['name'] ?? 'User') ?></div>
                                <div class="text-amber-200 font-medium">
                                    <?= Auth::isKomisariatBesar() ? 'Komisariat Besar' : View::e($currentUser['komisariat_name'] ?? 'DPK Fakultas') ?>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl py-2 border border-slate-200 z-50 text-slate-800">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs text-slate-400 font-semibold uppercase">Masuk Sebagai</p>
                                <p class="text-sm font-bold text-slate-800"><?= View::e($currentUser['name'] ?? '') ?></p>
                                <p class="text-xs text-[#8B0000] font-semibold"><?= View::e($currentUser['position'] ?? $currentUser['role'] ?? '') ?></p>
                            </div>
                            <div class="py-1">
                                <a href="<?= App::baseUrl('dashboard') ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Dashboard</a>
                                <a href="<?= App::baseUrl('letters') ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Manajemen Surat</a>
                                <a href="<?= App::baseUrl('agenda') ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Buku Agenda</a>
                                <?php if (Auth::isKomisariatBesar()): ?>
                                    <a href="<?= App::baseUrl('approvals') ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Meja Verifikasi & Disposisi</a>
                                    <a href="<?= App::baseUrl('dpk') ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Kelola 3 DPK Fakultas</a>
                                <?php endif; ?>
                            </div>
                            <div class="border-t border-slate-100 pt-1">
                                <a href="<?= App::baseUrl('logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Keluar (Logout)</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container with Sidebar and Content -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Sidebar Navigation (Desktop & Mobile Drawer) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 transform transition-transform duration-200 ease-in-out md:translate-x-0 md:static md:inset-auto md:w-64 flex flex-col">
            
            <!-- Quick Action: Submit Letter Button -->
            <div class="p-4 border-b border-slate-100">
                <a href="<?= App::baseUrl('letters/create') ?>" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#8B0000] hover:bg-[#6B0000] text-white text-sm font-semibold rounded-xl shadow-sm transition space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span><?= Auth::isDpk() ? 'Ajukan Surat Baru' : 'Buat / Input Surat' ?></span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="<?= App::baseUrl('dashboard') ?>" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition <?= str_ends_with($currentUri, '/dashboard') || $currentUri === '/' ? 'bg-red-50 text-[#8B0000] font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>

                <a href="<?= App::baseUrl('letters') ?>" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition <?= str_contains($currentUri, '/letters') && !str_contains($currentUri, '/letters/create') ? 'bg-red-50 text-[#8B0000] font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Semua Surat
                </a>

                <?php if (Auth::isKomisariatBesar()): ?>
                <a href="<?= App::baseUrl('approvals') ?>" class="flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-xl transition <?= str_contains($currentUri, '/approvals') ? 'bg-red-50 text-[#8B0000] font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>Verifikasi & Disposisi</span>
                    </div>
                    <span class="bg-amber-100 text-amber-800 text-xs px-2 py-0.5 rounded-full font-bold">Review</span>
                </a>
                <?php endif; ?>

                <a href="<?= App::baseUrl('agenda') ?>" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition <?= str_contains($currentUri, '/agenda') ? 'bg-red-50 text-[#8B0000] font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Buku Agenda Surat
                </a>

                <a href="<?= App::baseUrl('dpk') ?>" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition <?= str_contains($currentUri, '/dpk') ? 'bg-red-50 text-[#8B0000] font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    DPK Tingkat Fakultas
                </a>
            </nav>

            <!-- GMNI Motto Footer in Sidebar -->
            <div class="p-4 border-t border-slate-200 bg-slate-50 text-center">
                <p class="text-xs font-bold text-[#8B0000]">"Pejuang Pemikir - Pemikir Pejuang"</p>
                <p class="text-[10px] text-slate-400 mt-0.5">GMNI UNISDA Lamongan</p>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Flash Notifications -->
                <?php if (!empty($flashes)): ?>
                    <div class="space-y-2">
                        <?php foreach ($flashes as $flash): ?>
                            <?php 
                                $flashClass = match($flash['type']) {
                                    'success' => 'bg-emerald-50 border-emerald-300 text-emerald-800',
                                    'error' => 'bg-red-50 border-red-300 text-red-800',
                                    'warning' => 'bg-amber-50 border-amber-300 text-amber-800',
                                    default => 'bg-blue-50 border-blue-300 text-blue-800'
                                };
                            ?>
                            <div class="p-4 rounded-xl border flex items-center justify-between <?= $flashClass ?> shadow-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold">
                                        <?= match($flash['type']) { 'success' => '✓ Sukses:', 'error' => '✕ Peringatan:', 'warning' => '⚠ Perhatian:', default => 'ℹ Info:' } ?>
                                    </span>
                                    <span class="text-sm"><?= View::e($flash['message']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Page Content Injected Here -->
                <?= $content ?>

            </div>
        </main>
    </div>

</body>
</html>

