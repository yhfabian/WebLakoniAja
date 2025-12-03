<?php
include "../../db.php";  // ← FIX PATH

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$id_konselor  = $_POST['id_konselor'];
$judul        = $_POST['judul'];
$isi          = $_POST['isi'];
$link_sumber  = $_POST['link_sumber'];

$filename = null;

// --- Upload gambar jika ada ---
if (!empty($_FILES['gambar']['name'])) {

    $dir = "../../uploads/artikel/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = time() . "_" . rand(1000,9999) . "." . $ext;
    $path = $dir . $filename;

    move_uploaded_file($_FILES['gambar']['tmp_name'], $path);
}

$sql = "INSERT INTO artikel (id_konselor, judul, isi, gambar, link_sumber)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "issss", $id_konselor, $judul, $isi, $filename, $link_sumber);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    header("Location: ../../artikel.php"); // ← Redirect kembali ke artikel
    exit;
}

echo json_encode(["success" => false, "message" => "Gagal menyimpan artikel"]);
