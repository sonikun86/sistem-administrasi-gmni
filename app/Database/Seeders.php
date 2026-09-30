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

        // 4. Seed Sample Letters
        if ($letterModel->count() === 0) {
            $catRek = $catModel->findBy('code', 'REK');
            $catSk = $catModel->findBy('code', 'SK');
            $catEdr = $catModel->findBy('code', 'EDR');

            $rekId = $catRek ? $catRek['id'] : 2;
            $skId = $catSk ? $catSk['id'] : 1;
            $edrId = $catEdr ? $catEdr['id'] : 5;

            // Surat 1: Permohonan Rekomendasi dari DPK Hatta (Status: Disetujui)
            $token1 = bin2hex(random_bytes(16));
            $l1 = $letterModel->create([
                'tracking_code' => 'SRT-' . date('Ymd') . '-001',
                'reference_number' => '001/REK/DPK-GMNI-HATTA/UNISDA/IX/2026',
                'category_id' => $rekId,
                'komisariat_id' => $kHattaId,
                'direction' => 'masuk',
                'sender' => 'DPK GMNI Hatta UNISDA',
                'recipient' => 'Komisariat Besar GMNI UNISDA',
                'title' => 'Permohonan Rekomendasi Kegiatan Diskusi Hukum Progresif Mahasiswa',
                'description' => 'Permohonan rekomendasi dan pengantar kegiatan diskusi publik bertema "Hukum Progresif dalam Mengawal Hak Marhaen" bertempat di Auditorium Kampus UNISDA.',
                'letter_date' => date('Y-m-d', strtotime('-2 days')),
                'attachment_path' => null,
                'status' => 'disetujui',
                'verification_token' => $token1,
                'created_by' => $dpkHattaUserId,
                'approved_by' => $ketuaId,
                'approved_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
            ]);

            LetterTimeline::log($l1, $dpkHattaUserId, 'Pengajuan Surat Dibuat', 'diajukan', 'Draf surat dan proposal kegiatan diunggah oleh DPK Hatta.');
            LetterTimeline::log($l1, $sekreId, 'Verifikasi Berkas Administrasi', 'diverifikasi', 'Berkas lengkap dan memenuhi syarat administrasi organisasi.');
            LetterTimeline::log($l1, $ketuaId, 'Pengesahan & Persetujuan Ketua', 'disetujui', 'Disetujui. Surat Rekomendasi resmi diterbitkan dan siap diunduh.');

            // Surat 2: Pengajuan SK Panitia PPAB dari DPK Ficus Benjamina (Status: Diverifikasi / Menunggu Persetujuan Ketua)
            $token2 = bin2hex(random_bytes(16));
            $l2 = $letterModel->create([
                'tracking_code' => 'SRT-' . date('Ymd') . '-002',
                'reference_number' => null,
                'category_id' => $skId,
                'komisariat_id' => $kFicusId,
                'direction' => 'masuk',
                'sender' => 'DPK GMNI Ficus Benjamina',
                'recipient' => 'Komisariat Besar GMNI UNISDA',
                'title' => 'Permohonan Penerbitan SK Panitia Pekan Penerimaan Anggota Baru (PPAB) 2026',
                'description' => 'Mengajukan pengesahan susunan kepanitiaan PPAB DPK Ficus Benjamina tahun akademik 2026/2027.',
                'letter_date' => date('Y-m-d'),
                'attachment_path' => null,
                'status' => 'diverifikasi',
                'verification_token' => $token2,
                'created_by' => $dpkHattaUserId, // placeholder user
            ]);

            LetterTimeline::log($l2, $dpkHattaUserId, 'Pengajuan Surat Dibuat', 'diajukan', 'Pengajuan berkas SK diajukan secara online.');
            LetterTimeline::log($l2, $sekreId, 'Diverifikasi Sekretariat', 'diverifikasi', 'Susunan panitia dan kelengkapan administrasi telah divalidasi Sekretaris Besar. Diteruskan ke Ketua.');

            // Surat 3: Surat Instruksi Resmi dari Komisariat Besar ke Seluruh DPK (Surat Keluar)
            $token3 = bin2hex(random_bytes(16));
            $l3 = $letterModel->create([
                'tracking_code' => 'SRT-' . date('Ymd') . '-003',
                'reference_number' => '002/EDR/KOM-BESAR/GMNI-UNISDA/IX/2026',
                'category_id' => $edrId,
                'komisariat_id' => $kBesarId,
                'direction' => 'keluar',
                'sender' => 'Komisariat Besar GMNI UNISDA',
                'recipient' => 'Seluruh DPK GMNI se-UNISDA (Hatta, Ficus, Brahmana)',
                'title' => 'Instruksi Pelaksanaan Masa Kaderisasi dan Penertiban Administrasi Organisasi',
                'description' => 'Instruksi serentak kepada jajaran DPK untuk menyelaraskan kurikulum kaderisasi PPAB dan pelaporan data anggota baru ke Komisariat Besar.',
                'letter_date' => date('Y-m-d'),
                'attachment_path' => null,
                'status' => 'selesai',
                'verification_token' => $token3,
                'created_by' => $sekreId,
                'approved_by' => $ketuaId,
                'approved_at' => date('Y-m-d H:i:s'),
            ]);

            LetterTimeline::log($l3, $sekreId, 'Penerbitan Draf Instruksi', 'draft', 'Draf dibuat oleh Sekretaris Komisariat Besar.');
            LetterTimeline::log($l3, $ketuaId, 'Pengesahan & Terbit', 'selesai', 'Ditandatangani digital oleh Ketua Komisariat Besar dan didistribusikan ke DPK.');
        }
    }
}

