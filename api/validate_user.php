<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $tanggal_lahir = isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '';

    if ($email == '' || $tanggal_lahir == '') {
        echo json_encode([
            'success' => false,
            'message' => 'Semua field harus diisi'
        ]);
        exit;
    }

    // Convert format tanggal dari DD/MM/YYYY ke YYYY-MM-DD
    $tanggal_lahir_db = DateTime::createFromFormat('d/m/Y', $tanggal_lahir);
    if ($tanggal_lahir_db === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Format tanggal lahir tidak valid. Gunakan format DD/MM/YYYY'
        ]);
        exit;
    }
    $tanggal_lahir_db = $tanggal_lahir_db->format('Y-m-d');

    $email = mysqli_real_escape_string($conn, $email);

    $query = "SELECT username, email, tanggal_lahir FROM user WHERE email='$email' AND tanggal_lahir='$tanggal_lahir_db'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Query gagal: ' . mysqli_error($conn)
        ]);
        exit;
    }

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result); // PERBAIKAN: $result bukan $data
        
        echo json_encode([
            'success' => true,
            'message' => 'User valid',
            'user' => [
                'username' => $data['username'],
                'email' => $data['email']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Email atau tanggal lahir tidak sesuai'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gunakan metode POST!'
    ]);
}
?>