<?php
session_start();
include 'db.php'; // koneksi ke database

// Lama waktu session aktif (dalam detik) — di sini 30 menit
$timeout_duration = 1800; // 1800 detik = 30 menit

// Jika sudah login dan belum timeout
if (isset($_SESSION['id_konselor'])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        // Jika sudah melebihi waktu, logout otomatis
        session_unset();
        session_destroy();
        setcookie("remember_me", "", time() - 3600, "/"); // hapus cookie juga
        header("Location: login.php?timeout=1");
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time(); // perbarui waktu aktivitas
    header("Location: dashboard.php");
    exit();
}

$errors = [];

// Jika cookie "remember_me" masih ada
if (!isset($_SESSION['id_konselor']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM konselor WHERE remember_token = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) === 1) {
        $konselor = mysqli_fetch_assoc($result);

        // Aktifkan kembali session
        $_SESSION['id_konselor'] = $konselor['id_konselor'];
        $_SESSION['nama']        = $konselor['nama'];
        $_SESSION['nip']         = $konselor['nip'];
        $_SESSION['bidang']      = $konselor['bidang_keahlian'];
        $_SESSION['LAST_ACTIVITY'] = time();

        header("Location: dashboard.php");
        exit();
    }
}

// Ketika form login dikirim
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); // checkbox “ingat saya”

    if (empty($username)) $errors[] = "Username/Email harus diisi.";
    if (empty($password)) $errors[] = "Password harus diisi.";

    if (empty($errors)) {
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

                // Regenerate session ID untuk keamanan
                session_regenerate_id(true);

                $_SESSION['id_konselor'] = $konselor['id_konselor'];
                $_SESSION['nama']        = $konselor['nama'];
                $_SESSION['nip']         = $konselor['nip'];
                $_SESSION['bidang']      = $konselor['bidang_keahlian'];
                $_SESSION['LAST_ACTIVITY'] = time();

                // Jika “ingat saya” dicentang
                if ($remember) {
                    $token = bin2hex(random_bytes(32)); // token acak
                    $expiry = time() + (86400 * 7); // berlaku 7 hari

                    // Simpan token di database
                    $stmt = mysqli_prepare($conn, "UPDATE konselor SET remember_token = ? WHERE id_konselor = ?");
                    mysqli_stmt_bind_param($stmt, "si", $token, $konselor['id_konselor']);
                    mysqli_stmt_execute($stmt);

                    // Simpan juga token di cookie
                    setcookie("remember_me", $token, $expiry, "/", "", false, true);
                }

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
    
    <?php if (isset($_GET['timeout'])): ?>
        <div class="alert error">Session Anda telah berakhir. Silakan login kembali.</div>
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
            <span class="toggle-password" onclick="togglePassword()"></span>
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
