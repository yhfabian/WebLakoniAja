<?php
session_start();
require "db.php";

if (!isset($_SESSION['id_konselor'])) {
    die("Akses ditolak.");
}

$id_konselor = $_SESSION['id_konselor'];

if (!isset($_FILES['foto'])) {
    die("File tidak ditemukan.");
}

$foto = $_FILES['foto'];
$allowed = ['jpg','jpeg','png'];
$ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    die("Format file harus JPG/PNG.");
}

if ($foto['size'] > 2 * 1024 * 1024) {
    die("Ukuran maksimum 2MB");
}

// Buat nama file baru unik
$nama_file = "konselor_" . $id_konselor . "_" . time() . "." . $ext;

$target = "uploads/" . $nama_file;
move_uploaded_file($foto['tmp_name'], $target);

// Update ke database
$sql = "UPDATE konselor SET foto = '$nama_file' WHERE id_konselor = $id_konselor";
mysqli_query($conn, $sql);

// Redirect balik ke dashboard
header("Location: dashboard.php");
exit();
?>
