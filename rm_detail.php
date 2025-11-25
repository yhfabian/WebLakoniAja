<?php
session_start();
if (!isset($_SESSION['id_konselor'])) {
    die("Akses ditolak. Harap login sebagai konselor.");
}

$id_monitoring = $_GET['id'] ?? null;
if (!$id_monitoring) {
    die("ID Monitoring tidak ditemukan.");
}

// Ambil data via API
$api = "http://localhost/lakoni_aja/api/monitoring/detail.php?id=" . $id_monitoring;
$response = file_get_contents($api);
$data = json_decode($response, true);

if (!$data || $data['status'] !== "success") {
    die("Data rekam medis tidak ditemukan.");
}

$m = $data['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Detail Rekam Medis</title>
<link rel="stylesheet" href="assets/css/rekam_medis.css">
</head>

<body>

<a href="rekam_medis.php" class="btn-back">← Kembali</a>

<div class="container">

    <div class="card">
        <div class="title">📄 Detail Rekam Medis</div>

        <h2 class="patient-name"><?= $m['nama_user'] ?></h2>
        <small>ID Monitoring: RM-<?= $m['id_monitoring'] ?></small><br>
        <small>NIM: <?= $m['nim_user'] ?></small><br>
        <small>Kelas: <?= $m['kelas_user'] ?></small><br>

        <div class="info-box">
            <strong>Konselor:</strong> <?= $m['nama_konselor'] ?>
        </div>

        <div class="info-box">
            <strong>Tanggal Konseling:</strong> <?= $m['tanggal'] ?>
        </div>

        <h3 style="color:#1c6dd0;">Catatan Konseling</h3>
        <p><?= nl2br($m['catatan']) ?></p>

        <h3 style="color:#1c6dd0;">Diagnosis</h3>
        <p><?= nl2br($m['diagnosis']) ?></p>

        <h3 style="color:#1c6dd0;">Rekomendasi</h3>
        <p><?= nl2br($m['rekomendasi']) ?></p>

        <hr class="divider">

        <a href="rm_pdf.php?id=<?= $m['id_monitoring'] ?>" class="btn-export">📄 Export PDF</a>
    </div>

</div>

</body>
</html>
