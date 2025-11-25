<?php
header("Content-Type: application/json");
require "../../db.php";

$query = "SELECT id_user, nama, nim, kelas FROM user ORDER BY nama ASC";
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
