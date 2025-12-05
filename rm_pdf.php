<?php
// MATIKAN ERROR AGAR PDF TIDAK CORRUPT
error_reporting(0);
ini_set('display_errors', 0);

ob_start();

require "vendor/autoload.php";
require "db.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// =======================
// CEK ID
// =======================
if (!isset($_GET['id'])) {
    die("ID monitoring tidak ditemukan.");
}
$id = $_GET['id'];

// =======================
// AMBIL DATA MONITORING
// =======================
$query = "
SELECT m.*, 
       u.nama AS nama_user,
       u.nim AS nim_user,
       u.email AS email_user,
       u.no_hp AS kontak_user,
       k.nama AS nama_konselor
FROM monitoring m
JOIN user u ON m.id_user = u.id_user
JOIN konselor k ON m.id_konselor = k.id_konselor
WHERE m.id_monitoring = '$id'
";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data tidak ditemukan.");
}

// =======================
// LOGO BASE64 FIX
// =======================

$logo_path = __DIR__ . "/assets/img/polije.jpg";   // <<< PERBAIKAN DI SINI

if (file_exists($logo_path)) {
    $logo_base64 = base64_encode(file_get_contents($logo_path));
    $logo_src = "data:image/png;base64," . $logo_base64;
} else {
    $logo_src = "";
}

// =======================
// HTML PDF
// =======================
$html = "

<style>
body { font-family: sans-serif; font-size:12px; }
.header { text-align:center; }
.header img { width:90px; margin-bottom:5px; }
.title { font-size:20px; font-weight:bold; color:#1c6dd0; margin:10px 0; }

.section {
    padding:10px;
    background:#f6f9ff;
    border:1px solid #d8e4ff;
    border-radius:8px;
    margin-bottom:12px;
}
.section-title {
    font-weight:bold;
    color:#1c6dd0;
    margin-bottom:6px;
}
</style>

<div class='header'>
    <img src='$logo_src'>
    <div class='title'>Rekam Medis Konseling</div>
</div>

<div class='section'>
    <div class='section-title'>Informasi Mahasiswa</div>
    <p><b>Nama:</b> {$data['nama_user']}</p>
    <p><b>NIM:</b> {$data['nim_user']}</p>
    <p><b>email:</b> {$data['email_user']}</p>
    <p><b>Kontak:</b> {$data['kontak_user']}</p>
</div>

<div class='section'>
    <div class='section-title'>Informasi Konseling</div>
    <p><b>Konselor:</b> {$data['nama_konselor']}</p>
    <p><b>Tanggal:</b> {$data['tanggal']}</p>
</div>

<div class='section'>
    <div class='section-title'>Catatan Konseling</div>
    " . nl2br($data['catatan']) . "
</div>

<div class='section'>
    <div class='section-title'>Diagnosis</div>
    " . nl2br($data['diagnosis']) . "
</div>

<div class='section'>
    <div class='section-title'>Rekomendasi</div>
    " . nl2br($data['rekomendasi']) . "
</div>
";

// =======================
// RENDER PDF
// =======================
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

ob_clean();

$dompdf->stream("rekam_medis_$id.pdf", ["Attachment" => false]);
exit;
