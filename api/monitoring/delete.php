<?php
header("Content-Type: application/json");
require "../../db.php";

if (!isset($_GET['id'])) {
    echo json_encode(["status"=>false, "message"=>"id wajib"]);
    exit;
}

$id = $_GET['id'];

$ok = mysqli_query($conn, "DELETE FROM monitoring WHERE id_monitoring = '$id'");

if ($ok) {
    header("Location: ../../rekam_medis.php"); // ← Redirect kembali ke rm
} else {
    echo json_encode(["status"=>false, "message"=>"Gagal menghapus"]);
}
?>
