<?php
// views/agenda/print.php
use App\Helpers\View;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= View::e($title) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #111;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 12pt;
            margin: 4px 0;
            color: #8B0000;
        }
        .header p {
            font-size: 9pt;
            margin: 0;
            color: #444;
        }
        .report-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin: 20px 0 15px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .sig-box {
            float: right;
            width: 260px;
            text-align: center;
        }
        .sig-space {
            height: 70px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Print Action Banner (Hidden when printed) -->
    <div class="no-print" style="background: #8B0000; color: #fff; padding: 10px 15px; margin-bottom: 20px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
        <span><strong>Dokumen Rekapitulasi Agenda Surat GMNI UNISDA</strong></span>
        <button onclick="window.print()" style="background: #fff; color: #8B0000; border: none; font-weight: bold; padding: 6px 14px; border-radius: 4px; cursor: pointer;">
            🖨 Cetak Sekarang (Print / PDF)
        </button>
    </div>

    <!-- Official Header -->
    <div class="header">
        <h1>GERAKAN MAHASISWA NASIONAL INDONESIA (GMNI)</h1>
        <h2>DEWAN PIMPINAN KOMISARIAT BESAR GMNI UNISDA LAMONGAN</h2>
        <p>Sekretariat: Kampus Universitas Islam Darul 'Ulum Lamongan, Jawa Timur</p>
    </div>

    <div class="report-title">
        BUKU AGENDA SURAT <?= strtoupper($direction) ?> ORGANISASI
    </div>

    <p style="font-size: 9pt; margin-bottom: 10px;">
        Tanggal Dicetak: <?= date('d F Y') ?> | Total Arsip: <?= count($letters) ?> Dokumen
    </p>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 170px;">Nomor Surat</th>
                <th style="width: 80px;">Tanggal</th>
                <th><?= $direction === 'masuk' ? 'Pengirim (Asal DPK)' : 'Tujuan Surat' ?></th>
                <th>Perihal / Keterangan</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($letters)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Tidak ada surat tercatat.</td>
                </tr>
            <?php else: ?>
                <?php $idx = 1; foreach ($letters as $l): ?>
                    <tr>
                        <td style="text-align: center;"><?= $idx++ ?></td>
                        <td style="font-family: monospace; font-size: 9pt; font-weight: bold;"><?= View::e($l['reference_number'] ?: $l['tracking_code']) ?></td>
                        <td><?= View::formatDate($l['letter_date']) ?></td>
                        <td><?= View::e($direction === 'masuk' ? $l['sender'] : $l['recipient']) ?></td>
                        <td>
                            <strong><?= View::e($l['title']) ?></strong>
                        </td>
                        <td style="text-align: center; font-size: 9pt;"><?= strtoupper($l['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signatures">
        <div class="sig-box">
            <p>Lamongan, <?= date('d F Y') ?></p>
            <p style="margin-top: 5px;"><strong>Komisariat Besar GMNI UNISDA</strong></p>
            <div class="sig-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">( Pengurus Komisariat Besar )</p>
            <p style="font-size: 9pt; margin-top: 2px;">Terverifikasi Melalui SIMAS</p>
        </div>
    </div>

</body>
</html>

