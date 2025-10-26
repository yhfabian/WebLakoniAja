<?php
include 'config.php';

$id_sesi = $_GET['id_sesi'] ?? null;

if (!$id_sesi) {
    echo json_encode(["status" => "error", "message" => "ID sesi diperlukan"]);
    exit;
}

$stmt = mysqli_prepare($conn, 
    "SELECT * FROM chat WHERE id_sesi = ? ORDER BY waktu_kirim ASC"
);
mysqli_stmt_bind_param($stmt, "i", $id_sesi);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

echo json_encode(["status" => "success", "data" => $messages]);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
