<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

$id_jadwal = $_GET['id'] ?? null;

if (!$id_jadwal) {
    header("Location: kelola_jadwal.php");
    exit();
}

// Ambil data jadwal berdasarkan ID
$stmt = mysqli_prepare($conn, "
    SELECT j.*, k.nama AS nama_konselor 
    FROM jadwal j 
    JOIN konselor k ON j.id_konselor = k.id_konselor 
    WHERE j.id_jadwal = ?
");
mysqli_stmt_bind_param($stmt, "i", $id_jadwal);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jadwal = mysqli_fetch_assoc($result);

if (!$jadwal) {
    echo "<script>alert('Jadwal tidak ditemukan!'); window.location='kelola_jadwal.php';</script>";
    exit();
}

$success = $error = "";

// Update data jadwal
if (isset($_POST['update'])) {
    $tanggal     = $_POST['tanggal'];
    $jam_mulai   = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $status      = $_POST['status'];

    if (empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
        $error = "Semua field wajib diisi.";
    } else {
        $stmt = mysqli_prepare($conn, "
            UPDATE jadwal 
            SET tanggal=?, jam_mulai=?, jam_selesai=?, status=? 
            WHERE id_jadwal=?
        ");
        mysqli_stmt_bind_param($stmt, "ssssi", $tanggal, $jam_mulai, $jam_selesai, $status, $id_jadwal);
        $updated = mysqli_stmt_execute($stmt);

        if ($updated) {
            $_SESSION['success'] = "Jadwal berhasil diperbarui!";
            header("Location: kelola_jadwal.php");
            exit();
        } else {
            $error = "Gagal memperbarui jadwal.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Jadwal | Admin Panel</title>
  <link rel="stylesheet" href="assets/css/kelola_jadwal.css?v=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold ms-3" href="#">Lakoni Aja | Admin</a>
    <div class="ms-auto me-3 text-white fw-semibold">
      <?= htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin') ?> |
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
  <h5 class="text-center mb-4">Menu Admin</h5>
  <a href="dashboard_admin.php"> Dashboard</a>
  <a href="kelola_konselor.php"> Kelola Konselor</a>
  <a href="kelola_user.php"> Kelola User</a>
  <a href="kelola_jadwal.php" class="active"> Kelola Jadwal</a>
</div>

<!-- Content -->
<div class="content">
  <div class="container mt-5">
    <div class="card shadow-sm p-4">
      <h3 class="text-primary mb-3">Edit Jadwal Konseling</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Nama Konselor</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($jadwal['nama_konselor']) ?>" disabled>
        </div>

        <div class="mb-3">
          <label for="tanggal" class="form-label">Tanggal Konseling</label>
          <input type="date" id="tanggal" name="tanggal" class="form-control" 
                 value="<?= htmlspecialchars($jadwal['tanggal']) ?>" required>
        </div>

        <div class="mb-3">
          <label for="jam_mulai" class="form-label">Jam Mulai</label>
          <input type="time" id="jam_mulai" name="jam_mulai" class="form-control" 
                 value="<?= htmlspecialchars($jadwal['jam_mulai']) ?>" required>
        </div>

        <div class="mb-3">
          <label for="jam_selesai" class="form-label">Jam Selesai</label>
          <input type="time" id="jam_selesai" name="jam_selesai" class="form-control" 
                 value="<?= htmlspecialchars($jadwal['jam_selesai']) ?>" required>
        </div>

        <div class="mb-3">
          <label for="status" class="form-label">Status</label>
          <select id="status" name="status" class="form-select" required>
            <option value="Tersedia" <?= ($jadwal['status'] == 'Tersedia') ? 'selected' : '' ?>>Tersedia</option>
            <option value="Dipesan" <?= ($jadwal['status'] == 'Dipesan') ? 'selected' : '' ?>>Dipesan</option>
            <option value="Selesai" <?= ($jadwal['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
          </select>
        </div>

        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
        <a href="kelola_jadwal.php" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

</body>
</html>
