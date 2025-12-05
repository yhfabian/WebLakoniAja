<?php
session_start();
if (!isset($_SESSION['id_konselor'])) {
    die("Akses ditolak. Harap login sebagai konselor.");
}

$id_konselor = $_SESSION['id_konselor'];

// --- Ambil ID Monitoring dari URL ---
$id_monitoring = $_GET['id'] ?? null;
if (!$id_monitoring) {
    die("ID Monitoring tidak ditemukan.");
}

// --- Ambil Data Monitoring via API ---
$api = "http://localhost/weblakoniaja/api/monitoring/detail.php?id=" . $id_monitoring;
$response = file_get_contents($api);
$data = json_decode($response, true);

if (!$data || !$data['status']) {
    die("Data rekam medis tidak ditemukan.");
}

$m = $data['data'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Rekam Medis</title>
<link rel="stylesheet" href="assets/css/rm_detail.css?v=<?php echo time(); ?>">
</head>

<body>

<a href="rekam_medis.php" class="btn-back">← Kembali</a>

<div class="container">

    <div class="card">
        <div class="title">📄 Detail Rekam Medis</div>

        <div class="box">
            <h3> Data Mahasiswa</h3>
            <p><strong>Nama:</strong> <?= htmlspecialchars($m['nama_user']) ?></p>
            <p><strong>NIM:</strong> <?= $m['nim_user'] ?></p>
            <p><strong>Email:</strong> <?= $m['email_user'] ?></p>
        </div>

        <div class="box">
            <h3> Konselor</h3>
            <p><strong>Nama Konselor:</strong> <?= $m['nama_konselor'] ?></p>
            <p><strong>Tanggal Konseling:</strong> <?= $m['tanggal'] ?></p>
        </div>

        <div class="box">
            <h3> Catatan Konseling</h3>
            <p><?= nl2br($m['catatan']) ?></p>
        </div>

        <div class="box">
            <h3> Diagnosis</h3>
            <p><?= nl2br($m['diagnosis']) ?></p>
        </div>

        <div class="box">
            <h3> Rekomendasi</h3>
            <p><?= nl2br($m['rekomendasi']) ?></p>
        </div>

       <div class="action-area">

        <a href="#" class="btn-edit" onclick="openEditPopup()"> Edit</a>

        <a href="api/monitoring/delete.php?id=<?= $m['id_monitoring'] ?>" 
       class="btn-delete" 
       onclick="return confirm('Yakin ingin menghapus rekam medis ini?')">
        Hapus
        </a>

        <a href="rm_pdf.php?id=<?= $m['id_monitoring'] ?>" class="btn-pdf"> Export PDF</a>
        </div>

    </div>
    <!-- POPUP EDIT REKAM MEDIS -->
    <div class="popup" id="popupEdit">
    <div class="popup-content">
        <h3>Edit Rekam Medis</h3>

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

            <div class="btn-group">
                <button type="submit" class="btn-save">Simpan Perubahan</button>
                <button type="button" class="btn-cancel" onclick="closeEditPopup()">Batal</button>
            </div>
        </form>
    </div>
</div>

</div>
<script>
function openEditPopup() {
    document.getElementById("popupEdit").style.display = "flex";
}
function closeEditPopup() {
    document.getElementById("popupEdit").style.display = "none";
}
</script>

</body>
</html>
