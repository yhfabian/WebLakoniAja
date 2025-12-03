<?php
header("Content-Type: application/json");
require_once "../db.php";

$id = $_GET['id_artikel'] ?? '';

if ($id == "") {
    echo json_encode([
        "success" => false,
        "message" => "id_artikel wajib dikirim"
    ]);
    exit();
}

$sql = "SELECT a.*, k.nama AS nama_konselor 
        FROM artikel a
        LEFT JOIN konselor k ON a.id_konselor = k.id_konselor
        WHERE a.id_artikel = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($res);

if($data){
    $data['gambar_url'] = $data['gambar']
        ? "http://localhost/lakoni_aja/uploads/artikel/" . $data['gambar']
        : null;

    echo json_encode(["success"=>true, "data"=>$data]);
} else {
    echo json_encode(["success"=>false, "message"=>"Artikel tidak ditemukan"]);
}
?>
