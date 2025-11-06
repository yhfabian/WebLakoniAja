<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

// --- Tambah User ---
if (isset($_POST['tambah'])) {
    $nama     = $_POST['nama'];
    $nim      = $_POST['nim'];
    $kelas    = $_POST['kelas'];
    $kontak   = $_POST['kontak'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO user (nama, nim, kelas, kontak, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $nama, $nim, $kelas, $kontak, $username, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: kelola_user.php");
    exit();
}

// --- Edit User ---
if (isset($_POST['edit'])) {
    $id_user  = $_POST['id_user'];
    $nama     = $_POST['nama'];
    $nim      = $_POST['nim'];
    $kelas    = $_POST['kelas'];
    $kontak   = $_POST['kontak'];
    $username = $_POST['username'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE user SET nama=?, nim=?, kelas=?, kontak=?, username=?, password=? WHERE id_user=?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama, $nim, $kelas, $kontak, $username, $password, $id_user);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE user SET nama=?, nim=?, kelas=?, kontak=?, username=? WHERE id_user=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $nama, $nim, $kelas, $kontak, $username, $id_user);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: kelola_user.php");
    exit();
}

// --- Hapus User ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM user WHERE id_user = $id");
    header("Location: kelola_user.php");
    exit();
}

// --- Reset Password User ---
if (isset($_GET['reset'])) {
    $id = $_GET['reset'];
    $newPassword = password_hash("user123", PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "UPDATE user SET password=? WHERE id_user=?");
    mysqli_stmt_bind_param($stmt, "si", $newPassword, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "<script>alert('Password user berhasil direset menjadi: user123'); window.location='kelola_user.php';</script>";
    exit();
}

// --- Pencarian User ---
$cari = "";
if (isset($_GET['cari'])) {
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
    $users = mysqli_query($conn, "SELECT * FROM user WHERE nama LIKE '%$cari%' OR nim LIKE '%$cari%' ORDER BY nama ASC");
} else {
    $users = mysqli_query($conn, "SELECT * FROM user ORDER BY nama ASC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola User | Lakoni Aja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/kelola_user.css?v=1.0">
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
  <a href="kelola_user.php" class="active"> Kelola User</a>
  <a href="kelola_jadwal.php"> Kelola Jadwal</a>
</div>

<!-- Content -->
<div class="content">
  <div class="container-fluid mt-4">
    <h2 class="page-title fw-bold">Kelola User (Mahasiswa)</h2>

    <!-- Form Pencarian -->
    <form method="GET" class="d-flex mb-3" role="search">
      <input type="text" class="form-control me-2" name="cari" placeholder="Cari nama atau NIM..." value="<?= htmlspecialchars($cari) ?>">
      <button class="btn btn-primary">Cari</button>
      <?php if ($cari != ""): ?>
        <a href="kelola_user.php" class="btn btn-secondary ms-2">Reset</a>
      <?php endif; ?>
    </form>

    <!-- Tombol Tambah User -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahUserModal">+ Tambah User</button>

    <!-- Tabel User -->
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Daftar User</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>NIM</th>
              <th>Kelas</th>
              <th>Kontak</th>
              <th>Username</th>
              <th width="180">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($users) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($users)): ?>
                <tr>
                  <td><?= $row['id_user'] ?></td>
                  <td><?= htmlspecialchars($row['nama']) ?></td>
                  <td><?= htmlspecialchars($row['nim']) ?></td>
                  <td><?= htmlspecialchars($row['kelas']) ?></td>
                  <td><?= htmlspecialchars($row['kontak']) ?></td>
                  <td><?= htmlspecialchars($row['username']) ?></td>
                  <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $row['id_user'] ?>">Edit</button>
                    <a href="?reset=<?= $row['id_user'] ?>" class="btn btn-info btn-sm" onclick="return confirm('Yakin ingin reset password user ini ke default (user123)?')">Reset</a>
                    <a href="?hapus=<?= $row['id_user'] ?>" onclick="return confirm('Yakin ingin menghapus user ini?')" class="btn btn-danger btn-sm">Hapus</a>
                  </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editUserModal<?= $row['id_user'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <form method="POST" class="modal-content">
                      <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                        <div class="mb-3">
                          <label>Nama</label>
                          <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>NIM</label>
                          <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($row['nim']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>Kelas</label>
                          <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($row['kelas']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>Kontak</label>
                          <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($row['kontak']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>Username</label>
                          <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>Password (Opsional)</label>
                          <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="edit" class="btn btn-success">Simpan</button>
                      </div>
                    </form>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-muted">Belum ada user ditemukan.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="footer">
    <p>&copy; <?= date('Y') ?> Lakoni Aja - Sistem Konseling Polije</p>
  </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="tambahUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>NIM</label>
          <input type="text" name="nim" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Kelas</label>
          <input type="text" name="kelas" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Kontak</label>
          <input type="text" name="kontak" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
