<?php
session_start();
if (!isset($_SESSION['id_konselor'])) die("Akses ditolak.");

$id = $_GET['id'] ?? null;

$data = json_decode(file_get_contents("http://localhost/lakoni_aja/api/monitoring/detail.php?id=$id"), true);
$rm = $data['data'] ?? null;

if (!$rm) die("Data tidak ditemukan.");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Rekam Medis</title>
    <link rel="stylesheet" href="css/rekam_medis.css">
</head>
<body>

<a href="rekam_medis.php" class="btn-back">← Kembali</a>

<div class="container">
    <div class="card">
        <div class="title">✏️ Edit Rekam Medis</div>

        <form action="api/monitoring/update.php" method="POST">

            <input type="hidden" name="id_monitoring" value="<?= $rm['id_monitoring'] ?>">

            <label class="label">Tanggal</label>
            <input type="date" name="tanggal" class="input" value="<?= $rm['tanggal'] ?>">

            <label class="label">Catatan Konseling</label>
            <textarea class="textarea" name="catatan"><?= $rm['catatan'] ?></textarea>

            <label class="label">Diagnosis</label>
            <input type="text" class="input" name="diagnosis" value="<?= $rm['diagnosis'] ?>">

            <label class="label">Rekomendasi</label>
            <textarea class="textarea" name="rekomendasi"><?= $rm['rekomendasi'] ?></textarea>

            <button class="btn-save">Simpan Perubahan</button>
        </form>
    </div>
</div>

</body>
</html>
