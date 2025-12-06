<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lakoni Aja</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/index.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Dancing+Script:wght@500&display=swap" rel="stylesheet">
</head>
<body>
  <!-- HEADER -->
  <header>
    <div class="logo">
      <img src="assets/img/logo2.png" alt="Lakoni Aja Logo">
      <h1>LAKONI AJA</h1>
    </div>
    <nav class="navbar">
      <ul class="nav-list">

        <!-- Dropdown Login -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle login-dropdown" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Login
          </a>
          <ul class="dropdown-menu" aria-labelledby="loginDropdown">
            <li><a class="dropdown-item" href="login_admin.php">Login Admin</a></li>
            <li><a class="dropdown-item" href="login.php">Login Konselor</a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <!-- HERO SECTION -->
  <section class="hero">
    <div class="hero-content">
      <h2>RUANG AMAN UNTUK<br>KESEHATAN MENTAL MAHASISWA</h2>
      <h3>Politeknik Negeri Jember</h3>
      <p>
        Platform ini hadir sebagai ruang aman, nyaman, dan mudah diakses oleh mahasiswa Polije 
        untuk mendapatkan layanan konseling secara digital sebagai pendukung kesehatan mental seorang mahasiswa.
      </p>
    </div>
    <div class="hero-image">
      <img src="assets/img/mhs.png" alt="Mahasiswa">
    </div>
  </section>

  <!-- SECTION TIPS -->
  <section class="tips">
    <h2>GIMANA SIH CARA JAGA KESEHATAN MENTAL?</h2>

    <div class="tips-container">
      <div class="tip-card">
        <div class="icon">🕒</div>
        <h3>Kelola Waktu Dengan Bijak</h3>
        <p>Jadwal rapi, hati pun damai, stres berkurang fokus tercapai.</p>
      </div>

      <div class="tip-card">
        <div class="icon">🧑‍🤝‍🧑</div>
        <h3>Cerita Pada Orang Yang Tepat</h3>
        <p>Berbagi cerita dengan konselor bijak, pikiran lega masalah pun menipis sekejap.</p>
      </div>

      <div class="tip-card">
        <div class="icon">❤️</div>
        <h3>Rawat Diri Secara Konsisten</h3>
        <p>Tidur cukup, makan teratur, olahraga ringan bikin jiwa selalu makmur.</p>
      </div>
    </div>
  </section>
</body>
</html>
