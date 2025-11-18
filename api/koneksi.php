<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "lakoni_aja";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]));
}
?>
