<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "../../db.php";

// Validasi input
$judul       = $_POST['judul'] ?? '';
$isi         = $_POST['isi'] ?? '';
$link_sumber = $_POST['link_sumber'] ?? '';
$id_konselor = $_POST['id_konselor'] ?? '';

if (!$judul || !$isi || !$id_konselor) {
    echo json_encode([
        "success" => false,
        "message" => "Judul, isi, dan id_konselor wajib diisi!"
    ]);
    exit();
}

// Upload gambar
$gambar = null;

if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {

    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $namaFile = "artikel_" . time() . "." . $ext;

    $target = __DIR__ . "/../../uploads/artikel/" . $namaFile;

    if (!is_dir(__DIR__ . "/../../uploads/artikel/")) {
        mkdir(__DIR__ . "/../../uploads/artikel/", 0777, true);
    }

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $gambar = "uploads/artikel/" . $namaFile;
    }
}

$query = "INSERT INTO artikel (judul, isi, gambar, link_sumber, id_konselor)
          VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssi", $judul, $isi, $gambar, $link_sumber, $id_konselor);

$exec = mysqli_stmt_execute($stmt);

echo json_encode([
    "success" => $exec,
    "message" => $exec ? "Artikel berhasil diupload!" : "Gagal upload artikel",
]);

?>
