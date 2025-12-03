<?php
header("Content-Type: application/json");
include "../../db.php";

$result = $conn->query("SELECT *, CONCAT('http://localhost/lakoni_aja/uploads/artikel/', gambar) AS gambar_url FROM artikel ORDER BY id_artikel DESC");

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $rows
]);
?>
