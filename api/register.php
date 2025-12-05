<?php
// Matikan warning/notice agar output JSON tetap valid
error_reporting(0);
header('Content-Type: application/json');

// Koneksi database
require_once __DIR__ . '/../db.php';  // pastikan path benar

// Izinkan request dari semua origin (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Cek metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan!"]);
    exit;
}

// Ambil data POST
$nama          = $_POST['nama'] ?? '';
$nim           = $_POST['nim'] ?? '';
$email         = $_POST['email'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$no_hp         = $_POST['no_hp'] ?? '';
$username      = $_POST['username'] ?? '';
$password      = $_POST['password'] ?? '';

// Validasi field kosong
if (empty($nama) || empty($nim) || empty($email) || empty($tanggal_lahir) || empty($no_hp) || empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap!"]);
    exit;
}

// Validasi panjang password (8-16 karakter)
if (strlen($password) < 8 || strlen($password) > 16) {
    echo json_encode(["success" => false, "message" => "Password harus antara 8 hingga 16 karakter!"]);
    exit;
}

// Validasi NIM (1 huruf + 8 angka)
if (!preg_match('/^[A-Za-z]\d{8}$/', $nim)) {
    echo json_encode(["success" => false, "message" => "Format NIM harus 1 huruf diikuti 8 angka!"]);
    exit;
}

// Validasi email sesuai NIM
$expected_email = $nim . '@student.polije.ac.id';
if ($email !== $expected_email) {
    echo json_encode(["success" => false, "message" => "Email harus " . $expected_email]);
    exit;
}

// Validasi format tanggal lahir DD/MM/YYYY
if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggal_lahir)) {
    echo json_encode(["success" => false, "message" => "Format tanggal lahir harus DD/MM/YYYY!"]);
    exit;
}

// Konversi tanggal ke format MySQL YYYY-MM-DD
$tanggal_mysql = DateTime::createFromFormat('d/m/Y', $tanggal_lahir);
if (!$tanggal_mysql) {
    echo json_encode(["success" => false, "message" => "Format tanggal lahir tidak valid!"]);
    exit;
}
$tanggal_mysql = $tanggal_mysql->format('Y-m-d');

// Cek duplikat email, NIM, username
$check = $conn->prepare("SELECT email, nim, username FROM user WHERE email = ? OR nim = ? OR username = ?");
if (!$check) {
    echo json_encode(["success" => false, "message" => "Gagal mempersiapkan query: " . $conn->error]);
    exit;
}
$check->bind_param("sss", $email, $nim, $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $existing = $result->fetch_assoc();
    if ($existing['email'] === $email) {
        echo json_encode(["success" => false, "message" => "Email sudah terdaftar!"]);
    } else if ($existing['nim'] === $nim) {
        echo json_encode(["success" => false, "message" => "NIM sudah terdaftar!"]);
    } else if ($existing['username'] === $username) {
        echo json_encode(["success" => false, "message" => "Username sudah terdaftar!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Data sudah terdaftar!"]);
    }
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert data
$stmt = $conn->prepare("INSERT INTO user (nama, nim, email, no_hp, tanggal_lahir, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Gagal mempersiapkan insert: " . $conn->error]);
    exit;
}
$stmt->bind_param("sssssss", $nama, $nim, $email, $no_hp, $tanggal_mysql, $username, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registrasi berhasil!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan data: " . $stmt->error]);
}

$stmt->close();
$conn->close();