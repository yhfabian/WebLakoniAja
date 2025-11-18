<?php
header("Content-Type: application/json");
include "koneksi.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM konselor WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($res) == 0) {
    echo json_encode(["success" => false, "message" => "Email tidak ditemukan"]);
    exit;
}

$data = mysqli_fetch_assoc($res);

if (!password_verify($password, $data['password'])) {
    echo json_encode(["success" => false, "message" => "Password salah"]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Login berhasil",
    "konselor" => [
        "id_konselor" => $data['id_konselor'],
        "nama"        => $data['nama'],
        "email"       => $data['email'],
        "foto"        => $data['foto']
    ]
]);
?>
