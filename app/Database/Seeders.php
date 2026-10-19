<?php
// Database/Seeders.php
declare(strict_types=1);

namespace App\Database;

use App\Models\Komisariat;
use App\Models\User;
use App\Models\LetterCategory;
use App\Models\Letter;
use App\Models\LetterTimeline;

class Seeders
{
    public static function run(): void
    {
        $komisariatModel = new Komisariat();
        $userModel = new User();
        $catModel = new LetterCategory();
        $letterModel = new Letter();

        // 1. Seed Komisariats
        if ($komisariatModel->count() === 0) {
            $kBesarId = $komisariatModel->create([
                'code' => 'KOM-BESAR',
                'name' => 'Komisariat Besar GMNI UNISDA',
                'faculty' => 'Tingkat Universitas Islam Darul \'Ulum Lamongan',
                'description' => 'Dewan Pimpinan Komisariat Besar / Koordinator Komisariat GMNI UNISDA Lamongan',
                'contact_person' => 'Bung Irgi Alamsyah Ali Alhana (Ketua)',
                'phone' => '081234567890',
                'is_active' => 1
            ]);

            $kHattaId = $komisariatModel->create([
                'code' => 'HATTA',
                'name' => 'DPK GMNI Hatta',
                'faculty' => 'Fakultas Hukum & Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)',
                'description' => 'Komisariat Tingkat Fakultas bidang Hukum dan Ilmu Sosial',
                'contact_person' => 'Bung Wahyu Yulianto (Ketua DPK)',
                'phone' => '081234567891',
                'is_active' => 1
            ]);

            $kFicusId = $komisariatModel->create([
                'code' => 'FICUS_BENJAMINA',
                'name' => 'DPK GMNI Ficus Benjamina',
                'faculty' => 'Fakultas Pertanian, Peternakan & Sains Teknologi (MIPA)',
                'description' => 'Komisariat Tingkat Fakultas bidang Agrokompleks dan Sains Teknologi',
                'contact_person' => 'Sarinah Ana Syauki Virda (Ketua DPK)',
                'phone' => '081234567892',
                'is_active' => 1
            ]);

            $kBrahmanaId = $komisariatModel->create([
                'code' => 'BRAHMANA',
                'name' => 'DPK GMNI Brahmana',
                'faculty' => 'Fakultas Agama Islam (FAI) & FKIP',
                'description' => 'Komisariat Tingkat Fakultas bidang Agama Islam dan Pendidikan Keguruan',
                'contact_person' => 'Sarinah Aqila (Ketua DPK)',
                'phone' => '081234567893',
                'is_active' => 1
            ]);
        } else {
            $kBesar = $komisariatModel->findByCode('KOM-BESAR');
            $kHatta = $komisariatModel->findByCode('HATTA');
            $kFicus = $komisariatModel->findByCode('FICUS_BENJAMINA');
            $kBrahmana = $komisariatModel->findByCode('BRAHMANA');

            $kBesarId = $kBesar ? $kBesar['id'] : 1;
            $kHattaId = $kHatta ? $kHatta['id'] : 2;
            $kFicusId = $kFicus ? $kFicus['id'] : 3;
            $kBrahmanaId = $kBrahmana ? $kBrahmana['id'] : 4;
        }

        // 2. Seed Users
        if ($userModel->count() === 0) {
            $defaultPassword = password_hash('password123', PASSWORD_BCRYPT);

            // Ketua Komisariat Besar
            $ketuaId = $userModel->create([
                'komisariat_id' => $kBesarId,
                'name' => 'Irgi Alamsyah Ali Alhana',
                'email' => 'ketua.besar@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'ketua_besar',
                'position' => 'Ketua Komisariat Besar GMNI UNISDA',
                'phone' => '081234567890',
                'is_active' => 1
            ]);

            // Sekretaris Komisariat Besar
            $sekreId = $userModel->create([
                'komisariat_id' => $kBesarId,
                'name' => 'Toni Ardiansyah',
                'email' => 'sekretaris.besar@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'sekretaris_besar',
                'position' => 'Sekretaris Komisariat Besar GMNI UNISDA',
                'phone' => '081234567895',
                'is_active' => 1
            ]);

            // DPK GMNI Hatta
            $dpkHattaUserId = $userModel->create([
                'komisariat_id' => $kHattaId,
                'name' => 'M. Wahyu Yulianto',
                'email' => 'dpk.hatta@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'dpk',
                'position' => 'Ketua DPK GMNI Hatta UNISDA',
                'phone' => '081234567891',
                'is_active' => 1
            ]);
            $userModel->create([
                'komisariat_id' => $kHattaId,
                'name' => 'M. Fathur Rahman',
                'email' => 'sekretaris.hatta@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'dpk',
                'position' => 'Sekretaris DPK GMNI Hatta UNISDA',
                'is_active' => 1
            ]);

            // DPK GMNI Ficus Benjamina
            $dpkFicusUserId = $userModel->create([
                'komisariat_id' => $kFicusId,
                'name' => 'Ana Syauki Firdaus',
                'email' => 'dpk.ficus@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'dpk',
                'position' => 'Ketua DPK GMNI Ficus Benjamina UNISDA',
                'phone' => '081234567892',
                'is_active' => 1
            ]);
            $userModel->create([
                'komisariat_id' => $kFicusId,
                'name' => 'M. Syaifullah Akbar',
                'email' => 'sekretaris.ficus@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'dpk',
                'position' => 'Sekretaris DPK GMNI Ficus Benjamina UNISDA',
                'is_active' => 1
            ]);

            // DPK GMNI Brahmana
            $dpkBrahmanaUserId = $userModel->create([
                'komisariat_id' => $kBrahmanaId,
                'name' => 'Aqila Nur Aini',
                'email' => 'dpk.brahmana@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'dpk',
                'position' => 'Ketua DPK GMNI Brahmana UNISDA',
                'phone' => '081234567893',
                'is_active' => 1
            ]);
            $userModel->create([
                'komisariat_id' => $kBrahmanaId,
                'name' => 'Efandry Zagky Maryanu',
                'email' => 'sekretaris.brahmana@gmni-unisda.or.id',
                'password' => $defaultPassword,
                'role' => 'dpk',
                'position' => 'Sekretaris DPK GMNI Brahmana UNISDA',
                'is_active' => 1
            ]);
        } else {
            $ketua = $userModel->findByEmail('ketua.besar@gmni-unisda.or.id');
            $sekre = $userModel->findByEmail('sekretaris.besar@gmni-unisda.or.id');
            $dpkHattaUser = $userModel->findByEmail('dpk.hatta@gmni-unisda.or.id');
            
            $ketuaId = $ketua ? $ketua['id'] : 1;
            $sekreId = $sekre ? $sekre['id'] : 2;
            $dpkHattaUserId = $dpkHattaUser ? $dpkHattaUser['id'] : 3;
        }

        // 3. Seed Letter Categories
        if ($catModel->count() === 0) {
            $catModel->create(['code' => 'SK', 'name' => 'Surat Keputusan (SK Pengesahan / Penetapan)', 'description' => 'Pengesahan kepengurusan DPK, pelantikan, atau susunan panitia PPAB']);
            $catModel->create(['code' => 'REK', 'name' => 'Surat Rekomendasi Kegiatan', 'description' => 'Rekomendasi pelaksanaan kegiatan tingkat fakultas ke dekanat/eksternal']);
            $catModel->create(['code' => 'PENG', 'name' => 'Surat Pengantar & Dispensasi', 'description' => 'Surat pengantar izin kuliah atau dispensasi perkuliahan mahasiswa anggota GMNI']);
            $catModel->create(['code' => 'UND', 'name' => 'Surat Undangan', 'description' => 'Undangan rapat konsolidasi, pelantikan, diskusi marhaenisme, atau PPAB']);
            $catModel->create(['code' => 'EDR', 'name' => 'Surat Edaran / Instruksi', 'description' => 'Instruksi resmi Komisariat Besar ke seluruh DPK se-UNISDA']);
            $catModel->create(['code' => 'LPJ', 'name' => 'Laporan Pertanggungjawaban (LPJ)', 'description' => 'Penyerahan arsip laporan pelaksanaan kegiatan organisasi']);
            $catModel->create(['code' => 'MDT', 'name' => 'Surat Mandat Delegasi', 'description' => 'Pemberian mandat utusan kader mewakili agenda komisariat']);
        }
    }
}

