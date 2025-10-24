<?php
session_start();
include 'db.php';

$errors = [];
$success = "";

if (isset($_POST['reset'])) {
    $kontak = trim($_POST['kontak']);

    if (empty($kontak)) {
        $errors[] = "Masukkan username atau email.";
    } else {
        // Cari konselor
        $sql = "SELECT * FROM Konselor WHERE username='$kontak' OR kontak='$kontak' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $konselor = mysqli_fetch_assoc($result);

            // Reset password default (misalnya polije123)
            $new_pass = "polije123";
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);

            // Update password di DB
            $update = "UPDATE Konselor SET password='$hashed' WHERE id_konselor=".$konselor['id_konselor'];
            if (mysqli_query($conn, $update)) {
                $success = "Password berhasil direset. Password baru: <b>$new_pass</b>";
            } else {
                $errors[] = "Gagal reset password: " . mysqli_error($conn);
            }
        } else {
            $errors[] = "Username/Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lupa Password | Konselor</title>
  <link rel="stylesheet" href="assets/css/style.css?v=1.0">
  <style>
    .container { max-width: 500px; margin: 80px auto; background:#fff; padding: 20px; border-radius: 10px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
    h2 { margin-bottom: 15px; }
    .alert { padding: 10px; margin-bottom: 10px; border-radius: 6px; }
    .alert.error { background:#ffe5e5; color:#cc0000; }
    .alert.success { background:#e6ffe6; color:#008000; }
    label { display:block; margin-top: 10px; }
    input { width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px; }
    button { margin-top:15px; width:100%; padding:10px; background:#0066cc; border:none; color:#fff; border-radius:6px; cursor:pointer; }
    button:hover { background:#004c99; }
    a { color:#0066cc; text-decoration:none; }
  </style>
</head>
<body>
<div class="container">
  <h2>Lupa Password</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert error">
      <?= implode("<br>", $errors) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert success">
      <?= $success ?>
    </div>
    <p><a href="login.php">Kembali ke Login</a></p>
  <?php else: ?>
    <form method="POST">
      <label for="kontak">Username atau Email</label>
      <input type="text" id="kontak" name="kontak" placeholder="Masukkan username atau email" required>
      <button type="submit" name="reset">Reset Password</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
