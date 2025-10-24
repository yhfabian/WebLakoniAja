<?php
$host = "localhost";   // server database
$user = "root";        // username MySQL (default: root)
$pass = "";            // password MySQL (kosong jika default di XAMPP/Laragon)
$db   = "lakoni_aja";  // nama database

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("❌ Koneksi gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Debug (opsional, matikan di production)
# echo "✅ Koneksi berhasil ke database $db";

// Pastikan koneksi tersedia secara global
$GLOBALS['conn'] = $conn;
?>
