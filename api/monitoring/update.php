<?php
header("Content-Type: application/json");
include "../koneksi.php";

$id = $_POST['id_monitoring'];
$tanggal = $_POST['tanggal'];
$catatan = $_POST['catatan'];
$diagnosis = $_POST['diagnosis'];
$rekomendasi = $_POST['rekomendasi'];

$stmt = $conn->prepare("
    UPDATE monitoring 
    SET tanggal=?, catatan=?, diagnosis=?, rekomendasi=?
    WHERE id_monitoring=?
");
$stmt->bind_param("ssssi", $tanggal, $catatan, $diagnosis, $rekomendasi, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Data berhasil diperbarui"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui data"]);
}
?>
