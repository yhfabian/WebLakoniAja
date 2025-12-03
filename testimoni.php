<?php
session_start();
include 'db.php';

// Cek login konselor
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];

// Ambil data konselor dari database
$stmt = mysqli_prepare($conn, "SELECT nama, foto FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$konselor = mysqli_fetch_assoc($result);

$nama_konselor = $konselor['nama'] ?? 'Konselor';
$foto = !empty($konselor['foto']) 
    ? 'uploads/' . $konselor['foto'] 
    : 'assets/img/user.png';


// ============================
//  AMBIL DATA TESTIMONI VIA API
// ============================
$api_testimoni = "http://localhost/lakoni_aja/api/get_testimoni.php";
$response = @file_get_contents($api_testimoni);
$data_api = json_decode($response, true);

$testimoni_list = $data_api['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Testimoni - Lakoni Aja</title>

    <link rel="stylesheet" href="assets/css/testimoni.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>

<div class="dashboard-container">

    <!-- === SIDEBAR === -->
    <div class="sidebar">

        <div>
            <a href="dashboard.php" class="item icon top">
                <i class="ri-home-5-line"></i>
            </a>
        </div>

        <div class="menu">
            <a href="jadwalkonselor.php" class="item">
                <i class="ri-calendar-event-line"></i>
            </a>
            <a href="chat.php" class="item">
                <i class="ri-message-3-line"></i>
            </a>
            <a href="testimoni.php" class="item active">
                <i class="ri-chat-smile-3-line"></i>
            </a>
            <a href="rekam_medis.php" class="item">
                <i class="ri-file-list-3-line"></i>
            </a>
            <a href="artikel.php" class="item">
                <i class="ri-article-line"></i>
            </a>
        </div>

        <div>
            <a href="logout.php" class="icon bottom">
                <i class="ri-logout-circle-r-line"></i>
            </a>
        </div>

    </div>


    <!-- === CONTENT === -->
    <div class="content">
        <div class="title">Testimoni <span>Mahasiswa</span></div>

        <div class="testi-container">

            <?php if (empty($testimoni_list)): ?>

                <p style="text-align:center; color:#777; margin-top:20px;">
                    Belum ada testimoni dari mahasiswa.
                </p>

            <?php else: ?>

                <?php foreach ($testimoni_list as $t): ?>
                    
                    <div class="testi-card">

                        <!-- FOTO DEFAULT -->
                        <img src="https://cdn-icons-png.flaticon.com/512/4140/4140037.png" 
                             alt="User" class="avatar">

                        <div class="text">
                            <h4><?= htmlspecialchars($t['nama']) ?></h4>

                            <p><?= nl2br(htmlspecialchars($t['komentar'])) ?></p>

                            <div class="row">
                                <span>🗓 <?= date("d M Y", strtotime($t['tanggal'])) ?></span>
                                <span>💬 Testimoni</span>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>


    <!-- === RIGHT PANEL === -->
    <div class="right-panel">
        <div class="profile-card">
            <img src="<?= $foto ?>" class="profile-img" alt="profile">
            <h3><?= htmlspecialchars($nama_konselor) ?></h3>
            <p class="email">Konselor Polije</p>
        </div>
    </div>

</div>

</body>
</html>
