<?php
header("Content-Type: application/json");
include "koneksi.php";

$id = $_GET['id_konselor'];

$sql = "SELECT * FROM jadwal WHERE id_konselor = ? ORDER BY tanggal ASC, jam_mulai ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$list = [];

while ($row = mysqli_fetch_assoc($res)) {
    $list[] = $row;
}

echo json_encode(["success" => true, "jadwal" => $list]);
?>
