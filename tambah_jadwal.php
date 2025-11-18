<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_konselor'])) {
  header("Location: login.php");
  exit();
}

$id_konselor = $_SESSION['id_konselor'];
$tanggal = $_POST['tanggal'];
$jam_mulai = $_POST['jam_mulai'];
$jam_selesai = $_POST['jam_selesai'];
$status = $_POST['status'];

$query = "INSERT INTO jadwal (id_konselor, tanggal, jam_mulai, jam_selesai, status) 
          VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "issss", $id_konselor, $tanggal, $jam_mulai, $jam_selesai, $status);

if (mysqli_stmt_execute($stmt)) {
  echo "<script>alert('✅ Jadwal berhasil ditambahkan!'); window.location.href='jadwalkonselor.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menambah jadwal.'); window.location.href='jadwalkonselor.php';</script>";
}
?>
