<?php
session_start();
include 'db.php';

// Pastikan admin login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

$success = "";
$error = "";

// Tambah konselor baru
if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $bidang = trim($_POST['bidang_keahlian']);
    $kontak = trim($_POST['kontak']);

    if ($nama && $username && $password && $bidang && $kontak) {

        // Cek username
        $check = mysqli_prepare($conn, "SELECT id_konselor FROM konselor WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "❌ Username sudah digunakan!";
        } else {

            $pwHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = mysqli_prepare($conn, "
                INSERT INTO konselor (nama, username, password, bidang_keahlian, kontak)
                VALUES (?, ?, ?, ?, ?)
            ");

            mysqli_stmt_bind_param($stmt, "sssss", $nama, $username, $pwHash, $bidang, $kontak);

            if (mysqli_stmt_execute($stmt)) {
                $success = "✅ Konselor berhasil ditambahkan!";
            } else {
                $error = "Gagal menambah konselor.";
            }
        }

        mysqli_stmt_close($check);
    } else {
        $error = "Semua field wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tambah Konselor</title>
<link rel="stylesheet" href="assets/css/dashboard_admin.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="assets/css/kelola_konselor.css?v=<?php echo time(); ?>">
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

    <h2 class="page-title">Tambah Konselor</h2>

    <?php if ($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>

    <div class="form-box">
        <form method="POST">

            <label>Nama Konselor</label>
            <input type="text" name="nama" required>

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Bidang Keahlian</label>
            <input type="text" name="bidang_keahlian" required>

            <label>Email</label>
            <input type="text" name="kontak" required>

           <div class="button-group">
                <button type="submit" name="simpan" class="btn-save">Simpan</button>
                <a href="kelola_konselor.php" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>

</div>

<footer class="footer">
  © 2025 Lakoni Aja - Sistem Konseling Polije
</footer>

</body>
</html>
