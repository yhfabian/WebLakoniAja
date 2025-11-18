<?php
header("Content-Type: application/json");
include "koneksi.php";

$res = mysqli_query($conn, "SELECT id_konselor, nama, foto FROM konselor");

$list = [];
while ($row = mysqli_fetch_assoc($res)) {
    $list[] = $row;
}

echo json_encode(["success" => true, "konselor" => $list]);
?>
