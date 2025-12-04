<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

    if ($username == '' || $new_password == '') {
        echo json_encode([
            'success' => false,
            'message' => 'Semua field harus diisi'
        ]);
        exit;
    }

    if (strlen($new_password) < 8) {
        echo json_encode([
            'success' => false,
            'message' => 'Password baru minimal 8 karakter'
        ]);
        exit;
    }

    $username = mysqli_real_escape_string($conn, $username);

    $check_query = "SELECT * FROM user WHERE username='$username'";
    $check_result = mysqli_query($conn, $check_query);

    if (!$check_result) {
        echo json_encode([
            'success' => false,
            'message' => 'Query gagal: ' . mysqli_error($conn)
        ]);
        exit;
    }

    if (mysqli_num_rows($check_result) > 0) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_query = "UPDATE user SET password='$hashed_new_password' WHERE username='$username'";
        $update_result = mysqli_query($conn, $update_query);

        if ($update_result) {
            echo json_encode([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengubah password: ' . mysqli_error($conn)
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gunakan metode POST!'
    ]);
}
?>