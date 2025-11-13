<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username == '' || $password == '') {
        echo json_encode([
            'success' => false,
            'message' => 'Field tidak boleh kosong'
        ]);
        exit;
    }

    $username = mysqli_real_escape_string($conn, $username);

    // Ambil data user berdasarkan username (tabel 'user')
    $query = "SELECT * FROM user WHERE username='$username'";
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

        // Cek password dengan bcrypt
        if (password_verify($password, $data['password'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil!',
                'data' => [
                    'nama' => $data['nama'],
                    'username' => $data['username'],
                    'email' => $data['email']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Password salah!'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Username tidak ditemukan!'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gunakan metode POST!'
    ]);
}
?>