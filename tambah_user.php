<?php
session_start();
include 'db.php';

// Pastikan admin login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

// --- Submit Tambah User ---
if (isset($_POST['tambah'])) {

    $nama     = trim($_POST['nama']);
    $nim      = trim($_POST['nim']);
    $email    = trim($_POST['email']);
    $kontak   = trim($_POST['kontak']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // validasi
    if ($nama && $nim && $email && $kontak && $username) {

        $stmt = mysqli_prepare($conn, "INSERT INTO user (nama, nim, email, kontak, username, password) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $nim, $email, $kontak, $username, $password);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('User berhasil ditambahkan!'); window.location='kelola_user.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan user!');</script>";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <link rel="stylesheet" href="assets/css/form_user.css?v=<?= time(); ?>">
</head>

<body>

<div class="form-container">
    <h2>➕ Tambah User</h2>

    <form method="POST">

        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>

        <label>NIM</label>
        <input type="text" name="nim" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Kontak</label>
        <input type="text" name="kontak" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <div class="btn-group">
            <button type="submit" name="tambah" class="btn-save">Simpan</button>
            <a href="kelola_user.php" class="btn-cancel">Batal</a>
        </div>

    </form>
</div>

</body>
</html>
