<?php
session_start();
include 'db.php';

// Pastikan admin login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

// ============================
// AMBIL DATA KONSELOR BY ID
// ============================
if (!isset($_GET['id'])) {
    die("ID konselor tidak ditemukan.");
}

$id = intval($_GET['id']);

$stmt = mysqli_prepare($conn, "SELECT * FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Konselor tidak ditemukan.");
}

$data = mysqli_fetch_assoc($result);

// ============================
// UPDATE DATA KONSELOR
// ============================
$success = "";
$error = "";

if (isset($_POST['update'])) {
    $nama   = trim($_POST['nama']);
    $bidang = trim($_POST['bidang_keahlian']);
    $kontak = trim($_POST['kontak']);

    if ($nama && $bidang && $kontak) {

        $stmt2 = mysqli_prepare($conn, 
            "UPDATE konselor SET nama = ?, bidang_keahlian = ?, kontak = ? WHERE id_konselor = ?"
        );
        mysqli_stmt_bind_param($stmt2, "sssi", $nama, $bidang, $kontak, $id);

        if (mysqli_stmt_execute($stmt2)) {
            header("Location: kelola_konselor.php?success=1");
            exit();
        } else {
            $error = "Gagal memperbarui data.";
        }
        mysqli_stmt_close($stmt2);

    } else {
        $error = "Semua field wajib diisi.";
    }
}

// ============================
// RESET PASSWORD
// ============================
if (isset($_POST['reset_password'])) {

    $new_pw = $_POST['new_password'];

    if (strlen($new_pw) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        $hash = password_hash($new_pw, PASSWORD_BCRYPT);

        $stmt3 = mysqli_prepare($conn, "UPDATE konselor SET password = ? WHERE id_konselor = ?");
        mysqli_stmt_bind_param($stmt3, "si", $hash, $id);

        if (mysqli_stmt_execute($stmt3)) {
            $success = "Password berhasil direset!";
        } else {
            $error = "Gagal reset password.";
        }
        mysqli_stmt_close($stmt3);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Konselor</title>
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

<a href="kelola_konselor.php" class="btn-back">← Kembali</a>

<div class="content">

    <div class="main-header">
        <h1 class="brand-title">Lakoni Aja</h1>
        <div class="profile-section">
            <span class="profile-name">Admin Petugas</span>
            <div class="profile-avatar">A</div>
        </div>
    </div>

    <hr class="header-line">

    <h2>Edit Konselor</h2>

    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <div class="form-box">

        <!-- FORM UPDATE -->
        <form method="POST">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

            <label>Bidang Keahlian</label>
            <input type="text" name="bidang_keahlian" value="<?= htmlspecialchars($data['bidang_keahlian']) ?>" required>

            <label>Kontak</label>
            <input type="text" name="kontak" value="<?= htmlspecialchars($data['kontak']) ?>" required>

            <label>Password Baru</label>
            <input type="password" name="new_password" required>

             <button type="submit" name="update" class="btn-submit">Simpan Perubahan</button>
        </form>

    </div>

    



</div>

</body>
</html>
