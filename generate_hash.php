<?php
// Tampilkan semua error agar tahu kalau ada masalah
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ganti password di bawah ini sesuai keinginanmu
$password = "11111111";

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h3>Hasil Hash Password:</h3>";
echo "<textarea rows='3' cols='80'>$hash</textarea>";
?>
