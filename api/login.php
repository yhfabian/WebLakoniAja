<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $login = isset($_POST['login']) ? $_POST['login'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($login == '' || $password == '') {
        echo json_encode([
            'success' => false,
            'message' => 'Field tidak boleh kosong'
        ]);
        exit;
    }

    $login = mysqli_real_escape_string($conn, $login);

    // Cek user berdasarkan username atau email
    $query = "SELECT * FROM user WHERE username='$login' OR email='$login'";
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

        if (password_verify($password, $data['password'])) {

            // Kirimkan id_user juga
            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil!',
                'user' => [
                    'id_user' => $data['id_user'],      // Diperbaiki
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
            'message' => 'Username/Email tidak ditemukan!'
        ]);
    }

} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gunakan metode POST!'
    ]);
}
?>