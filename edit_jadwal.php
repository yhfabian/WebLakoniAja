<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

$id_jadwal = $_GET['id'] ?? 0;

// Ambil data jadwal
$stmt = mysqli_prepare($conn, "SELECT * FROM jadwal WHERE id_jadwal = ?");
mysqli_stmt_bind_param($stmt, "i", $id_jadwal);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jadwal = mysqli_fetch_assoc($result);

// Ambil daftar konselor
$listKonselor = mysqli_query($conn, "SELECT id_konselor, nama FROM konselor");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Jadwal</title>
    <link rel="stylesheet" href="assets/css/edit_jadwal.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="sidebar">
  <div class="list_sidebar">
    <h2>Dashboard Admin</h2>
    <a href="dashboard_admin.php">📊 Dashboard</a>
    <a href="kelola_user.php">👤 Kelola User</a>
    <a href="kelola_konselor.php">🧑‍⚕️ Kelola Konselor</a>
    <a href="kelola_jadwal.php" class="active">📅 Kelola Jadwal</a>
  </div>
  <div class="keluar">
        <a class="logout" href="logout_admin.php">Logout</a>
    </div>
</div>

<div class="content">

    <!-- HEADER -->
    <div class="main-header">
        <h1 class="brand-title">Lakoni Aja</h1>
        <div class="profile-section">
            <span class="profile-name">Admin Petugas</span>
            <div class="profile-avatar">A</div>
        </div>
    </div>

    <hr class="header-line">

    <h2>Edit Jadwal</h2>

    <!-- FORM WRAPPER -->
    <div class="form-box">

        <form action="update_jadwal.php" method="POST">

            <input type="hidden" name="id_jadwal" value="<?= $jadwal['id_jadwal'] ?>">

            <label>Konselor</label>
            <select name="id_konselor" required>
                <?php while ($k = mysqli_fetch_assoc($listKonselor)) { ?>
                    <option value="<?= $k['id_konselor'] ?>"
                        <?= $k['id_konselor'] == $jadwal['id_konselor'] ? 'selected' : '' ?>>
                        <?= $k['nama'] ?>
                    </option>
                <?php } ?>
            </select>

            <label>Tanggal Konseling</label>
            <input type="date" name="tanggal" value="<?= $jadwal['tanggal'] ?>" required>

            <label>Jam Mulai</label>
            <input type="time" name="jam_mulai" value="<?= $jadwal['jam_mulai'] ?>" required>

            <label>Jam Selesai</label>
            <input type="time" name="jam_selesai" value="<?= $jadwal['jam_selesai'] ?>" required>

            <label>Status</label>
            <select name="status">
                <option value="Tersedia" <?= $jadwal['status'] == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                <option value="Dipesan"  <?= $jadwal['status'] == 'Dipesan'  ? 'selected' : '' ?>>Dipesan</option>
            </select>

            <div class="btn-group">
                <button type="submit" class="btn-save">Simpan Perubahan</button>
                <a href="kelola_jadwal.php" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>

</div>


</body>
</html>
