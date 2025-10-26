<?php
include 'config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);  // Wajib ini
if ($data === null) {
    echo json_encode([
        "status" => "error",
        "message" => "JSON tidak terbaca.",
        "raw_input" => file_get_contents("php://input")
    ]);
    exit;
}


$id_user     = $data['id_user'] ?? null;
$id_konselor = $data['id_konselor'] ?? null;
$id_sesi     = $data['id_sesi'] ?? null;
$pesan       = trim($data['pesan'] ?? '');

if (empty($pesan)) {
    echo json_encode(["status" => "error", "message" => "Pesan kosong."]);
    exit;
}

$stmt = mysqli_prepare($conn,
    "INSERT INTO chat (id_user, id_konselor, id_sesi, pesan, waktu_kirim)
     VALUES (?, ?, ?, ?, NOW())"
);
mysqli_stmt_bind_param($stmt, "iiis", $id_user, $id_konselor, $id_sesi, $pesan);
$success = mysqli_stmt_execute($stmt);

if ($success) {
    echo json_encode(["status" => "success", "message" => "Pesan terkirim"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan pesan"]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
