<?php
session_start();
include 'db.php'; // koneksi ke database

// Jika sudah login
if (isset($_SESSION['id_konselor'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) $errors[] = "Username/Email harus diisi.";
    if (empty($password)) $errors[] = "Password harus diisi.";

    if (empty($errors)) {
    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, 
        "SELECT * FROM konselor WHERE username=? OR kontak=? LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $konselor = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $konselor['password'])) {
            $_SESSION['id_konselor'] = $konselor['id_konselor'];
            $_SESSION['nama']        = $konselor['nama'];
            $_SESSION['nip']         = $konselor['nip'];
            $_SESSION['bidang']      = $konselor['bidang_keahlian'];

            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Password salah.";
        }
    } else {
        $errors[] = "Konselor tidak ditemukan.";
    }
}

}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login Konselor | Ruang Polije</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0" />
</head>
<body>
<div class="container">
  <!-- Bagian kiri -->
  <div class="left">
    <img src="assets/img/logo2.png" alt="Logo Polije" style="width:250px;">
    <h1>LOGIN KONSELOR</h1>
    <p>Masuk ke sistem untuk melayani mahasiswa.</p>
  </div>

  <!-- Bagian kanan -->
  <div class="right">
    <h1>LOGIN</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
            <?php foreach($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username / Email</label>
        <input type="text" id="username" name="username" placeholder="username/email" required />

        <label for="password">Password</label>
        <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Masukkan password" required />
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <div class="forgot-password"><a href="#">Lupa password?</a></div>
        <p><a href="forgot_password">Kembali ke Login</a></p>
        
        <button type="submit" name="login">LOGIN</button>
        <p>Belum punya akun? <a href="register.php">Daftar</a></p>
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
