# SIMAS GMNI UNISDA (Sistem Informasi Manajemen Administrasi Surat)

> **Komisariat Besar GMNI UNISDA & 3 DPK Tingkat Fakultas (DPK Hatta, DPK Ficus Benjamina, DPK Brahmana)**  
> *"Pejuang Pemikir - Pemikir Pejuang"* &bull; *"GMNI Jaya! Marhaen Menang!"*

---

## 📌 Latar Belakang & Solusi

Sistem ini dibangun untuk menyelesaikan permasalahan birokrasi dan korespondensi di lingkungan **Komisariat Besar GMNI UNISDA** (Universitas Islam Darul 'Ulum Lamongan) dengan 3 komisariat tingkat fakultas di bawah naungannya:
1. **DPK GMNI Hatta** (Fakultas Hukum & FISIP)
2. **DPK GMNI Ficus Benjamina** (Fakultas Pertanian, Peternakan & Sains Teknologi/MIPA)
3. **DPK GMNI Brahmana** (Fakultas Agama Islam & FKIP)

### Masalah yang Diselesaikan:
- **Menghilangkan ketergantungan tatap muka fisik**: Pengurus DPK tidak perlu lagi membuang waktu dan menunda kegiatan organisasi hanya untuk menemui pengurus Komisariat Besar secara langsung demi meminta nomor surat, verifikasi berkas, surat rekomendasi, atau tanda tangan pengesahan SK.
- **Transparansi status surat (*Realtime Tracking*)**: DPK dapat langsung mengetahui posisi surat mereka (*Diajukan ➔ Diverifikasi Sekretaris ➔ Disahkan Ketua / Perlu Revisi*).
- **Keabsahan Dokumen Digital (QR Code Verification)**: Setiap surat yang disahkan dilengkapi QR Code verifikasi mandiri yang dapat di-scan secara publik oleh pihak birokrasi kampus atau dekanat.
- **Penomoran Otomatis & Buku Agenda**: Mencegah nomor surat ganda atau loncat, serta memudahkan pembuatan rekap agenda surat untuk LPJ kepengurusan.

---

## 🚀 Cara Menjalankan di Komputer Lokal

1. Buka terminal pada folder proyek ini:
   ```bash
   cd /home/laksana/Documents/sistem-manajemen-surat
   ```
2. Jalankan server web bawaan PHP:
   ```bash
   php -S localhost:8000 server.php
   ```
3. Buka browser dan akses:
   ```
   http://localhost:8000
   ```

---

## 🔑 Akun & Kredensial Pengguna

Semua akun di bawah ini menggunakan kata sandi default: **`password123`**  
*(Disediakan juga tombol **Simulasi Akun Cepat (1-Klik)** di bagian atas antarmuka dan halaman login)*:

| Peran / Jabatan | Email Akun | Deskripsi Wewenang |
| :--- | :--- | :--- |
| **Ketua Komisariat Besar** | `ketua.besar@gmni-unisda.or.id` | Pengesahan akhir, persetujuan SK/Rekomendasi, dan pengisian Lembar Disposisi |
| **Sekretaris Besar** | `sekretaris.besar@gmni-unisda.or.id` | Verifikasi kelengkapan berkas, alokasi nomor surat resmi GMNI, pengembalian revisi |
| **Pengurus DPK GMNI Hatta** | `dpk.hatta@gmni-unisda.or.id` | Pengajuan surat, tracking status, dan download surat resmi DPK Hatta |
| **Pengurus DPK GMNI Ficus Benjamina** | `dpk.ficus@gmni-unisda.or.id` | Pengajuan surat, tracking status, dan download surat resmi DPK Ficus |
| **Pengurus DPK GMNI Brahmana** | `dpk.brahmana@gmni-unisda.or.id` | Pengajuan surat, tracking status, dan download surat resmi DPK Brahmana |

---

## 🔄 Alur Kerja Surat (Digital Workflow)

1. **DPK Mengajukan Surat Online**:
   - DPK masuk ke menu **Ajukan Surat Baru**, mengisi perihal, memilih kategori (Rekomendasi / SK / Pengantar / Undangan), dan mengunggah berkas draf / proposal (PDF/DOCX).
   - Status awal: `🟡 Diajukan`.
2. **Sekretariat Besar Memverifikasi Berkas**:
   - Sekretaris membuka **Meja Verifikasi & Disposisi**.
   - Jika berkas belum lengkap: Klik **Minta Revisi** dan berikan catatan perbaikan. Status: `🟠 Perlu Revisi`.
   - Jika berkas valid: Klik **Verifikasi & Alokasikan No. Surat**. Sistem akan meng-generate nomor surat resmi sesuai format organisasi. Status: `🔵 Diverifikasi`.
3. **Ketua Besar Memberikan Pengesahan & Disposisi**:
   - Ketua membuka berkas yang telah diverifikasi, memilih instruksi disposisi, dan klik **Sahkan & Setujui**.
   - Status menjadi: `🟢 Disetujui / Selesai`.
4. **Unduh Dokumen & Verifikasi Publik**:
   - DPK langsung dapat mengunduh dokumen resmi bertanda tangan digital.
   - Dokumen memiliki **QR Code Verifikasi**. Pihak kampus atau pihak luar yang memindai QR Code akan diarahkan ke halaman validasi publik (`/verifikasi?token=...`) yang mengonfirmasi keaslian dokumen.

---

## 💾 Ketahanan SQLite & Panduan Hosting (cPanel / VPS)

### Apakah SQLite Mampu Menampung Sistem Ini di Hosting?
**Ya, sangat mampu dan sangat direkomendasikan!**
- **Kapasitas**: SQLite secara teknis mampu menampung database hingga **281 Terabyte** dan menangani puluhan hingga ratusan transaksi per detik.
- **Beban Organisasi**: Sistem persuratan Komisariat Besar dan 3 DPK fakultas umumnya memproses puluhan hingga ribuan surat per tahun dengan puluhan pengurus aktif. Beban ini menggunakan **kurang dari 1%** kapasitas maksimal SQLite.
- **Keuntungan Besar SQLite di Shared Hosting / cPanel**:
  - **Zero Maintenance**: Tidak perlu membuat database user, privileges, atau setting port MySQL di cPanel.
  - **Backup Sangat Mudah**: Seluruh database tersimpan rapi dalam satu file (`database/database.sqlite`). Untuk backup tahunan saat LPJ, Anda cukup menyalin file tersebut!
  - **Performa Cepat**: Akses disk lokal tanpa latensi koneksi jaringan MySQL.

### Cara Upload ke Shared Hosting cPanel:
1. Kompres seluruh folder proyek menjadi file `.zip`.
2. Di cPanel File Manager, unggah dan ekstrak ke direktori hosting Anda (misal `public_html` atau subdomain `gmni.unisda.ac.id`).
3. Pastikan folder `database/`, `storage/`, dan `public/uploads/` memiliki izin tulis (*permission 755* atau *777*).
4. Selesai! Aplikasi langsung dapat diakses tanpa perlu konfigurasi server tambahan.

### Opsi Beralih ke MySQL (Jika Kampus Mengharuskan MySQL):
Sistem ini menggunakan arsitektur **PDO Database Agnostik** yang mendukung dual-driver (SQLite & MySQL). Jika di masa mendatang Anda ingin memakai MySQL, cukup buka file `.env` dan ubah:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_mysql_anda
DB_USERNAME=user_mysql_anda
DB_PASSWORD=password_mysql_anda
```
Aplikasi akan secara otomatis membuat seluruh tabel dan data di MySQL tanpa perlu mengubah baris kode program satu pun!

---

## 🛠️ Struktur Direktori

```
sistem-manajemen-surat/
├── app/
│   ├── Config/          # Konfigurasi aplikasi & database (SQLite & MySQL)
│   ├── Controllers/     # Logika alur surat, verifikasi, agenda, dan DPK
│   ├── Database/        # Migrasi tabel & seeder data awal organisasi
│   ├── Helpers/         # Autentikasi sesi, token CSRF, QR Code SVG, formatting
│   └── Models/          # Abstraksi data PDO (Surat, Pengguna, DPK, Disposisi)
├── database/
│   └── database.sqlite  # Berkas database SQLite
├── public/
│   ├── index.php        # Front controller & routing
│   ├── .htaccess        # URL rewrite rules
│   └── uploads/         # Direktori penyimpanan berkas draf & lampiran
├── storage/
│   └── sessions/        # Penyimpanan sesi lokal
├── views/
│   ├── agenda/          # Tampilan buku agenda & cetak rekapitulasi
│   ├── approvals/       # Meja kerja Sekretaris & Ketua Komisariat Besar
│   ├── auth/            # Halaman login & pemilih peran
│   ├── dashboard/       # Dashboard metrik & status 3 DPK
│   ├── dpk/             # Profil & manajemen DPK se-UNISDA
│   ├── layouts/         # Layout responsif bertema GMNI
│   ├── letters/         # Tampilan pengajuan, daftar, & pratinjau surat
│   └── verification/    # Sertifikat verifikasi publik via scan QR
├── server.php           # Runner web server lokal
├── .env                 # Konfigurasi lingkungan aktif
└── README.md
```

