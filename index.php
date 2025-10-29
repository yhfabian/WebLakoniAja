<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ruang Polije - Konseling</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; }
    .hero {
      background: linear-gradient(to right, #e0f0ff, #ffffff);
      padding: 60px 20px;
    }
    .hero h1 { font-weight: bold; color: #002B5B; }
    .feature-card {
      border-radius: 15px;
      padding: 20px;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .feature-card h4 { color: #002B5B; }
  </style>
</head>
<body>

<!-- Header / Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Lakoni Aja</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Home</a>
        </li>

        <!-- Dropdown Login -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Login
          </a>
          <ul class="dropdown-menu" aria-labelledby="loginDropdown">
            <li><a class="dropdown-item" href="login_admin.php">Login Admin</a></li>
            <li><a class="dropdown-item" href="login.php">Login Konselor</a></li>
          </ul>
        </li>

        <!-- Tombol daftar -->
        <li class="nav-item">
          <a class="btn btn-primary ms-2" href="register.php">Daftar Konselor</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero text-center">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-7 text-start">
        <h1>Ruang Aman Untuk Kesehatan Mental Mahasiswa</h1>
        <p>Platform ini hadir sebagai ruang aman, nyaman, dan mudah diakses mahasiswa Polije untuk layanan konseling digital.</p>
        
      </div>
      <div class="col-md-5">
          <img src="assets/img/logo2.png" alt="Logo Polije" style="width:200px;">
      </div>
    </div>
  </div>
</section>

<!-- Fitur Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-card">
          <h4>Chat</h4>
          <p>Curhat cepat tanpa sekat, aman dan nyaman setiap saat.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <h4>Booking Konseling</h4>
          <p>Jadwal konseling tercatat rapi dan otomatis sesuai aktivitas mahasiswa.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <h4>Testimoni</h4>
          <p>Bagikan pengalaman secara pribadi maupun umum, bangun dukungan positif.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Tambahkan di bawah semua elemen, sebelum </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



