<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../db.php';

if (!isset($_POST['id_booking']) || !isset($_POST['id_jadwal'])) {
    echo json_encode([
        "status" => false,
        "message" => "Parameter id_booking dan id_jadwal wajib dikirim"
    ]);
    exit();
}

$id_booking = $conn->real_escape_string($_POST['id_booking']);
$id_jadwal  = $conn->real_escape_string($_POST['id_jadwal']);

// HAPUS BOOKING
$delete = $conn->query("DELETE FROM booking WHERE id_booking = '$id_booking'");

if (!$delete) {
    echo json_encode([
        "status" => false,
        "message" => "Gagal menghapus booking",
        "error"   => $conn->error
    ]);
    exit();
}

// SET JADWAL TERSEDIA KEMBALI
$conn->query("UPDATE jadwal SET status='Tersedia' WHERE id_jadwal='$id_jadwal'");

echo json_encode([
    "status" => true,
    "message" => "Booking berhasil dibatalkan"
]);
