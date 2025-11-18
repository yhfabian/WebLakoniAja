<?php
session_start();
include 'db.php';

// pastikan login konselor
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = (int)$_SESSION['id_konselor'];

// menerima POST dari form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: jadwalkonselor.php");
    exit();
}

$tanggal = $_POST['tanggal'] ?? '';
$jam_mulai = $_POST['jam_mulai'] ?? '';
$jam_selesai = $_POST['jam_selesai'] ?? '';
$status = $_POST['status'] ?? 'tersedia';

// validasi
if (!$tanggal || !$jam_mulai || !$jam_selesai) {
    header("Location: jadwalkonselor.php?error=" . urlencode("Semua field wajib diisi."));
    exit();
}

// API endpoint
$api_url = "http://localhost/lakoni_aja/api/jadwal_tambah.php";

// POST context (tanpa cURL)
$postData = http_build_query([
    'id_konselor' => $id_konselor,
    'tanggal' => $tanggal,
    'jam_mulai' => $jam_mulai,
    'jam_selesai' => $jam_selesai,
    'status' => $status
]);

$options = [
    'http' => [
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'timeout' => 5,
        'content' => $postData
    ]
];

$context  = stream_context_create($options);
$response = @file_get_contents($api_url, false, $context);

// jika gagal request
if ($response === false) {
    header("Location: jadwalkonselor.php?error=" . urlencode("API tidak dapat diakses."));
    exit();
}

$json = json_decode($response, true);

// cek hasil API
if ($json && isset($json['status']) && $json['status'] === "success") {
    header("Location: jadwalkonselor.php?success=1");
    exit();
} else {
    $err = $json['message'] ?? "Gagal menambah jadwal.";
    header("Location: jadwalkonselor.php?error=" . urlencode($err));
    exit();
}
