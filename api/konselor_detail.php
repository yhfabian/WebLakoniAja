<?php
header("Content-Type: application/json");
include "koneksi.php";

$id = $_GET['id_konselor'];

$sql = "SELECT * FROM konselor WHERE id_konselor = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

echo json_encode([
    "success" => true,
    "detail" => mysqli_fetch_assoc($res)
]);
?>
