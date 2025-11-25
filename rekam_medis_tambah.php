<?php
// Ambil ID monitoring jika ada (opsional)
$id_monitoring = $_GET['id_monitoring'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Rekam Medis</title>
<link rel="stylesheet" href="assets/css/rekam_medis.css?v=<?php echo time(); ?>">
<style>
.form-card {
    background: white;
    padding: 30px;
    border-radius: 18px;
    max-width: 700px;
    margin: auto;
    border: 1px solid #d8ecff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.form-card h2 {
    color: #1c6dd0;
    margin-bottom: 20px;
}

.btn-submit {
    background: #1c6dd0;
    color: white;
    padding: 10px 18px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    margin-top: 20px;
}

.btn-submit:hover {
    background: #0f4da0;
}
</style>
</head>

<body>

<a href="rekam_medis.php" class="btn-back">← Kembali</a>

<div class="container">

    <div class="form-card">
        <h2>Tambah Rekam Medis Baru</h2>

        <form action="rekam_medis_action.php" method="POST">

            <?php if ($id_monitoring): ?>
                <input type="hidden" name="id_monitoring" value="<?= $id_monitoring ?>">
                <p><strong>ID Monitoring:</strong> <?= $id_monitoring ?></p>
            <?php endif; ?>

            <label class="label">Nama Pasien</label>
            <input type="text" class="input" name="nama_user" required>

            <label class="label">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="input" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <label class="label">Kontak</label>
            <input type="text" class="input" name="kontak" required>

            <label class="label">Usia</label>
            <input type="number" class="input" name="usia" required>

            <label class="label">Nama Konselor</label>
            <input type="text" class="input" name="nama_konselor" required>

            <label class="label">Tanggal Kunjungan</label>
            <input type="date" class="input" name="tgl_terakhir" required>

            <label class="label">Catatan Konseling</label>
            <textarea class="textarea" name="catatan" required></textarea>

            <label class="label">Diagnosis</label>
            <textarea class="textarea" name="diagnosis" required></textarea>

            <label class="label">Rekomendasi</label>
            <textarea class="textarea" name="rekomendasi" required></textarea>

            <button class="btn-submit" type="submit">Simpan Rekam Medis</button>
        </form>
    </div>

</div>

</body>
</html>
