<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

// --- Hapus jadwal ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal WHERE id_jadwal = $id");
    $_SESSION['success'] = "Jadwal berhasil dihapus!";
    header("Location: kelola_jadwal.php");
    exit();
}

// --- Ambil semua jadwal konseling ---
$query = "
  SELECT j.*, k.nama AS nama_konselor 
  FROM jadwal j 
  JOIN konselor k ON j.id_konselor = k.id_konselor 
  ORDER BY j.tanggal DESC, j.jam_mulai ASC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Jadwal | Admin Panel</title>
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
  <div class="container-fluid mt-4">
    <h2 class="fw-bold text-primary mb-4">Kelola Jadwal Konseling</h2>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
          <tr class="text-center">
            <th>No</th>
            <th>Nama Konselor</th>
            <th>Tanggal</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_konselor']) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['jam_mulai']) ?></td>
                <td><?= htmlspecialchars($row['jam_selesai']) ?></td>
                <td>
                  <span class="badge 
                    <?php if ($row['status'] == 'Tersedia') echo 'bg-success';
                    elseif ($row['status'] == 'Dipesan') echo 'bg-warning text-dark';
                    else echo 'bg-info text-dark'; ?>">
                    <?= htmlspecialchars($row['status']) ?>
                  </span>
                </td>
                <td class="text-center">
                  <a href="edit_jadwal.php?id=<?= $row['id_jadwal'] ?>" class="btn btn-sm btn-primary">Edit</a>
                  <a href="?hapus=<?= $row['id_jadwal'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
                </td>
              </tr>
          <?php endwhile; else: ?>
              <tr><td colspan="7" class="text-center text-muted">Belum ada jadwal tersedia.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
