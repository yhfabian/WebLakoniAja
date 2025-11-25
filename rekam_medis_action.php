<?php
session_start();

if (!isset($_SESSION['id_konselor'])) {
    die("Akses ditolak.");
}

$id_konselor = $_SESSION['id_konselor'];

// API tujuan
$url = "http://localhost/lakoni_aja/api/monitoring/add.php";

// Data POST dari form
$data = [
    "id_user" => $_POST["id_user"],
    "id_konselor" => $id_konselor,
    "tanggal" => $_POST["tanggal"],
    "catatan" => $_POST["catatan"],
    "diagnosis" => $_POST["diagnosis"],
    "rekomendasi" => $_POST["rekomendasi"]
];

// Kirim ke API menggunakan stream context (karena cURL tidak aktif di PC kamu)
$options = [
    "http" => [
        "header"  => "Content-Type: application/x-www-form-urlencoded",
        "method"  => "POST",
        "content" => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

$result = json_decode($response, true);

// Jika sukses kembali ke halaman rekam_medis.php
if ($result && $result["status"] === true) {
    header("Location: rekam_medis.php?success=1");
    exit();
}

// Jika gagal
header("Location: rekam_medis.php?error=1");
exit();
