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

// Simpan data ke variabel
$nama_konselor = $konselor['nama'] ?? 'Konselor';
$foto = !empty($konselor['foto']) 
    ? 'uploads/' . $konselor['foto'] 
    : 'assets/img/user.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Konselor</title>
  <link rel="stylesheet" href="assets/css/styledashboard.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="dashboard-container">

    <!-- === SIDEBAR === -->
    <aside class="sidebar">
      <div class="logo">
        <img src="assets/img/logo.png" alt="Lakoni Aja">
        <h2>Lakoni<br>Aja</h2>
      </div>

      <nav class="menu">
        <a href="dashboard_konselor.php" class="active">🏠<br>Dashboard</a>
        <a href="jadwalkonselor.php">📅<br>Jadwal</a>
        <a href="chat.php">💬<br>Chat</a>
        <a href="testimoni.php">⭐<br>Testimoni</a>
        <a href="logout.php" style="color:#ff4b5c;">🚪<br>Logout</a>
      </nav>
    </aside>

    <!-- === MAIN CONTENT === -->
    <main class="content">
      <h1 class="title">Halo, <span><?= htmlspecialchars($nama_konselor) ?>!</span></h1>

      <!-- Welcome Section -->
      <section class="main-dashboard">
        <div class="welcome-card">
          <div class="welcome-left">
            <h3>Selamat Datang Kembali 🌼</h3>
            <p>Semoga harimu menyenangkan! Yuk cek jadwal konseling hari ini.</p>
            <button class="add-btn" onclick="window.location.href='jadwalkonselor.php'">+</button>
          </div>
        </div>

        <div class="result-card">
          <h3>Gimana Mood Kamu Minggu Ini?</h3>
          <p>Jaga semangat ya, <?= htmlspecialchars($nama_konselor) ?> 🌸</p>

          <div class="chart">
            <div class="chart-item"><span>Bahagia</span><div class="bar blue" style="width:80%"></div></div>
            <div class="chart-item"><span>Capek</span><div class="bar green" style="width:50%"></div></div>
            <div class="chart-item"><span>Tenang</span><div class="bar yellow" style="width:70%"></div></div>
          </div>
        </div>
      </section>

      <!-- Menu Cards -->
      <div class="menu-cards">
        <div class="card" onclick="window.location.href='rekamedis.php'">
          <img src="https://cdn-icons-png.flaticon.com/512/4305/4305456.png" alt="rekamedis">
          <p>Reka Medis</p>
        </div>
        <div class="card" onclick="window.location.href='mood.php'">
          <img src="https://cdn-icons-png.flaticon.com/512/2921/2921822.png" alt="mood">
          <p>Penambah Mood</p>
        </div>
       <div class="card" onclick="window.location.href='jadwalkonselor.php'">

          <img src="https://cdn-icons-png.flaticon.com/512/2947/2947990.png" alt="jadwal">
          <p>Jadwal Saya</p>
        </div>
      </div>

      <!-- Calendar -->
      <div class="calendar-card">
        <h3>Kalender Kegiatan</h3>
        <iframe 
          src="https://calendar.google.com/calendar/embed?src=id.indonesian%23holiday%40group.v.calendar.google.com&ctz=Asia%2FJakarta" 
          style="border:0" 
          width="100%" 
          height="300" 
          frameborder="0" 
          scrolling="no">
        </iframe>
      </div>
    </main>

    <!-- === RIGHT PANEL === -->
    <aside class="right-panel">
      <div class="profile-card">
        <img src="<?= $foto ?>" class="profile-img" alt="profile">
        <h3><?= htmlspecialchars($nama_konselor) ?></h3>
        <p class="email">Konselor Polije</p>
       
      </div>

      <div class="reminder-card">
        <h4>CATATAN</h4>
        <p class="title">JANGAN LUPA REKA MEDISNYA!!!</p>
        <span class="desc">Rabu, 5 Oktober 2025 — rekapan data medis harus diselesaikan.</span>
      </div>
    </aside>

  </div>
</body>
</html>
