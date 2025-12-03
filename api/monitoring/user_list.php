<?php
header("Content-Type: application/json");
require "../../db.php";

$q = mysqli_query($conn, "
    SELECT id_user, nama, nim, email 
    FROM user 
    ORDER BY nama ASC
");

$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
