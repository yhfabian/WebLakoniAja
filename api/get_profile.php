<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';

    if ($username == '') {
        echo json_encode([
            'success' => false,
            'message' => 'Username harus diisi'
        ]);
        exit;
    }

    $username = mysqli_real_escape_string($conn, $username);

    // Ambil data user berdasarkan username (tabel 'user')
    $query = "SELECT id_user, nama, nim, email, no_hp, tanggal_lahir, username 
              FROM user WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Query gagal: ' . mysqli_error($conn)
        ]);
        exit;
    }

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);

        // Format data yang NULL
        $no_hp = ($data['no_hp'] == null || $data['no_hp'] == 'NULL') ? '' : $data['no_hp'];
        $tanggal_lahir = ($data['tanggal_lahir'] == null || $data['tanggal_lahir'] == '0000-00-00') ? '' : $data['tanggal_lahir'];

        echo json_encode([
            'success' => true,
            'user' => [
                'id_user' => $data['id_user'],
                'nama' => $data['nama'],
                'nim' => $data['nim'],
                'email' => $data['email'],
                'no_hp' => $no_hp,
                'tanggal_lahir' => $tanggal_lahir,
                'username' => $data['username']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User tidak ditemukan!'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gunakan metode POST!'
    ]);
}
?>