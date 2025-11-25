<?php
session_start();
if (!isset($_SESSION['id_konselor'])) {
    die("Akses ditolak. Harap login sebagai konselor.");
}

$id_konselor = $_SESSION['id_konselor'];

// --- API GET MONITORING ---
$api_url = "http://localhost/lakoni_aja/api/monitoring/list.php?id_konselor=" . $id_konselor;
$response = file_get_contents($api_url);
$data = json_decode($response, true);

$monitoring = $data['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekam Medis Konselor</title>
<link rel="stylesheet" href="assets/css/rekam_medis.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="container">
    <h1>Rekam Medis Konselor</h1>
    <p class="subtitle">Sistem pengarsipan data konseling mahasiswa</p>

    <div class="grid">

        <!-- LEFT SECTION : LIST -->
        <div class="card">
            <div class="title">📁 Daftar Rekam Medis</div>

            <div class="patient-list">

                <?php if (empty($monitoring)): ?>
                    <p>Tidak ada data rekam medis.</p>
                <?php endif; ?>

                <?php foreach ($monitoring as $m): ?>
                    <a href="rm_detail.php?id=<?= $m['id_monitoring'] ?>" style="text-decoration:none;color:inherit;">
                        <div class="patient-item">
                            <strong><?= $m['nama_user'] ?></strong>

                            <small>
                                NIM : <?= $m['nim_user'] ?> • 
                                Kelas : <?= $m['kelas_user'] ?>
                            </small>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>

            <button class="btn-add" onclick="openPopup()">+</button>
        </div>

        <!-- RIGHT SECTION : PLACEHOLDER -->
        <div class="card">
            <div class="title">📄 Detail Rekam Medis</div>
            <p>Pilih salah satu rekam medis dari kiri untuk melihat detail.</p>
        </div>

    </div>
</div>


<!-- POPUP TAMBAH REKAM MEDIS -->
<div class="popup" id="popup">
    <div class="popup-content">
        <h3>Tambah Rekam Medis</h3>

        <form action="api/monitoring/add.php" method="POST">

            <input type="hidden" name="id_konselor" value="<?= $id_konselor ?>">

            <!-- PILIH USER -->
            <label class="label">Pilih Mahasiswa</label>
            <select class="input" name="id_user" required>
                <option value="">-- Pilih Mahasiswa --</option>

                <?php
                $users = file_get_contents("http://localhost/lakoni_aja/api/monitoring/user_list.php");
                $users = json_decode($users, true)['data'] ?? [];

                foreach ($users as $u):
                ?>
                    <option value="<?= $u['id_user'] ?>">
                        <?= $u['nama'] ?> (<?= $u['nim'] ?> - <?= $u['kelas'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- TANGGAL -->
            <label class="label">Tanggal</label>
            <input type="date" class="input" name="tanggal" required>

            <!-- CATATAN -->
            <label class="label">Catatan Konseling</label>
            <textarea class="textarea" name="catatan" required></textarea>

            <!-- DIAGNOSIS -->
            <label class="label">Diagnosis</label>
            <input type="text" class="input" name="diagnosis">

            <!-- REKOMENDASI -->
            <label class="label">Rekomendasi</label>
            <textarea class="textarea" name="rekomendasi"></textarea>

            <div class="btn-group">
                <button type="submit" class="btn-save">Simpan</button>
                <button type="button" class="btn-cancel" onclick="closePopup()">Batal</button>
            </div>
        </form>
    </div>
</div>


<script>
function openPopup() {
    document.getElementById("popup").style.display = "flex";
}
function closePopup() {
    document.getElementById("popup").style.display = "none";
}
</script>

</body>
</html>
