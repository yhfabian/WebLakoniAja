<?php
header("Content-Type: application/json");
include "koneksi.php";

$id_konselor = $_POST['id_konselor'];
$tanggal     = $_POST['tanggal'];
$mulai       = $_POST['jam_mulai'];
$selesai     = $_POST['jam_selesai'];
$status      = $_POST['status'];

$sql = "INSERT INTO jadwal (id_konselor, tanggal, jam_mulai, jam_selesai, status)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "issss", $id_konselor, $tanggal, $mulai, $selesai, $status);
$exec = mysqli_stmt_execute($stmt);

echo json_encode([
    "success" => $exec,
    "message" => $exec ? "Jadwal berhasil ditambahkan" : "Gagal menambahkan jadwal"
]);
?>
