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

        // Cek apakah username sudah digunakan
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

/* ========== RESET PASSWORD ========== */
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

/* ========== DELETE ========== */
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Konselor</title>
    <link rel="stylesheet" href="assets/css/kelola_konselor.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<div class="sidebar">
  <div class="list_sidebar">
      <h2>Dashboard Admin</h2>
      <a href="dashboard_admin.php">📊 Dashboard</a>
      <a href="kelola_user.php">👤 Kelola User</a>
      <a href="kelola_konselor.php" class="active">🧑‍⚕️ Kelola Konselor</a>
      <a href="kelola_jadwal.php">📅 Kelola Jadwal</a>
  </div>
  <div class="keluar"><a href="logout.php" class="logout">Logout</a></div>
</div>

<div class="content">

    <div class="main-header">
        <h1 class="brand-title">Lakoni Aja</h1>
        <div class="profile-section">
            <span class="profile-name">Admin Petugas</span>
            <div class="profile-avatar">A</div>
        </div>
    </div>

    <hr class="header-line">

    <div class="header-actions">
        <h2 class="page-title">Kelola Konselor</h2>
        <a href="tambah_konselor.php" class="btn-add">+ Tambah Konselor</a>
    </div>
    

    <!-- ALERT SUCCESS / ERROR -->
    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Bidang</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($konselor)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['bidang_keahlian']) ?></td>
                    <td><?= htmlspecialchars($row['kontak']) ?></td>

                    <td class="action-buttons">
                        <a href="edit_konselor.php?id=<?= $row['id_konselor'] ?>" class="btn-edit">Edit</a>
                        <a href="?hapus=<?= $row['id_konselor'] ?>" 
                           class="btn-delete"
                           onclick="return confirm('Yakin ingin menghapus konselor ini?')">
                           Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

            <?php if (mysqli_num_rows($konselor) == 0): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px;">Belum ada konselor.</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

<footer class="footer">
  <p>© 2025 Lakoni Aja - Sistem Konseling Polije</p>
</footer>

</body>
</html>
