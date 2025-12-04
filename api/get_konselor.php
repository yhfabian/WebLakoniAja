<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../db.php';  // PASTI KE SINI

if (!isset($conn)) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database tidak ditemukan"
    ]);
    exit();
}

$query = "SELECT id_konselor, nama, bidang_keahlian, nip FROM konselor";
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
    "data"   => $list
]);
