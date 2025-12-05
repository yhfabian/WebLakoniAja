<?php
session_start();
include "db.php";

if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$artikel = $conn->query("SELECT * FROM artikel ORDER BY id_artikel DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Artikel Konselor</title>
    <link rel="stylesheet" href="assets/css/artikel.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

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

<?php if(isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    position: "center",
    icon: "success",
    title: "<?= $_SESSION['success'] ?>",
    showConfirmButton: false,
    timer: 1500
});
</script>
<?php 
unset($_SESSION['success']);
endif; 
?>


<!-- ================= MAIN CONTENT (DESAIN BARU) ================= -->
<div class="container">
    <h2>ARTIKEL</h2>
    <p>Daftar artikel yang Anda upload</p>

    <div id="artikelList" class="artikel-list">

        <?php while($a = $artikel->fetch_assoc()): ?>
        <div class="card">
            <a href="artikel_detail.php?id=<?= $a['id_artikel'] ?>" class="card-link">
                <img src="uploads/artikel/<?= $a['gambar'] ?>" alt="gambar artikel">
                <h3><?= $a['judul'] ?></h3>
                <p><?= substr($a['isi'], 0, 80) ?>...</p>
            </a>

            <div class="card-actions">
                <a href="edit_artikel.php?id=<?= $a['id_artikel'] ?>" class="btn-edit">Edit</a>

                <button class="btn-delete" onclick="confirmDelete(<?= $a['id_artikel'] ?>)">
                    Hapus
                </button>
            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>
</div>


<!-- TOMBOL TAMBAH -->
<a href="artikel_add.php" class="fab">+</a>


<!-- POPUP SUCCESS -->
<div class="popup-overlay" id="popupSuccess">
    <div class="popup-box">
        <div class="check-icon">✔</div>
        <h3 id="popupMessage"></h3>
    </div>
</div>


<!-- POPUP KONFIRMASI HAPUS -->
<div class="popup-confirm" id="popupConfirm">
    <div class="popup-box">
        <h3>Yakin ingin menghapus?</h3>
        <div class="confirm-buttons">
            <button class="btn-yes" id="btnYes">Ya</button>
            <button class="btn-no" onclick="closeConfirm()">Tidak</button>
        </div>
    </div>
</div>

<script>
// ------------------------- POPUP SUCCESS -------------------------
const successMsg = sessionStorage.getItem("success");
if (successMsg) {
    document.getElementById("popupMessage").innerText = successMsg;
    document.getElementById("popupSuccess").style.display = "flex";

    setTimeout(() => {
        document.getElementById("popupSuccess").style.display = "none";
    }, 1500);

    sessionStorage.removeItem("success");
}



// ------------------------- KONFIRMASI HAPUS -------------------------
let deleteID = null;

function confirmDelete(id) {
    deleteID = id;
    document.getElementById("popupConfirm").style.display = "flex";
}

function closeConfirm() {
    document.getElementById("popupConfirm").style.display = "none";
}

document.getElementById("btnYes").onclick = function () {
    if (!deleteID) return;

    fetch("api/artikel/delete.php", {
        method: "POST",
        body: new URLSearchParams({ id_artikel: deleteID })
    })
    .then(res => res.json())
    .then(data => {
        sessionStorage.setItem("success", "Artikel berhasil dihapus");
        location.reload();
    });

    closeConfirm();
};

</script>

</body>
</html>
