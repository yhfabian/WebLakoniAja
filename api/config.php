<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "lakoni_aja";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal koneksi database",
        "error" => mysqli_connect_error()
    ]);
    exit;
}

mysqli_set_charset($conn, "utf8mb4");

// --- Tambahkan baris ini untuk test ---
echo json_encode(["status" => "ok", "message" => "Koneksi database berhasil!"]);

?>
