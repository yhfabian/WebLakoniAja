<?php
session_start();
if (!isset($_SESSION['id_konselor'])) {
    die("Akses ditolak. Harap login sebagai konselor.");
}

$id_konselor = $_SESSION['id_konselor'];
$id_monitoring = $_GET['id'] ?? null;
if (!$id_monitoring) {
    die("ID Rekam Medis tidak ditemukan.");
}
$api_url = "http://127.0.0.1/weblakoniaja/api/monitoring/detail.php?id=" . $id_monitoring;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $api_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);


if (!$data || $data['status'] !== true) {
    die("Data rekam medis tidak ditemukan melalui API.");
}

$m = $data['data'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Rekam Medis</title>
<link rel="stylesheet" href="assets/css/rekam_medis.css?v=<?php echo time(); ?>">
<style>
.edit-container {
    max-width: 700px;
    margin: 30px auto;
}

.edit-card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}

.label {
    font-weight: 600;
    color: #0b3d91;
    margin-top: 12px;
    display: block;
}

.input, .textarea {
    width: 100%;
    padding: 10px;
    border-radius: 12px;
    border: 1px solid #cfe0f5;
    margin-top: 5px;
    background: #f8fbff;
}

.textarea {
    height: 110px;
}

.btn-submit {
    margin-top: 18px;
    background: #0095ff;
    color: white;
    border: none;
    padding: 12px 18px;
    font-size: 15px;
    border-radius: 12px;
    cursor: pointer;
}

.btn-back {
    background: #ddd;
    margin-left: 10px;
    padding: 12px 18px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    color: black;
}
</style>
</head>
<body>

<div class="edit-container">
    
    <div class="edit-card">

        <h2 style="color:#1c6dd0; margin-bottom:10px;">✏ Edit Rekam Medis</h2>
        <small>ID Monitoring: RM-<?= $m['id_monitoring'] ?></small><br>
        <small>Mahasiswa: <b><?= $m['nama_user'] ?></b></small><br><br>

        <form action="api/monitoring/update.php" method="POST">

            <input type="hidden" name="id_monitoring" value="<?= $m['id_monitoring'] ?>">

            <label class="label">Tanggal</label>
            <input type="date" class="input" name="tanggal" value="<?= $m['tanggal'] ?>" required>

           
            <label class="label">Catatan Konseling</label>
            <textarea class="textarea" name="catatan" required><?= $m['catatan'] ?></textarea>

            <label class="label">Diagnosis</label>
            <input type="text" class="input" name="diagnosis" value="<?= $m['diagnosis'] ?>">

 
            <label class="label">Rekomendasi</label>
            <textarea class="textarea" name="rekomendasi"><?= $m['rekomendasi'] ?></textarea>

            <button type="submit" class="btn-submit">💾 Simpan Perubahan</button>
            <a href="rekam_medis.php" class="btn-back">Kembali</a>
        </form>
    </div>
</div>

</body>
</html>
