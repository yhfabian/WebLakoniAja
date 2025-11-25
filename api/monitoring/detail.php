<?php
header("Content-Type: application/json");
require "../../db.php";

if (!isset($_GET['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "ID wajib dikirim"
    ]);
    exit;
}

$id = $_GET['id'];

$query = "
SELECT 
    m.id_monitoring,
    m.id_user,
    m.id_konselor,
    m.tanggal,
    m.catatan,
    m.diagnosis,
    m.rekomendasi,

    u.nama AS nama_user,
    u.nim AS nim_user,
    u.kelas AS kelas_user,

    k.nama AS nama_konselor
FROM monitoring m
JOIN user u ON m.id_user = u.id_user
JOIN konselor k ON m.id_konselor = k.id_konselor
WHERE m.id_monitoring = '$id'
";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

echo json_encode([
    "status" => $data ? "success" : "error",
    "data"   => $data
]);
