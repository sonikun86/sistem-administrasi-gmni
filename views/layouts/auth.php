<?php
// views/layouts/auth.php
use App\Helpers\View;
$flashes = View::getFlashes();
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($title ?? 'SIMAS GMNI UNISDA') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-[#450a0a] to-slate-900 text-slate-100">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#8B0000] border-2 border-[#D4AF37] shadow-xl text-3xl font-black text-white mb-4">
            ★
        </div>
        <h2 class="text-2xl font-black tracking-tight text-white">SIMAS GMNI UNISDA</h2>
        <p class="mt-1 text-sm text-amber-200/90 font-medium">Sistem Informasi Manajemen Administrasi Surat</p>
        <p class="text-xs text-slate-400">Komisariat Besar & DPK Tingkat Fakultas UNISDA</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg px-4">
        <?php if (!empty($flashes)): ?>
            <div class="mb-4 space-y-2">
                <?php foreach ($flashes as $flash): ?>
                    <div class="p-3 rounded-xl text-sm font-medium <?= $flash['type'] === 'error' ? 'bg-red-900/80 text-red-200 border border-red-700' : 'bg-emerald-900/80 text-emerald-200 border border-emerald-700' ?>">
                        <?= View::e($flash['message']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white text-slate-800 py-8 px-6 shadow-2xl rounded-2xl sm:px-10 border border-slate-200">
            <?= $content ?>
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> Komisariat Besar GMNI UNISDA &bull; "Pejuang Pemikir - Pemikir Pejuang"
        </div>
    </div>

</body>
</html>

