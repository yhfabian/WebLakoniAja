<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

// Ambil data statistik
$totalKonselor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM konselor"))['total'];
$totalUser     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user"))['total'];
$totalJadwal   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM jadwal"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | Lakoni Aja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard_admin.css?v=1.0">
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
  <a href="dashboard_admin.php" class="active">🏠 Dashboard</a>
  <a href="kelola_konselor.php">👨‍🏫 Kelola Konselor</a>
  <a href="kelola_user.php">👥 Kelola User</a>
  <a href="kelola_jadwal.php">🗓️ Kelola Jadwal</a>
  <a href="monitor_chat.php">💬 Monitor Chat</a>
</div>

<!-- Content -->
<div class="content">
  <div class="container-fluid mt-4">
    <h2 class="fw-bold text-primary mb-4">Dashboard Admin</h2>

    <div class="row text-center g-4">
      <div class="col-md-4">
        <div class="card card-stat p-3">
          <h5>Total Konselor</h5>
          <h3><?= $totalKonselor ?></h3>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-stat p-3">
          <h5>Total Mahasiswa</h5>
          <h3><?= $totalUser ?></h3>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-stat p-3">
          <h5>Total Jadwal</h5>
          <h3><?= $totalJadwal ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="footer">
    <p>&copy; <?= date('Y') ?> Lakoni Aja - Sistem Konseling Polije</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
