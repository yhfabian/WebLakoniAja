<?php
require_once __DIR__ . '/db.php';

function register($nama, $username, $password, $nip, $bidang_keahlian, $kontak)
{
    global $conn;

    // Validasi sederhana
    if (empty($nama) || empty($username) || empty($password) || empty($kontak)) {
        return false;
    }

    // Validasi email
    if (!filter_var($kontak, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Enkripsi password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $check = mysqli_query($conn, "SELECT * FROM konselor WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        return false; // Username sudah terdaftar
    }

    // Insert data ke tabel
    $query = "INSERT INTO konselor (nama, username, password, nip, bidang_keahlian, kontak)
              VALUES ('$nama', '$username', '$hashedPassword', '$nip', '$bidang_keahlian', '$kontak')";
    
    if (mysqli_query($conn, $query)) {
        return true;
    } else {
        return false;
    }
}
?>
