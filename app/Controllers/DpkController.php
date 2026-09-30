<?php
// Controllers/DpkController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Komisariat;
use App\Models\Letter;
use App\Models\User;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\View;
use App\Config\App;

class DpkController
{
    public function index(): void
    {
        Auth::requireAuth();

        $komisariatModel = new Komisariat();
        $letterModel = new Letter();
        $userModel = new User();

        $allDpk = $komisariatModel->all('id ASC');
        $dpkList = [];

        foreach ($allDpk as $dpk) {
            $id = (int)$dpk['id'];
            $lettersCount = $letterModel->count("komisariat_id = {$id}");
            $users = $userModel->where('komisariat_id', $id);
            $dpkList[] = [
                'item' => $dpk,
                'letters_count' => $lettersCount,
                'users' => $users
            ];
        }

        View::render('dpk.index', [
            'title' => 'Komisariat Tingkat Fakultas - GMNI UNISDA',
            'dpkList' => $dpkList,
            'user' => Auth::user()
        ]);
    }

    public function store(): void
    {
        Auth::requireKomisariatBesar();
        Csrf::verify();

        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $faculty = trim($_POST['faculty'] ?? '');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $code === '') {
            View::flash('error', 'Nama Komisariat dan Kode Singkatan wajib diisi.');
            header('Location: ' . App::baseUrl('dpk'));
            exit;
        }

        $komisariatModel = new Komisariat();
        if ($komisariatModel->findByCode($code)) {
            View::flash('error', "Kode komisariat '{$code}' sudah terdaftar.");
            header('Location: ' . App::baseUrl('dpk'));
            exit;
        }

        $komisariatModel->create([
            'code' => $code,
            'name' => $name,
            'faculty' => $faculty,
            'contact_person' => $contactPerson,
            'phone' => $phone,
            'description' => $description,
            'is_active' => 1
        ]);

        View::flash('success', "DPK baru '{$name}' berhasil ditambahkan ke sistem!");
        header('Location: ' . App::baseUrl('dpk'));
        exit;
    }
}

