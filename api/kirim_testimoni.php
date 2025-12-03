<?php
// Tampilkan error hanya di log, jangan ke output
error_reporting(E_ALL);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../db.php';

// Ambil POST data
$id_user     = $_POST['id_user'] ?? '';
$id_konselor = $_POST['id_konselor'] ?? '';
$komentar    = $_POST['komentar'] ?? '';
$tanggal     = $_POST['tanggal'] ?? '';

// Validasi field
if (empty($id_user) || empty($id_konselor) || empty($komentar) || empty($tanggal)) {
    echo json_encode([
        "status" => false,
        "message" => "Semua field wajib diisi"
    ]);
    exit();
}

// Insert testimoni
$insert = "INSERT INTO testimoni(id_user, id_konselor, komentar, tanggal) VALUES (?, ?, ?, ?)";
$stmtInsert = $conn->prepare($insert);
$stmtInsert->bind_param("ssss", $id_user, $id_konselor, $komentar, $tanggal);

if ($stmtInsert->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "Testimoni berhasil dikirim"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Gagal mengirim testimoni: " . $stmtInsert->error
    ]);
}
