<?php
session_start();
if (!isset($_SESSION['id_konselor'])) {
    die("Akses ditolak. Harap login sebagai konselor.");
}

$id_konselor = $_SESSION['id_konselor'];

// API rekam medis
$api_url = "http://localhost/WeblakoniAja/api/monitoring/list.php?id_konselor=" . $id_konselor;
$response = file_get_contents($api_url);
$data = json_decode($response, true);

$monitoring = $data['data'] ?? [];

// API list user (untuk popup tambah)
$user_list = file_get_contents("http://localhost/WeblakoniAja/api/monitoring/user_list.php");
$users = json_decode($user_list, true)['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekam Medis Konselor</title>
    <link rel="stylesheet" href="assets/css/rekam_medis.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>

<div class="page-container">

    <!-- ====================== SIDEBAR ======================= -->
    <div class="sidebar">

    <div>
        <a href="dashboard.php" class="item icon top">
            <i class="ri-home-5-line"></i>
        </a>
    </div>

    <div class="menu">
        <a href="jadwalkonselor.php" class="item active">
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

    <!-- ====================== CONTENT AREA ======================= -->
    <div class="content-area">

        <!-- HEADER + SEARCH -->
        <div class="arsip-header">
            <h2>Daftar Rekam Medis</h2>
            <input type="text" id="searchBox" class="search-box" placeholder="Cari nama / NIM...">
        </div>

        <!-- ====================== CARD LIST ======================= -->
        <div class="card-grid" id="cardGrid">

            <?php 
            // LOOP DATA MONITORING (DARI API)
            foreach ($monitoring as $m): 
            
                // inisial dari nama
                $initial = strtoupper(substr($m["nama_user"], 0, 1));
            ?>

            <div class="arsip-card item-card" 
                 data-nama="<?= strtolower($m['nama_user']) ?>"
                 data-nim="<?= strtolower($m['nim_user']) ?>">

                <div class="card-top">
                    <div class="initials"><?= $initial ?></div>

                    <div>
                        <h3><?= $m['nama_user'] ?></h3>
                        <p>NIM: <?= $m['nim_user'] ?></p>
                    </div>
                </div>

                <div class="card-info">
                    <p><strong>Email:</strong> <?= $m['email_user'] ?></p>
                    <p><strong>Diagnosis:</strong> <?= $m['diagnosis'] ?></p>
                </div>

                <a href="rm_detail.php?id=<?= $m['id_monitoring'] ?>" class="btn-detail">
                    Lihat Detail
                </a>
            </div>

            <?php endforeach; ?>

        </div> <!-- end card-grid -->

    </div> <!-- end content-area -->


    <!-- ====================== TOMBOL ADD (+) ======================= -->
    <button class="floating-btn" onclick="openPopup()">+</button>




    <!-- ====================== POPUP FORM ======================= -->
    <div class="popup" id="popup">
        <div class="popup-content">

            <h3>Tambah Rekam Medis</h3>

            <form action="api/monitoring/add.php" method="POST">

                <input type="hidden" name="id_konselor" value="<?= $id_konselor ?>">

                <label class="label">Mahasiswa</label>
                <select name="id_user" class="input" required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php
                    $users = file_get_contents("http://localhost/weblakoniaja/api/monitoring/user_list.php");
                    $users = json_decode($users, true)['data'] ?? [];
                    foreach ($users as $u):
                    ?>
                        <option value="<?= $u['id_user'] ?>">
                            <?= $u['nama'] ?> (<?= $u['nim'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="label">Tanggal Konseling</label>
                <input type="date" name="tanggal" class="input" required>

                <label class="label">Catatan Konseling</label>
                <textarea name="catatan" class="textarea" required></textarea>

                <label class="label">Diagnosis</label>
                <input type="text" name="diagnosis" class="input">

                <label class="label">Rekomendasi</label>
                <textarea name="rekomendasi" class="textarea"></textarea>

                <div class="btn-group">
                    <button class="btn-save" type="submit">Simpan</button>
                    <button class="btn-cancel" type="button" onclick="closePopup()">Batal</button>
                </div>

            </form>

        </div>
    </div>



</div> <!-- end container -->



<!-- ====================== JAVASCRIPT ======================= -->
<script>
function openPopup() {
    document.getElementById("popup").style.display = "flex";
}
function closePopup() {
    document.getElementById("popup").style.display = "none";
}

/* ================= SEARCH FUNCTION ================= */
document.getElementById("searchBox").addEventListener("keyup", function () {
    let keyword = this.value.toLowerCase();
    let cards = document.querySelectorAll(".item-card");

    cards.forEach(card => {
        let nama = card.getAttribute("data-nama");
        let nim  = card.getAttribute("data-nim");

        if (nama.includes(keyword) || nim.includes(keyword)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
});
</script>

</body>
</html>