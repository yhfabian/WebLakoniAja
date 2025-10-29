<?php
session_start();
include 'db.php'; // koneksi ke database

// Jika sudah login sebagai admin, arahkan ke dashboard admin
if (isset($_SESSION['id_admin'])) {
    header("Location: dashboard_admin.php");
    exit();
}

$errors = [];

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) $errors[] = "Username harus diisi.";
    if (empty($password)) $errors[] = "Password harus diisi.";

    if (empty($errors)) {
        // Gunakan prepared statement untuk keamanan
        $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $admin = mysqli_fetch_assoc($result);

            // Verifikasi password
            if (password_verify($password, $admin['password'])) {
    $_SESSION['id_admin']  = $admin['id_admin'];
    $_SESSION['nama_admin'] = $admin['nama_lengkap']; // ubah dari ['nama'] ke ['nama_lengkap']
    $_SESSION['role'] = 'admin';

    header("Location: dashboard_admin.php");
    exit();
}

            } else {
                $errors[] = "Password salah.";
            }
        } else {
            $errors[] = "Admin tidak ditemukan.";
        }
        mysqli_stmt_close($stmt);
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login Admin | LakoniAja</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0" />
</head>
<body>
<div class="container">
  <!-- Bagian kiri -->
  <div class="left">
    <img src="assets/img/logo2.png" alt="Logo LakoniAja" style="width:250px;">
    <h1>LOGIN ADMIN</h1>
    <p>Masuk ke sistem sebagai administrator.</p>
  </div>

  <!-- Bagian kanan -->
  <div class="right">
    <h1>LOGIN ADMIN</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
            <?php foreach($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Masukkan username" required />

        <label for="password">Password</label>
        <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Masukkan password" required />
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <button type="submit" name="login">LOGIN</button>
        <p><a href="index.php">Kembali ke Halaman Utama</a></p>
    </form>
  </div>
</div>

<script>
function togglePassword() {
    const field = document.getElementById("password");
    field.type = (field.type === "password") ? "text" : "password";
}
</script>
</body>
</html>
