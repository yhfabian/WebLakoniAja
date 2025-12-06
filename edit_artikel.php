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

// Ambil data artikel
$stmt = $conn->prepare("SELECT * FROM artikel WHERE id_artikel = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$artikel = $result->fetch_assoc();

if (!$artikel) {
    die("Artikel tidak ditemukan.");
}

// === UPDATE ARTIKEL ===
if (isset($_POST['update'])) {
    $judul  = $_POST['judul'];
    $isi    = $_POST['isi'];
    $sumber = $_POST['link_sumber'];
    $gambar = $artikel['gambar']; // default gambar lama

    // Jika upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $namaFile = time() . "_" . $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $folder = "uploads/artikel/" . $namaFile;

        if (move_uploaded_file($tmp, $folder)) {
            $gambar = $namaFile;
        }
    }

    $stmt2 = $conn->prepare("UPDATE artikel SET judul=?, isi=?, link_sumber=?, gambar=? WHERE id_artikel=?");
    $stmt2->bind_param("ssssi", $judul, $isi, $sumber, $gambar, $id);
    $stmt2->execute();

    $_SESSION['success'] = "Artikel berhasil diperbarui!";
header("Location: artikel.php");
exit();

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Artikel</title>
    <link rel="stylesheet" href="assets/css/artikel.css?v=<?php echo time(); ?>">
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
        <div class="form-page">
            <div class="form-box">

                <h2>Edit Artikel</h2>

                <form method="POST" enctype="multipart/form-data">

                    <label>Judul Artikel</label>
                    <input 
                        type="text"
                        name="judul"
                        value="<?= htmlspecialchars($artikel['judul']) ?>"
                        required
                        oninput="this.value = this.value.toUpperCase()"
                    >

                    <label>Isi Artikel</label>
                    <textarea name="isi" rows="5" required><?= htmlspecialchars($artikel['isi']) ?></textarea>

                    <label>Link Sumber</label>
                    <input 
                        type="text"
                        name="link_sumber"
                        value="<?= htmlspecialchars($artikel['link_sumber']) ?>"
                        required
                    >

                    <label>Gambar Saat Ini</label>
                    <br>
                    <img src="uploads/artikel/<?= $artikel['gambar'] ?>" width="180" style="border-radius:10px; margin:10px 0;">
                    <br>

                    <label>Upload Gambar Baru (opsional)</label>
                    <input type="file" name="gambar" accept="image/*">

                    <div class="form-buttons">
                        <button type="submit" name="update" class="btn-save">Simpan</button>
                        <a href="artikel.php" class="btn-cancel">Batal</a>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div> <!-- TUTUP DIV LAYOUT -->

</body>
</html>
