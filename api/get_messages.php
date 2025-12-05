<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Method tidak diizinkan, gunakan POST"
    ]);
    exit();
}

require_once __DIR__ . '/../db.php';

$id_booking = $_POST['id_booking'] ?? '';

if (empty($id_booking)) {
    echo json_encode([
        "status" => "error",
        "message" => "id_booking harus diisi"
    ]);
    exit();
}

try {
    $query = "SELECT c.*, 
                u.nama AS nama_user,
                k.nama AS nama_konselor
              FROM chat c
              LEFT JOIN user u ON c.id_user = u.id_user
              LEFT JOIN konselor k ON c.id_konselor = k.id_konselor
              WHERE c.id_booking = ?
              ORDER BY c.waktu_kirim ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_booking);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];

    while ($row = $result->fetch_assoc()) {

        // LOGIKA PENTING: tentukan pengirim
        if (!empty($row['id_user'])) {
            $pengirim = 'user';
            $nama_pengirim = $row['nama_user'] ?: "User";
        } else {
            $pengirim = 'konselor';
            $nama_pengirim = $row['nama_konselor'] ?: "Konselor";
        }

        $messages[] = [
            "id_chat"       => $row['id_chat'],
            "id_booking"    => $row['id_booking'],
            "id_user"       => $row['id_user'],
            "id_konselor"   => $row['id_konselor'],
            "pesan"         => $row['pesan'],
            "waktu_kirim"   => $row['waktu_kirim'],
            "pengirim"      => $pengirim,
            "nama_pengirim" => $nama_pengirim
        ];
    }

    echo json_encode([
        "status"  => "success",
        "message" => "Data pesan berhasil diambil",
        "data"    => $messages
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil pesan: " . $e->getMessage()
    ]);
}
?>