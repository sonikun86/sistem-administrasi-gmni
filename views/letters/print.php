<?php
// views/letters/print.php
use App\Helpers\View;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= View::e($title) ?></title>
    <style>
        @page {
            size: A4;
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 18px;
            position: relative;
        }
        .kop-surat .bintang {
            font-size: 28pt;
            color: #8B0000;
            line-height: 1;
            margin-bottom: 2px;
        }
        .kop-surat h1 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .kop-surat h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 3px 0;
            color: #8B0000;
            text-transform: uppercase;
        }
        .kop-surat p {
            font-size: 9.5pt;
            margin: 0;
            color: #333;
            font-family: Arial, sans-serif;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11pt;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .salam {
            font-weight: bold;
            font-style: italic;
            margin: 12px 0 8px;
            color: #8B0000;
        }
        .isi-surat {
            text-align: justify;
            margin-bottom: 15px;
            font-size: 11.5pt;
        }
        .isi-surat p {
            margin: 8px 0;
            text-indent: 30px;
        }
        .isi-surat p.no-indent {
            text-indent: 0;
        }
        .kotak-keterangan {
            background-color: #f9f9f9;
            border-left: 3px solid #8B0000;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 11pt;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            text-align: center;
            font-size: 11pt;
        }
        .signature-table td {
            vertical-align: top;
            width: 50%;
            padding: 0 10px;
        }
        .sig-space {
            height: 60px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stempel-digital {
            display: inline-block;
            border: 2px solid #8B0000;
            color: #8B0000;
            font-weight: bold;
            font-size: 8pt;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qr-section {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 8.5pt;
            color: #555;
            font-family: Arial, sans-serif;
        }
        .no-print {
            background: #8B0000;
            color: #fff;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Print Banner (Hidden when printed) -->
    <div class="no-print">
        <span><strong>Dokumen Resmi GMNI UNISDA &bull; Siap Dicetak ke Kertas A4</strong></span>
        <div>
            <button onclick="window.history.back()" style="background: #eee; color: #333; border: none; font-weight: bold; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-right: 8px;">
                &larr; Kembali
            </button>
            <button onclick="window.print()" style="background: #fff; color: #8B0000; border: none; font-weight: bold; padding: 6px 14px; border-radius: 4px; cursor: pointer;">
                🖨 Cetak Sekarang (Print / PDF)
            </button>
        </div>
    </div>

    <!-- Kop Surat Resmi GMNI UNISDA -->
    <div class="kop-surat">
        <div class="bintang">★</div>
        <h1>GERAKAN MAHASISWA NASIONAL INDONESIA</h1>
        <h2>KOMISARIAT BESAR GMNI UNISDA LAMONGAN</h2>
        <p>Sekretariat: Kampus Universitas Islam Darul 'Ulum Lamongan, Jl. Airlangga No. 03 Sukodadi, Lamongan, Jawa Timur</p>
    </div>

    <!-- Letter Metadata -->
    <table class="meta-table">
        <tr>
            <td style="width: 90px;">Nomor</td>
            <td style="width: 15px;">:</td>
            <td style="font-weight: bold; font-family: Arial, sans-serif;"><?= View::e($letter['reference_number'] ?: $letter['tracking_code']) ?></td>
            <td style="text-align: right;">Lamongan, <?= View::formatDate($letter['letter_date']) ?></td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td><?= !empty($letter['attachment_path']) ? '1 (Satu) Berkas Lampiran' : '-' ?></td>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td colspan="2" style="font-weight: bold; text-decoration: underline;">
                <?= View::e($letter['title']) ?>
            </td>
        </tr>
    </table>

    <!-- Recipient -->
    <div style="margin: 15px 0 10px; font-size: 11pt;">
        Kepada Yth.<br>
        <strong><?= View::e($letter['recipient']) ?></strong><br>
        di Tempat
    </div>

    <!-- Salam Pembuka -->
    <div class="salam">Merdeka!</div>

    <!-- Isi Surat -->
    <div class="isi-surat">
        <p>
            Salam pejuang pemikir - pemikir pejuang. Teriring salam dan doa semoga seluruh jajaran kader Gerakan Mahasiswa Nasional Indonesia senantiasa dalam lindungan Tuhan Yang Maha Esa dan tetap teguh dalam garis perjuangan kaum Marhaen.
        </p>

        <p>
            Sehubungan dengan permohonan dan koordinasi administrasi organisasi di lingkungan Komisariat Besar GMNI Universitas Islam Darul 'Ulum (UNISDA) Lamongan bersama Dewan Pengurus Komisariat (DPK) tingkat fakultas, bersama ini kami sampaikan hal-hal sebagai berikut:
        </p>

        <div class="kotak-keterangan">
            <strong>Keterangan / Uraian:</strong><br>
            <?= nl2br(View::e($letter['description'] ?: 'Tidak ada uraian tambahan.')) ?>
        </div>

        <p>
            Demikian surat ini kami sampaikan secara resmi melalui Sistem Informasi Manajemen Administrasi Surat (SIMAS) Komisariat Besar GMNI UNISDA. Atas perhatian, dukungan, dan kerja sama yang baik, kami haturkan terima kasih.
        </p>
    </div>

    <!-- Salam Penutup -->
    <div class="salam">GMNI Jaya! Marhaen Menang!</div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <p>Mengetahui / Mengajukan,<br><strong><?= View::e($letter['sender']) ?></strong></p>
                <div class="sig-space">
                    <span class="stempel-digital">TERVERIFIKASI SISTEM</span>
                </div>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">
                    <?= View::e($letter['creator_name'] ?? 'Pengurus DPK') ?>
                </p>
                <p style="font-size: 9pt; color: #555; margin: 0;">Pengurus DPK Fakultas</p>
            </td>

            <td>
                <p>Mengesahkan,<br><strong>Komisariat Besar GMNI UNISDA</strong></p>
                <div class="sig-space">
                    <?php if (in_array($letter['status'], ['disetujui', 'selesai'])): ?>
                        <span class="stempel-digital" style="border-color: #047857; color: #047857;">
                            ★ DISAHKAN RESMI DIGITAL
                        </span>
                    <?php else: ?>
                        <span style="font-size: 9pt; color: #888; font-style: italic;">(Menunggu Pengesahan)</span>
                    <?php endif; ?>
                </div>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">
                    <?= View::e($letter['approver_name'] ?? 'Bung Irgi Alamsyah Ali Alhana') ?>
                </p>
                <p style="font-size: 9pt; color: #555; margin: 0;">Ketua Komisariat Besar</p>
            </td>
        </tr>
    </table>

    <!-- QR Code Authenticity Section at footer -->
    <div class="qr-section">
        <div>
            <strong>Verifikasi Keaslian Dokumen:</strong><br>
            Surat ini diterbitkan dan tercatat sah di SIMAS GMNI UNISDA.<br>
            Pindai kode QR di samping atau kunjungi tautan verifikasi resmi:<br>
            <span style="font-family: monospace; font-size: 8pt; color: #8B0000;"><?= View::e($verifUrl) ?></span>
        </div>
        <div>
            <?= $qrSvg ?>
        </div>
    </div>

</body>
</html>

