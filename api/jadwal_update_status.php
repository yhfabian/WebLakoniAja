<?php
header("Content-Type: application/json");
include "koneksi.php";

$id_jadwal = $_POST['id_jadwal'];
$status    = $_POST['status'];

$sql = "UPDATE jadwal SET status = ? WHERE id_jadwal = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $id_jadwal);
$exec = mysqli_stmt_execute($stmt);

echo json_encode([
    "success" => $exec,
    "message" => $exec ? "Status berhasil diperbarui" : "Gagal update status"
]);
?>
