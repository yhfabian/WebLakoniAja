<?php
session_start();
include "db.php";

if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID artikel tidak ditemukan.");
}

$id = intval($_GET['id']);

$stmt = mysqli_prepare($conn, "SELECT * FROM artikel WHERE id_artikel = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$artikel = mysqli_fetch_assoc($result);

if (!$artikel) {
    die("Artikel tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Artikel</title>
    <link rel="stylesheet" href="assets/css/artikel.css?v=<?= time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        /* ---- Wrapper agar berada di tengah ---- */
        .detail-wrapper {
            display: flex;
            justify-content: center;
            margin-left: 120px; /* memberi ruang sidebar */
            padding: 40px 20px;
        }

        /* ---- Card utama ---- */
        .detail-container {
            width: 70%;
            background: #ffffff;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }

        .btn-back {
            background: #1a73e8;
            padding: 8px 16px;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
        }

        .detail-title {
            text-align: center;
            margin-top: 20px;
            color: #0a3ea1;
        }

        /* ---- Gambar berada di tengah ---- */
        .detail-image {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 25px 0;
        }

        .detail-image img {
            width: 90%;
            border-radius: 12px;
            object-fit: cover;
        }

        .detail-content {
            text-align: justify;
            color: #333;
            font-size: 16px;
            line-height: 1.7;
        }
    </style>

</head>

<body>

<!-- SIDEBAR -->
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
        <a href="testimoni.php" class="item">
            <i class="ri-chat-smile-3-line"></i>
        </a>
        <a href="rekam_medis.php" class="item">
            <i class="ri-file-list-3-line"></i>
        </a>
        <a href="artikel.php" class="item active">
            <i class="ri-article-line"></i>
        </a>
    </div>

    <div>
        <a href="logout.php" class="icon bottom">
            <i class="ri-logout-circle-r-line"></i>
        </a>
    </div>
</div>


<!-- MAIN CONTENT -->
<div class="detail-wrapper">

    <div class="detail-container">
        
        <a href="artikel.php" class="btn-back">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>

        <h1 class="detail-title"><?= strtoupper($artikel['judul']) ?></h1>

        <img src="uploads/artikel/<?= $artikel['gambar'] ?>" class="detail-image">

        <p class="detail-text">
            <?= nl2br($artikel['isi']) ?>
        </p>

        <?php if (!empty($artikel['link_sumber'])): ?>
            <p class="detail-source">
                <strong>Sumber Artikel:</strong><br>
                <a href="<?= $artikel['link_sumber'] ?>" target="_blank">
                    <?= $artikel['link_sumber'] ?>
                </a>
            </p>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
