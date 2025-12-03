<?php
include "../../db.php";

$id = $_POST['id_artikel'];

$stmt = $conn->prepare("DELETE FROM artikel WHERE id_artikel = ?");
$stmt->bind_param("i", $id);

echo json_encode([
    "status" => $stmt->execute()
]);
?>
