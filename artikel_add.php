<?php
session_start();
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Artikel</title>
    <link rel="stylesheet" href="assets/css/artikel.css?v=<?php echo time();?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>

<!-- TAMBAHKIN DIV LAYOUT INI -->
<div class="layout">

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

    <!-- TAMBAHKIN DIV CONTENT-AREA INI -->
    <div class="content-area">
        <div class="main-content">
            <h1>Tambah Artikel</h1>

            <form action="api/artikel/upload.php" method="POST" enctype="multipart/form-data" class="form-box">
                <input type="hidden" name="id_konselor" value="<?= $_SESSION['id_konselor'] ?>">

                <label>Judul Artikel</label>
                <input type="text" name="judul" required>

                <label>Isi Artikel</label>
                <textarea name="isi" required></textarea>

                <label>Link Sumber Artikel</label>
                <input type="text" name="link_sumber">

                <label>Upload Gambar</label>
                <input type="file" name="gambar" accept="image/*">

                <div class="form-buttons">
                    <button type="submit" class="btn-save">Simpan Artikel</button>
                    <a href="artikel.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div> <!-- TUTUP DIV LAYOUT -->

</body>
</html>
