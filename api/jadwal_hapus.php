<?php
header("Content-Type: application/json");
include "koneksi.php";

$id = $_POST['id_jadwal'];

$sql = "DELETE FROM jadwal WHERE id_jadwal = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
$exec = mysqli_stmt_execute($stmt);

echo json_encode([
    "success" => $exec,
    "message" => $exec ? "Jadwal berhasil dihapus" : "Gagal menghapus jadwal"
]);
?>
