<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

if (!isset($_GET['id'])) {
    die("ID Monitoring tidak ditemukan.");
}

$id_monitoring = $_GET['id'];

// Ambil data monitoring via API
$api = "http://localhost/lakoni_aja/api/monitoring/detail.php?id=" . $id_monitoring;
$response = file_get_contents($api);
$data = json_decode($response, true);

if (!$data || $data['status'] !== "success") {
    die("Data rekam medis tidak ditemukan.");
}

$m = $data['data'];

// Mulai DOMPDF
$dompdf = new Dompdf();

// HTML untuk PDF
$html = "
<style>
body { font-family: 'Arial'; font-size: 12px; }
h2 { color: #005baa; }
.box {
    border: 1px solid #ccc; 
    border-radius: 6px; 
    padding: 10px; 
    margin-top: 10px;
    background: #f9f9f9;
}
.label {
    font-weight: bold;
    color: #005baa;
}
</style>

<h2>Rekam Medis Konseling</h2>
<hr>

<div class='box'>
    <span class='label'>Nama Mahasiswa:</span> {$m['nama_user']}<br>
    <span class='label'>NIM:</span> {$m['nim_user']}<br>
    <span class='label'>Kelas:</span> {$m['kelas_user']}<br>
</div>

<div class='box'>
    <span class='label'>Konselor:</span> {$m['nama_konselor']}<br>
    <span class='label'>Tanggal Konseling:</span> {$m['tanggal']}<br>
</div>

<h3 style='color:#005baa;'>Catatan Konseling</h3>
<div class='box'>{$m['catatan']}</div>

<h3 style='color:#005baa;'>Diagnosis</h3>
<div class='box'>{$m['diagnosis']}</div>

<h3 style='color:#005baa;'>Rekomendasi</h3>
<div class='box'>{$m['rekomendasi']}</div>

";

// Masukkan HTML ke DOMPDF
$dompdf->loadHtml($html);

// Orientasi dan ukuran kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Download otomatis
$filename = "rekam_medis_" . $m['nama_user'] . "_RM" . $m['id_monitoring'] . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
