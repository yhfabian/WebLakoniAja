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
  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Poppins', sans-serif;
    }
    .navbar {
      background-color: #004aad;
    }
    .navbar-brand, .nav-link, .navbar-text {
      color: white !important;
    }
    .card-stat {
      border-left: 5px solid #004aad;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 10px;
    }
    .footer {
      text-align: center;
      padding: 20px;
      margin-top: 50px;
      color: #666;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Lakoni Aja - Admin Panel</a>
    <div class="d-flex align-items-center">
      <span class="me-3 text-white fw-semibold">
        <?= htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin') ?>
      </span>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Dashboard Content -->
<div class="container mt-4">
  <div class="row text-center g-3">
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

  <!-- Data Konselor -->
  <div class="card mt-5">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Daftar Konselor</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Bidang Keahlian</th>
            <th>Kontak</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $konselor = mysqli_query($conn, "SELECT * FROM konselor ORDER BY nama ASC");
          if (mysqli_num_rows($konselor) > 0):
            while ($row = mysqli_fetch_assoc($konselor)): ?>
              <tr>
                <td><?= $row['id_konselor'] ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['bidang_keahlian']) ?></td>
                <td><?= htmlspecialchars($row['kontak']) ?></td>
              </tr>
          <?php endwhile; else: ?>
              <tr><td colspan="4" class="text-center text-muted">Belum ada data konselor.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Data Jadwal -->
  <div class="card mt-4">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">Daftar Jadwal Konseling</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID Jadwal</th>
            <th>Konselor</th>
            <th>Tanggal</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $jadwal = mysqli_query($conn, "
            SELECT j.*, k.nama AS nama_konselor
            FROM jadwal j
            JOIN konselor k ON j.id_konselor = k.id_konselor
            ORDER BY tanggal DESC
          ");
          if (mysqli_num_rows($jadwal) > 0):
            while ($row = mysqli_fetch_assoc($jadwal)): ?>
              <tr>
                <td><?= $row['id_jadwal'] ?></td>
                <td><?= htmlspecialchars($row['nama_konselor']) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['jam_mulai']) ?></td>
                <td><?= htmlspecialchars($row['jam_selesai']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
              </tr>
          <?php endwhile; else: ?>
              <tr><td colspan="6" class="text-center text-muted">Belum ada jadwal konseling.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="footer">
  <p>&copy; <?= date('Y') ?> Lakoni Aja - Sistem Konseling Polije</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
