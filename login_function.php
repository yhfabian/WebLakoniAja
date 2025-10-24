<?php
require_once __DIR__ . '/db.php';

function login($username, $password)
{
    global $conn;

    // Cegah SQL Injection
    $username = mysqli_real_escape_string($conn, $username);

    // Ambil data konselor berdasarkan username
    $query = "SELECT * FROM konselor WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        return false; // Username tidak ditemukan
    }

    $user = mysqli_fetch_assoc($result);

    // Verifikasi password yang di-hash
    if (password_verify($password, $user['password'])) {
        // Simpan session (jika kamu pakai sistem session)
        $_SESSION['konselor'] = [
            'id' => $user['id'],
            'nama' => $user['nama'],
            'username' => $user['username'],
            'bidang_keahlian' => $user['bidang_keahlian'],
            'kontak' => $user['kontak']
        ];

        return true;
    } else {
        return false; // Password salah
    }
}
?>
