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
</head>

<body>

<!-- TAMBAHKIN WRAPPER LAYOUT -->
<div class="layout">

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

    <!-- CONTENT AREA -->
    <div class="content-area">
        <div class="detail-wrapper">
            <div class="detail-container">
                
                <a href="artikel.php" class="btn-back">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>

                <h1 class="detail-title"><?= strtoupper($artikel['judul']) ?></h1>

                <div class="detail-image">
                    <img src="uploads/artikel/<?= $artikel['gambar'] ?>" alt="<?= $artikel['judul'] ?>">
                </div>

                <div class="detail-text">
                    <?= nl2br(htmlspecialchars($artikel['isi'])) ?>
                </div>

                <?php if (!empty($artikel['link_sumber'])): ?>
                    <div class="detail-source">
                        <strong>Sumber Artikel:</strong><br>
                        <a href="<?= $artikel['link_sumber'] ?>" target="_blank">
                            <?= htmlspecialchars($artikel['link_sumber']) ?>
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div> <!-- TUTUP LAYOUT -->

</body>
</html>
