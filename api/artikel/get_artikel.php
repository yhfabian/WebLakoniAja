<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "../../db.php";

$query = "SELECT a.*, k.nama AS nama_konselor 
          FROM artikel a
          LEFT JOIN konselor k ON a.id_konselor = k.id_konselor
          ORDER BY a.tanggal_dibuat DESC";

$result = mysqli_query($conn, $query);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "id_artikel" => $row["id_artikel"],
        "judul" => $row["judul"],
        "isi" => $row["isi"],
        "gambar" => $row["gambar"] ? "http://localhost/lakoni_aja/" . $row["gambar"] : null,
        "link_sumber" => $row["link_sumber"],
        "tanggal_dibuat" => $row["tanggal_dibuat"],
        "tanggal_update" => $row["tanggal_update"],
        "nama_konselor" => $row["nama_konselor"]
    ];
}

echo json_encode([
    "success" => true,
    "data" => $data
]);
?>
