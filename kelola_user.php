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
    $kelas    = $_POST['email'];
    $kontak   = $_POST['no_hp'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO user (nama, nim, email, no_hp, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $nama, $nim, $email, $kontak, $username, $password);
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
    $kontak   = $_POST['no_hp'];
    $username = $_POST['username'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE user SET nama=?, nim=?, email=?, no_hp=?, username=?, password=? WHERE id_user=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama, $nim, $email, $kontak, $username, $password, $id_user);
    } else {
        $query = "UPDATE user SET nama=?, nim=?, email=?, no_hp=?, username=? WHERE id_user=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $nama, $nim, $email, $kontak, $username, $id_user);
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

// --- Search User ---
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
    <title>Kelola User</title>
    <!-- Load kedua CSS secara terpisah -->
    <link rel="stylesheet" href="assets/css/dashboard_admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/kelola_user.css?v=<?php echo time(); ?>">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="list_sidebar">
      <h2>Dashboard Admin</h2>
      <a href="dashboard_admin.php">📊 Dashboard</a>
      <a href="kelola_user.php" class="active">👤 Kelola User</a>
      <a href="kelola_konselor.php">🧑‍⚕️ Kelola Konselor</a>
      <a href="kelola_jadwal.php">📅 Kelola Jadwal</a>
  </div>

  <div class="keluar"><a href="logout.php" class="logout">Logout</a></div>
</div>

<!-- CONTENT -->
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

    <!-- TITLE + SEARCH + ADD USER -->
    <div class="page-header">
        <h2 class="page-title">Kelola User</h2>

        <div class="header-actions">
            <form method="GET" action="kelola_user.php" class="search-box">
                <input type="text" name="cari" value="<?= $cari ?>" placeholder="Cari user..." class="search-input">
                <button class="search-btn">🔍</button>
            </form>

            <a href="tambah_user.php" class="btn-add">+ Tambah User</a>
        </div>
    </div>

    <!-- TABLE USER -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:5%">ID</th>
                    <th style="width:20%">Nama</th>
                    <th style="width:15%">NIM</th>
                    <th style="width:20%">Email</th>
                    <th style="width:15%">Kontak</th>
                    <th style="width:10%">Username</th>
                    <th style="width:15%">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($users) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td style="text-align:center;"><?= $row['id_user'] ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= $row['nim'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['no_hp'] ?></td>
                            <td><?= $row['username'] ?></td>

                            <td>
                                <a href="edit_user.php?id=<?= $row['id_user'] ?>" class="btn-edit">Edit</a> |
                                <a href="?hapus=<?= $row['id_user'] ?>" class="btn-hapus" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:15px;">
                                Tidak ada data user.
                            </td>
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
