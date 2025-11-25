<?php
header("Content-Type: application/json");
require "../../db.php";

// cek data wajib
if (!isset($_POST['id_user']) || !isset($_POST['id_konselor']) || !isset($_POST['tanggal'])) {
    echo json_encode(["status"=>false, "message"=>"id_user, id_konselor, tanggal wajib"]);
    exit;
}

$id_user = $_POST['id_user'];
$id_konselor = $_POST['id_konselor'];
$tanggal = $_POST['tanggal'];
$catatan = $_POST['catatan'] ?? "";
$diagnosis = $_POST['diagnosis'] ?? "";
$rekomendasi = $_POST['rekomendasi'] ?? "";

// query insert
$query = "
INSERT INTO monitoring (id_user, id_konselor, tanggal, catatan, diagnosis, rekomendasi)
VALUES ('$id_user', '$id_konselor', '$tanggal', '$catatan', '$diagnosis', '$rekomendasi')
";

$ok = mysqli_query($conn, $query);

echo json_encode([
    "status" => $ok ? true : false,
    "message" => $ok ? "Berhasil menambah rekam medis" : "Gagal menambah rekam medis"
]);

// redirect jika akses via form browser
if ($ok) {
    header("Location: ../../rekam_medis.php?success=1");
    exit;
}
