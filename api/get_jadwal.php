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

if (!isset($_GET['id_konselor'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Parameter id_konselor wajib dikirim"
    ]);
    exit();
}

$id_konselor = $conn->real_escape_string($_GET['id_konselor']);

$query = "
    SELECT 
        id_jadwal,
        id_konselor,
        tanggal,
        jam_mulai,
        status
    FROM jadwal
    WHERE id_konselor = '$id_konselor'
    ORDER BY tanggal ASC, jam_mulai ASC
";

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
