<?php
header("Content-Type: application/json");
include "../koneksi.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID wajib"]);
    exit;
}

$conn->query("DELETE FROM monitoring WHERE id_monitoring = '$id'");

echo json_encode(["success" => true, "message" => "Rekam medis berhasil dihapus"]);
?>
