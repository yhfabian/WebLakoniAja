<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

$id_jadwal  = $_POST['id_jadwal'];
$id_konselor = $_POST['id_konselor'];
$tanggal    = $_POST['tanggal'];
$mulai      = $_POST['jam_mulai'];
$selesai    = $_POST['jam_selesai'];
$status     = $_POST['status'];

$stmt = mysqli_prepare($conn, 
    "UPDATE jadwal 
     SET id_konselor = ?, tanggal = ?, jam_mulai = ?, jam_selesai = ?, status = ?
     WHERE id_jadwal = ?"
);

mysqli_stmt_bind_param($stmt, "issssi", 
    $id_konselor, 
    $tanggal, 
    $mulai, 
    $selesai, 
    $status, 
    $id_jadwal
);

$exec = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($exec) {
    $_SESSION['success'] = "Jadwal berhasil diperbarui!";
} else {
    $_SESSION['error'] = "Gagal memperbarui jadwal.";
}

header("Location: kelola_jadwal.php");  // 👈 Redirect DI SINI
exit();
