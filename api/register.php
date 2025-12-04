<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = $_POST['nama'] ?? '';
    $nim = $_POST['nim'] ?? '';
    $email = $_POST['email'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($nim) || empty($email) || empty($tanggal_lahir) || empty($no_hp) || empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap!"]);
        exit;
    }

    if (!preg_match('/^[A-Za-z]\d{8}$/', $nim)) {
        echo json_encode(["success" => false, "message" => "Format NIM harus 1 huruf diikuti 8 angka!"]);
        exit;
    }

    $expected_email = $nim . '@student.polije.ac.id';
    if ($email !== $expected_email) {
        echo json_encode(["success" => false, "message" => "Email harus " . $expected_email]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Format email tidak valid!"]);
        exit;
    }

    if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggal_lahir)) {
    echo json_encode(["success" => false, "message" => "Format tanggal lahir harus DD/MM/YYYY!"]);
    exit;
    }

    $check = $conn->prepare("SELECT * FROM user WHERE email = ? OR nim = ?");
    $check->bind_param("ss", $email, $nim);
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

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO user (nama, nim, email, no_hp, tanggal_lahir, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nama, $nim, $email, $no_hp, $tanggal_lahir, $username, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registrasi berhasil!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menyimpan data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan!"]);
}
?>