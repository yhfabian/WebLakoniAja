<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = $_POST['nama'] ?? '';
    $nim = $_POST['nim'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($nim) || empty($email) || empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap!"]);
        exit;
    }

    // Periksa apakah email atau username sudah terdaftar
    $check = $conn->prepare("SELECT * FROM user WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email atau Username sudah terdaftar!"]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO user (nama, nim, email, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $nim, $email, $username, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registrasi berhasil!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menyimpan data!"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan!"]);
}
?>