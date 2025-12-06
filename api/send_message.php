<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { 
    http_response_code(200); 
    exit(); 
}

require_once __DIR__ . '/../db.php';

$id_booking  = $_POST['id_booking'] ?? '';
$id_user     = $_POST['id_user'] ?? '';
$id_konselor = $_POST['id_konselor'] ?? '';
$pesan       = $_POST['pesan'] ?? '';

if (empty($id_booking) || empty($pesan)) {
    echo json_encode(["status" => "error", "message" => "id_booking & pesan wajib"]);
    exit;
}

/*
 * LOGIKA BARU:
 * Jika pesan dari WEB KONSELOR → id_user harus NULL
 * Jika pesan dari USER ANDROID → id_konselor TETAP DIISI
 */
if (!empty($id_user)) {
}
else if (!empty($id_konselor)) {
    $id_user = NULL;
}
else {
    echo json_encode(["status" => "error", "message" => "id_user atau id_konselor harus dikirim"]);
    exit();
}

try {
    $query = "INSERT INTO chat (id_booking, id_user, id_konselor, pesan, waktu_kirim)
              VALUES (?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $id_booking, $id_user, $id_konselor, $pesan);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Pesan terkirim",
            "data" => [
                "id_chat" => $conn->insert_id,
                "id_booking" => $id_booking,
                "id_user" => $id_user,
                "id_konselor" => $id_konselor,
                "pesan" => $pesan,
            ]
        ]);
    } else {
        throw new Exception($stmt->error);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
