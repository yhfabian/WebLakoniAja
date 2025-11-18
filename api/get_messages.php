<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . '/../db.php';

// Menggunakan koneksi dari db.php
$conn = $GLOBALS['conn'];

$id_user = $_GET['id_user'] ?? '';
$id_konselor = $_GET['id_konselor'] ?? '';
$id_sesi = $_GET['id_sesi'] ?? '';

if(!empty($id_user) && !empty($id_konselor) && !empty($id_sesi)) {
    try {
        $query = "SELECT c.*, u.nama as nama_user, k.nama as nama_konselor 
                 FROM chat c
                 LEFT JOIN user u ON c.id_user = u.id_user
                 LEFT JOIN konselor k ON c.id_konselor = k.id_konselor
                 WHERE c.id_sesi = ? 
                 ORDER BY c.waktu_kirim ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_sesi);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $messages = [];
        
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                "id_chat" => $row['id_chat'],
                "id_user" => $row['id_user'],
                "id_konselor" => $row['id_konselor'],
                "pesan" => $row['pesan'],
                "waktu_kirim" => $row['waktu_kirim'],
                "pengirim" => ($row['id_user'] == $id_user) ? 'user' : 'konselor',
                "nama_pengirim" => ($row['id_user'] == $id_user) ? $row['nama_user'] : $row['nama_konselor']
            ];
        }
        
        echo json_encode([
            "status" => "success",
            "data" => $messages
        ]);
        
    } catch(Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Parameter tidak lengkap"
    ]);
}
?>