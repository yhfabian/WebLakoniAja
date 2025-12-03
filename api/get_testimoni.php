<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../db.php';

if (!isset($conn)) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database tidak ditemukan"
    ]);
    exit();
}

$query = "SELECT t.id_testimoni, t.komentar, t.tanggal, u.nama 
          FROM testimoni t
          LEFT JOIN user u ON t.id_user = u.id_user
          ORDER BY t.id_testimoni DESC";

$result = $conn->query($query);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Query gagal: " . $conn->error
    ]);
    exit();
}

$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $list
]);
