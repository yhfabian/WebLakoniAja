<?php
session_start();
include 'db.php'; // tetap dipakai untuk fallback jika API down

// Pastikan konselor login
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}
$id_konselor = (int) $_SESSION['id_konselor'];

// Ambil data konselor dari DB (nama + foto) — cepat dan pasti
$stmt = mysqli_prepare($conn, "SELECT nama, foto FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$konselor = mysqli_fetch_assoc($res);
$nama_konselor = $konselor['nama'] ?? 'Konselor';
$foto = !empty($konselor['foto']) ? 'uploads/' . $konselor['foto'] : 'assets/img/user.png';

// --------------- Ambil data jadwal via API terlebih dahulu ---------------
$jadwals = [];
$api_url = (strpos($_SERVER['HTTP_HOST'],'localhost') !== false || strpos($_SERVER['HTTP_HOST'],'127.0.0.1') !== false)
    ? "http://{$_SERVER['HTTP_HOST']}/WeblakoniAja/api/jadwal_list.php?id_konselor={$id_konselor}"
    : "/api/jadwal_list.php?id_konselor={$id_konselor}";

// try file_get_contents
$raw = false;
if (ini_get('allow_url_fopen')) {
    $raw = @file_get_contents($api_url);
}

// fallback ke cURL kalau file_get_contents fail
if ($raw === false) {
    if (function_exists('curl_version')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $raw = curl_exec($ch);
        curl_close($ch);
    }
}

// decode JSON jika ada
if ($raw) {
    $json = @json_decode($raw, true);
    if (isset($json['status']) && $json['status'] === 'success' && isset($json['data']) && is_array($json['data'])) {
        $jadwals = $json['data'];
    } else {
        // jika API merespon object lain atau error, fallback ke DB query langsung
        $jadwals = [];
    }
}

// fallback ke query DB langsung bila $jadwals kosong
if (empty($jadwals)) {
    $q = "SELECT * FROM jadwal WHERE id_konselor = ? ORDER BY tanggal ASC, jam_mulai ASC";
    $st = mysqli_prepare($conn, $q);
    mysqli_stmt_bind_param($st, "i", $id_konselor);
    mysqli_stmt_execute($st);
    $r = mysqli_stmt_get_result($st);
    while ($row = mysqli_fetch_assoc($r)) {
        $jadwals[] = $row;
    }
}

// Pesan sukses dari redirect tambah_jadwal_action.php
$successMessage = '';
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $successMessage = 'Jadwal berhasil ditambahkan!';
}
if (isset($_GET['error'])) {
    $errorMessage = $_GET['error'];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Layanan Konseling</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/jadwalkonselor.css?v=<?php echo time(); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body>

<div class="layout">
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

<div class="main-content">

  <?php if (!empty($successMessage)): ?>
    <div class="toast"><?= htmlspecialchars($successMessage) ?></div>
  <?php endif; ?>
  <?php if (!empty($errorMessage)): ?>
    <div class="alert-success" style="background:#ffdede;border:1px solid #e66; color:#700; margin:10px 20px; padding:10px; border-radius:8px;">
      <?= htmlspecialchars($errorMessage) ?>
    </div>
  <?php endif; ?>

  <section class="container">
    <div class="header-section">
      <h2>LAYANAN KONSELING</h2>
      <div class="search-box">
        <input type="text" id="search" placeholder="Cari jadwal (contoh: Monday, 10:00, 2025-11-18)">
        <button id="addBtn" class="btn-add">+</button>
      </div>
    </div>

    <div class="content">
      <div class="card konselor">
        <h3><?= htmlspecialchars($nama_konselor) ?></h3>
        <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Konselor" class="profile-img">
        <p class="stars">konselor</p>
      </div>

      <div class="card jadwal">
        <h3>JADWAL <?= strtoupper(htmlspecialchars($nama_konselor)) ?></h3>

        <div class="filter-jadwal">
          <select id="minggu">
            <option value="all">Semua Minggu</option>
            <option value="1">Minggu ke-1</option>
            <option value="2">Minggu ke-2</option>
            <option value="3">Minggu ke-3</option>
            <option value="4">Minggu ke-4</option>
          </select>

          <select id="bulan">
            <option value="all">Semua Bulan</option>
            <?php
              $bulan = [
                1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
              ];
              foreach ($bulan as $num=>$nama) {
                $sel = ($num == date('n')) ? 'selected' : '';
                echo "<option value=\"$num\" $sel>$nama</option>";
              }
            ?>
          </select>
        </div>

        <div id="jadwalList">
          <?php if (!empty($jadwals)): ?>
            <?php foreach ($jadwals as $row): ?>
              <?php
                // pastikan keys ada
                $tgl_raw = $row['tanggal'] ?? null;
                $jam_mulai_raw = $row['jam_mulai'] ?? $row['jam_mulai'] ?? null;
                $jam_selesai_raw = $row['jam_selesai'] ?? null;
                $status_raw = strtolower($row['status'] ?? 'tersedia');

                $tanggal_human = $tgl_raw ? date("l, d M Y", strtotime($tgl_raw)) : "-";
                $jam_mulai = $jam_mulai_raw ? date("H:i", strtotime($jam_mulai_raw)) : "--:--";
                $jam_selesai = $jam_selesai_raw ? date("H:i", strtotime($jam_selesai_raw)) : "--:--";
              ?>
              <div class="jadwal-item <?= htmlspecialchars($status_raw) ?>" data-tanggal="<?= htmlspecialchars($tgl_raw) ?>">
                <p><?= htmlspecialchars($tanggal_human) ?> — <?= htmlspecialchars($jam_mulai) ?> - <?= htmlspecialchars($jam_selesai) ?> WIB</p>
                <div class="status-label <?= htmlspecialchars($status_raw) ?>"><?= ucfirst(htmlspecialchars($status_raw)) ?></div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="no-data">Belum ada jadwal yang tersedia.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    </div>
  </section>

  <!-- POPUP TAMBAH JADWAL (form POST ke tambah_jadwal_action.php) -->
  <div id="popup" class="popup" aria-hidden="true">
    <div class="popup-content">
      <h3>Tambah Jadwal Baru</h3>
      <form id="addForm" method="POST" action="tambah_jadwal_action.php">
        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" required>

        <label for="jam_mulai">Jam Mulai</label>
        <input type="time" name="jam_mulai" required>

        <label for="jam_selesai">Jam Selesai</label>
        <input type="time" name="jam_selesai" required>

        <label for="status">Status</label>
        <select name="status">
          <option value="tersedia">Tersedia</option>
          <option value="dipesan">Dipesan</option>
          <option value="selesai">Selesai</option>
        </select>

        <div class="btn-group">
          <button type="submit" class="btn-save">Simpan</button>
          <button type="button" id="closePopup" class="btn-cancel">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  // Popup
  const addBtn = document.getElementById("addBtn");
  const popup = document.getElementById("popup");
  const closePopup = document.getElementById("closePopup");
  addBtn.addEventListener("click", ()=> popup.style.display = "flex");
  closePopup.addEventListener("click", ()=> popup.style.display = "none");

  // Filter minggu & bulan
  function getWeekOfMonth(date) {
    const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    const dayOfWeek = firstDay.getDay();
    return Math.ceil((date.getDate() + dayOfWeek) / 7);
  }
  function filterJadwal() {
    const minggu = document.getElementById('minggu').value;
    const bulan = document.getElementById('bulan').value;
    const items = document.querySelectorAll('.jadwal-item');
    items.forEach(item=>{
      const tanggalStr = item.getAttribute('data-tanggal');
      if (!tanggalStr) { item.style.display = 'none'; return; }
      const d = new Date(tanggalStr);
      const itemMonth = d.getMonth()+1;
      const itemWeek = getWeekOfMonth(d);
      const matchBulan = (bulan === 'all' || itemMonth == bulan);
      const matchMinggu = (minggu === 'all' || itemWeek == minggu);
      item.style.display = (matchBulan && matchMinggu) ? 'block' : 'none';
    });
  }
  document.getElementById('minggu').addEventListener('change', filterJadwal);
  document.getElementById('bulan').addEventListener('change', filterJadwal);

  // Search (pencarian jadwal)
  document.getElementById('search').addEventListener('input', function(){
    const kw = this.value.trim().toLowerCase();
    document.querySelectorAll('.jadwal-item').forEach(item=>{
      const text = item.querySelector('p').textContent.toLowerCase();
      item.style.display = text.includes(kw) ? 'block' : 'none';
    });
  });

  // Auto hide toast
  const toast = document.querySelector('.toast');
  if (toast) setTimeout(()=> toast.classList.add('hide'), 3000);
</script>

</body>
</html>
