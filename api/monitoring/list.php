<?php
header("Content-Type: application/json");
require "../../db.php";

if (!isset($_GET['id_konselor'])) {
    echo json_encode([
        "status" => false,
        "message" => "id_konselor wajib"
    ]);
    exit;
}

$id_konselor = $_GET['id_konselor'];

$query = "
SELECT 
    m.id_monitoring,
    m.tanggal,
    u.nama AS nama_user,
    u.nim AS nim_user,
    u.email AS email_user
FROM monitoring m
JOIN user u ON m.id_user = u.id_user
WHERE m.id_konselor = '$id_konselor'
ORDER BY m.id_monitoring DESC
";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
