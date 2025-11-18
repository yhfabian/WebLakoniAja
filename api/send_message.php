<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menggunakan koneksi dari db.php yang sudah ada
    $conn = $GLOBALS['conn'];
    
    $input = json_decode(file_get_contents("php://input"), true);
    
    if(!empty($input['id_user']) && !empty($input['id_konselor']) && 
       !empty($input['id_sesi']) && !empty($input['pesan'])) {
        
        try {
            $query = "INSERT INTO chat (id_user, id_konselor, id_sesi, pesan, waktu_kirim) 
                     VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $conn->prepare($query);
            
            // Bind parameters dengan tipe data yang sesuai
            $stmt->bind_param("iiis", 
                $input['id_user'], 
                $input['id_konselor'], 
                $input['id_sesi'], 
                $input['pesan']
            );
            
            if($stmt->execute()) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Pesan terkirim",
                    "id_chat" => $conn->insert_id
                ]);
            } else {
                throw new Exception($stmt->error);
            }
            
        } catch(Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal mengirim: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Data tidak lengkap"
        ]);
    }
}
?>