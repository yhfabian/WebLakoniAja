<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

$success = "";
$error = "";

/* ========== CREATE (Tambah Konselor) ========== */
if (isset($_POST['tambah'])) {
    $nama    = trim($_POST['nama']);
    $username= trim($_POST['username']);
    $password= $_POST['password'];
    $bidang  = trim($_POST['bidang_keahlian']);
    $kontak  = trim($_POST['kontak']);

    if ($nama && $username && $password && $bidang && $kontak) {
        // cek username unik
        $stmt = mysqli_prepare($conn, "SELECT id_konselor FROM konselor WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "❌ Username sudah digunakan!";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);
            $pwHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($conn, "INSERT INTO konselor (nama, username, password, bidang_keahlian, kontak) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $nama, $username, $pwHash, $bidang, $kontak);
            if (mysqli_stmt_execute($stmt)) {
                $success = "✅ Konselor baru berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = "Semua field harus diisi!";
    }
}

/* ========== UPDATE (Edit Konselor) ========== */
if (isset($_POST['update'])) {
    $id     = $_POST['id_konselor'];
    $nama   = trim($_POST['nama']);
    $bidang = trim($_POST['bidang_keahlian']);
    $kontak = trim($_POST['kontak']);

    $stmt = mysqli_prepare($conn, "UPDATE konselor SET nama = ?, bidang_keahlian = ?, kontak = ? WHERE id_konselor = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $bidang, $kontak, $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "✅ Data konselor berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

/* ========== RESET PASSWORD (Admin sets new password) ========== */
if (isset($_POST['reset'])) {
    $id = $_POST['id_konselor_reset'];
    $new_password = $_POST['new_password'];
    if (strlen($new_password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        $pwHash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($conn, "UPDATE konselor SET password = ? WHERE id_konselor = ?");
        mysqli_stmt_bind_param($stmt, "si", $pwHash, $id);
        if (mysqli_stmt_execute($stmt)) {
            $success = "🔐 Password berhasil di-reset.";
        } else {
            $error = "Gagal mereset password: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

/* ========== DELETE (Hapus Konselor) ========== */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $stmt = mysqli_prepare($conn, "DELETE FROM konselor WHERE id_konselor = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "🗑️ Konselor berhasil dihapus.";
    } else {
        $error = "Gagal menghapus: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

/* ========== READ (Ambil list konselor) ========== */
$konselor = mysqli_query($conn, "SELECT id_konselor, nama, username, bidang_keahlian, kontak FROM konselor ORDER BY id_konselor DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Konselor | Lakoni Aja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/kelola_konselor.css?v=1.0">

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top navbar-admin">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold ms-3" href="dashboard_admin.php">Lakoni Aja | Admin</a>
    <div class="ms-auto me-3 text-white fw-semibold">
      <?= htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin') ?> |
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<aside class="sidebar">
  <h5 class="text-center mb-4">Menu Admin</h5>
  <a href="dashboard_admin.php"> Dashboard</a>
  <a href="kelola_konselor.php" class="active"> Kelola Konselor</a>
  <a href="kelola_user.php"> Kelola User</a>
  <a href="kelola_jadwal.php"> Kelola Jadwal</a>
</aside>

<!-- Content -->
<main class="content">
  <div class="container mt-4">
    <h2 class="fw-bold text-primary mb-4">Kelola Konselor</h2>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Tombol Tambah Konselor -->
    <button class="btn btn-success mb-3" data-bs-toggle="collapse" data-bs-target="#formTambah">+ Tambah Konselor</button>

    <!-- Form Tambah Konselor (email & status dihilangkan) -->
    <div id="formTambah" class="collapse">
      <div class="card p-4 mb-4">
        <form method="POST" autocomplete="off">
          <div class="row g-3">
            <div class="col-md-6">
              <label>Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label>Password</label>
              <input type="password" name="password" class="form-control" minlength="6" required>
            </div>
            <div class="col-md-6">
              <label>Bidang Keahlian</label>
              <input type="text" name="bidang_keahlian" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label>Kontak (email/nomor)</label>
              <input type="text" name="kontak" class="form-control" required>
            </div>
          </div>
          <div class="mt-3">
            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabel Konselor (tanpa kolom email & status) -->
    <div class="card p-3">
      <h5 class="mb-3">Daftar Konselor</h5>
      <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Bidang</th>
            <th>Kontak</th>
            <th width="220">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (mysqli_num_rows($konselor) > 0):
              $no = 1;
              while ($row = mysqli_fetch_assoc($konselor)): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['nama']) ?></td>
                  <td><?= htmlspecialchars($row['username']) ?></td>
                  <td><?= htmlspecialchars($row['bidang_keahlian']) ?></td>
                  <td><?= htmlspecialchars($row['kontak']) ?></td>
                  <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_konselor'] ?>">Edit</button>

                    <!-- Reset Password -->
                    <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#resetModal<?= $row['id_konselor'] ?>">Reset Password</button>

                    <a href="?hapus=<?= $row['id_konselor'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus konselor ini?')">Hapus</a>
                  </td>
                </tr>

                <!-- Modal Edit Konselor -->
                <div class="modal fade" id="editModal<?= $row['id_konselor'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">Edit Konselor</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id_konselor" value="<?= $row['id_konselor'] ?>">
                          <label>Nama</label>
                          <input type="text" name="nama" class="form-control mb-2" value="<?= htmlspecialchars($row['nama']) ?>" required>
                          <label>Bidang Keahlian</label>
                          <input type="text" name="bidang_keahlian" class="form-control mb-2" value="<?= htmlspecialchars($row['bidang_keahlian']) ?>" required>
                          <label>Kontak</label>
                          <input type="text" name="kontak" class="form-control mb-2" value="<?= htmlspecialchars($row['kontak']) ?>" required>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Modal Reset Password -->
                <div class="modal fade" id="resetModal<?= $row['id_konselor'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" autocomplete="off">
                        <div class="modal-header bg-dark text-white">
                          <h5 class="modal-title">Reset Password untuk <?= htmlspecialchars($row['nama']) ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id_konselor_reset" value="<?= $row['id_konselor'] ?>">
                          <label>Password Baru</label>
                          <input type="password" name="new_password" class="form-control mb-2" minlength="6" required>
                          <small class="text-muted">Masukkan password baru (min 6 karakter).</small>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" name="reset" class="btn btn-warning">Reset Password</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                
          <?php endwhile; else: ?>
              <tr><td colspan="6" class="text-center text-muted">Belum ada data konselor.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>

  </div>
</main>
 <div class="footer">
    <p>&copy; <?= date('Y') ?> Lakoni Aja - Sistem Konseling Polije</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
