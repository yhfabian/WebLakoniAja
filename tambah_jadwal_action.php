<?php
// tambah_jadwal_action.php

session_start();
require_once 'db.php'; // untuk fallback

// Pastikan login konselor
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = (int)$_SESSION['id_konselor'];

// Terima POST dari form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: jadwalkonselor.php");
    exit();
}

$tanggal = $_POST['tanggal'] ?? '';
$jam_mulai = $_POST['jam_mulai'] ?? '';
$jam_selesai = $_POST['jam_selesai'] ?? '';
$status = $_POST['status'] ?? 'Tersedia';

// Validasi input
if (!$tanggal || !$jam_mulai || !$jam_selesai) {
    header("Location: jadwalkonselor.php?error=" . urlencode("Semua field wajib diisi."));
    exit();
}

// OPTION 1: Coba akses API dulu
$api_success = false;
$api_url = "http://localhost/WeblakoniAja/api/jadwal_tambah.php";

// Gunakan CURL jika ada
if (function_exists('curl_version')) {
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'id_konselor' => $id_konselor,
        'tanggal' => $tanggal,
        'jam_mulai' => $jam_mulai,
        'jam_selesai' => $jam_selesai,
        'status' => $status
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($response !== false && $http_code == 200) {
        $json = json_decode($response, true);
        if ($json && isset($json['success']) && $json['success'] === true) {
            $api_success = true;
            // Redirect dengan success
            header("Location: jadwalkonselor.php?success=1");
            exit();
        } else {
            $error_msg = $json['message'] ?? "API gagal tanpa pesan";
        }
    } else {
        $error_msg = "API tidak dapat diakses (HTTP: $http_code)";
    }
    curl_close($ch);
}

// OPTION 2: Fallback ke insert langsung ke database
if (!$api_success) {
    // Insert langsung ke database
    $query = "INSERT INTO jadwal (id_konselor, tanggal, jam_mulai, jam_selesai, status) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", 
            $id_konselor, 
            $tanggal, 
            $jam_mulai, 
            $jam_selesai, 
            $status
        );
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: jadwalkonselor.php?success=1");
        } else {
            $error_msg = "Gagal menambah jadwal: " . mysqli_error($conn);
            header("Location: jadwalkonselor.php?error=" . urlencode($error_msg));
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Database error: " . mysqli_error($conn);
        header("Location: jadwalkonselor.php?error=" . urlencode($error_msg));
    }
}

exit();
?>