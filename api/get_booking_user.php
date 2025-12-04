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

if (!isset($_GET['id_user'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Parameter id_user wajib dikirim"
    ]);
    exit();
}

$id_user = $conn->real_escape_string($_GET['id_user']);

// MODIFIKASI QUERY INI - TAMBAHKAN FIELD DARI JADWAL
$query = "
    SELECT 
        b.id_booking,
        b.id_user,
        b.id_jadwal,
        b.jenis_konseling,
        b.tanggal_booking,
        j.id_konselor,
        j.tanggal,           
        j.jam_mulai,         
        j.jam_selesai,     
        j.status as status_jadwal,  
        k.nama
    FROM booking b
    JOIN jadwal j ON b.id_jadwal = j.id_jadwal
    JOIN konselor k ON j.id_konselor = k.id_konselor
    WHERE b.id_user = '$id_user'
    ORDER BY b.id_booking DESC
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
?>