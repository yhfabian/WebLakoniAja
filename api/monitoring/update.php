<?php
header("Content-Type: application/json");
require "../../db.php";

if (!isset($_POST['id_monitoring'])) {
    echo json_encode(["status" => false, "message" => "id_monitoring wajib"]);
    exit;
}

$id = $_POST['id_monitoring'];
$tanggal = $_POST['tanggal'];
$catatan = $_POST['catatan'];
$diagnosis = $_POST['diagnosis'];
$rekomendasi = $_POST['rekomendasi'];

$query = "
UPDATE monitoring SET 
    tanggal = '$tanggal',
    catatan = '$catatan',
    diagnosis = '$diagnosis',
    rekomendasi = '$rekomendasi'
WHERE id_monitoring = '$id'
";

$update = mysqli_query($conn, $query);

if ($update) {
    
    // 🔥 Redirect kembali ke halaman detail rekam medis
    header("Location: ../../rm_detail.php?id=" . $id);
    exit;

} else {
    echo json_encode(["status" => false, "message" => "Gagal update data"]);
}
?>
