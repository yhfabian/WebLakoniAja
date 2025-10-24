<?php
session_start();
include 'db.php'; // koneksi ke database

if (isset($_POST['register'])) {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $nip      = trim($_POST['nip']);
    $bidang   = trim($_POST['bidang']);
    $kontak   = trim($_POST['kontak']);
    $password = $_POST['password'];

    $errors = [];

    // Validasi
    if (empty($nama)) $errors[] = "Nama harus diisi.";
    if (empty($username)) $errors[] = "Username harus diisi.";
    if (empty($nip)) $errors[] = "NIP harus diisi.";
    if (empty($bidang)) $errors[] = "Bidang keahlian harus diisi.";
    if (!filter_var($kontak, FILTER_VALIDATE_EMAIL)) $errors[] = "Kontak harus berupa email yang valid.";
    if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";

    if (empty($errors)) {
        // Cek username, NIP, atau email sudah ada
        $check = mysqli_query($conn, "SELECT * FROM Konselor 
            WHERE username='$username' OR nip='$nip' OR kontak='$kontak' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "Username / NIP / Email sudah terdaftar.";
        } else {
            // Simpan ke DB
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Konselor (nama, username, password, nip, bidang_keahlian, kontak) 
                    VALUES ('$nama', '$username', '$hashed_password', '$nip', '$bidang', '$kontak')";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success'] = "Registrasi konselor berhasil! Silakan login.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Terjadi kesalahan: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register Konselor | Ruang Polije</title>
  <link rel="stylesheet" href="assets/css/style.css?v=1.0">
</head>
<body>
<div class="container">
  <div class="left card">
    <h1>DAFTAR KONSELOR</h1>

    <?php if (!empty($errors)): ?>
      <div class="alert error">
        <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required>

      <label for="username">Username</label>
      <input type="text" id="username" name="username" required>

      <label for="nip">NIP</label>
      <input type="text" id="nip" name="nip" required>

      <label for="bidang">Bidang Keahlian</label>
      <input type="text" id="bidang" name="bidang" required>

      <label for="kontak">Email</label>
      <input type="email" id="kontak" name="kontak" required>

      <label for="password">Password</label>
      <div class="password-wrapper">
        <input type="password" id="password" name="password" required>
        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
      </div>

      <button type="submit" name="register">DAFTAR</button>
      <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </form>
  </div>

  <div class="right">
    <h1>Selamat Datang Konselor</h1>
    <p>Daftarkan diri Anda sebagai konselor untuk membantu mahasiswa menjaga kesehatan mental.</p>
  </div>
</div>

<script>
function togglePassword(id) {
  const field = document.getElementById(id);
  field.type = (field.type === "password") ? "text" : "password";
}
</script>
</body>
</html>
