<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Gunakan metode POST"
    ]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Alternatively, you can use form data
$id_user          = $input['id_user'] ?? $_POST['id_user'] ?? '';
$id_konselor      = $input['id_konselor'] ?? $_POST['id_konselor'] ?? '';
$jenis_konseling  = $input['jenis_konseling'] ?? $_POST['jenis_konseling'] ?? '';
$tanggal          = $input['tanggal'] ?? $_POST['tanggal'] ?? '';
$jam_mulai        = $input['jam_mulai'] ?? $_POST['jam_mulai'] ?? '';

// Debug log
error_log("Booking Request: id_user=$id_user, id_konselor=$id_konselor, jenis_konseling=$jenis_konseling, tanggal=$tanggal, jam_mulai=$jam_mulai");

if (empty($id_user) || empty($id_konselor) || empty($jenis_konseling) || empty($tanggal) || empty($jam_mulai)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Semua field wajib diisi"
    ]);
    exit();
}

// Escape parameters
$id_user = $conn->real_escape_string($id_user);
$id_konselor = $conn->real_escape_string($id_konselor);
$jenis_konseling = $conn->real_escape_string($jenis_konseling);
$tanggal = $conn->real_escape_string($tanggal);
$jam_mulai = $conn->real_escape_string($jam_mulai);

// ======================================
//  Cari ID Jadwal berdasarkan tanggal + jam + id_konselor
// ======================================

$q = "
    SELECT id_jadwal 
    FROM jadwal 
    WHERE tanggal = '$tanggal' 
      AND jam_mulai = '$jam_mulai'
      AND id_konselor = '$id_konselor'
      AND status = 'tersedia'
";

$res = $conn->query($q);

if (!$res) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
    exit();
}

if ($res->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Jadwal tidak tersedia / sudah dipesan"
    ]);
    exit();
}

$row = $res->fetch_assoc();
$id_jadwal = $row['id_jadwal'];

// Gabungkan tanggal_booking
$tanggal_booking = date('Y-m-d H:i:s'); // Waktu sekarang

// ======================================
//  Insert ke tabel booking
// ======================================

$insert = "
    INSERT INTO booking (id_user, id_jadwal, jenis_konseling, tanggal_booking)
    VALUES ('$id_user', '$id_jadwal', '$jenis_konseling', '$tanggal_booking')
";

if ($conn->query($insert)) {
    // Update status jadwal menjadi 'dipesan'
    $update_jadwal = $conn->query("
        UPDATE jadwal 
        SET status = 'dipesan' 
        WHERE id_jadwal = '$id_jadwal'
    ");
    
    if ($update_jadwal) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Booking berhasil",
            "data" => [
                "id_booking" => $conn->insert_id,
                "id_jadwal" => $id_jadwal
            ]
        ]);
    } else {
        // Rollback booking jika update jadwal gagal
        $conn->query("DELETE FROM booking WHERE id_jadwal = '$id_jadwal'");
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal update status jadwal: " . $conn->error
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal booking: " . $conn->error
    ]);
}

$conn->close();
?>