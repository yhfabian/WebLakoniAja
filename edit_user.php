<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('User tidak ditemukan!'); window.location='kelola_user.php';</script>";
    exit();
}

$id_user = intval($_GET['id']);
$data = mysqli_query($conn, "SELECT * FROM user WHERE id_user=$id_user");
$user = mysqli_fetch_assoc($data);

if (!$user) {
    echo "<script>alert('User tidak ditemukan!'); window.location='kelola_user.php';</script>";
    exit();
}

// --- Proses Update ---
if (isset($_POST['edit'])) {

    $nama     = trim($_POST['nama']);
    $nim      = trim($_POST['nim']);
    $email    = trim($_POST['email']);
    $kontak   = trim($_POST['kontak']);
    $username = trim($_POST['username']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE user SET nama=?, nim=?, email=?, kontak=?, username=?, password=? WHERE id_user=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama, $nim, $email, $kontak, $username, $password, $id_user);
    } else {
        $query = "UPDATE user SET nama=?, nim=?, email=?, kontak=?, username=? WHERE id_user=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $nama, $nim, $email, $kontak, $username, $id_user);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<script>alert('Data user berhasil diperbarui'); window.location='kelola_user.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="assets/css/form_user.css?v=<?= time(); ?>">
</head>

<body>

<div class="form-container">
    <h2>✏️ Edit User</h2>

    <form method="POST">

        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>

        <label>NIM</label>
        <input type="text" name="nim" value="<?= $user['nim'] ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>

        <label>Kontak</label>
        <input type="text" name="kontak" value="<?= $user['kontak'] ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= $user['username'] ?>" required>

        <label>Password Baru (opsional)</label>
        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">

        <div class="btn-group">
            <button type="submit" name="edit" class="btn-save">Simpan</button>
            <a href="kelola_user.php" class="btn-cancel">Batal</a>
        </div>

    </form>
</div>

</body>
</html>
