<?php
session_start();
include 'db.php';

// Jika belum login
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$errors = [];
$success = "";

// Ambil data konselor dari database
$stmt = mysqli_prepare($conn, "SELECT * FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $konselor = mysqli_fetch_assoc($result);
} else {
    // Jika data tidak ditemukan
    $konselor = [
        'nama' => 'Konselor Tidak Dikenal',
        'bidang_keahlian' => '-',
        'kontak' => '-',
        'foto' => 'assets/img/default_user.png'
    ];
}

// Simpan jadwal baru
if (isset($_POST['simpan'])) {
    $tanggal     = $_POST['tanggal'];
    $jam_mulai   = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $status      = $_POST['status'];

    if (empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
        $errors[] = "Semua field wajib diisi.";
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO jadwal (id_konselor, tanggal, jam_mulai, jam_selesai, status)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "issss", $id_konselor, $tanggal, $jam_mulai, $jam_selesai, $status);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $success = "Jadwal berhasil ditambahkan!";
        } else {
            $errors[] = "Gagal menambahkan jadwal.";
        }
        mysqli_stmt_close($stmt);
    }
}
$foto = !empty($konselor['foto']) ? 'uploads/' . $konselor['foto'] : 'assets/img/user.png';

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Jadwal | LakoniAja</title>
  <link rel="stylesheet" href="assets/css/form_jadwal.css?v=2.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="navbar">
  <div class="logo">
    <img src="assets/img/logo.png" alt="Logo">
    <h2>LAKONI AJA</h2>
  </div>
  <nav>
    <a href="#">TESTIMONI</a>
    <a href="form_jadwal.php" class="active">SCHEDULE</a>
    <a href="#">CHAT</a>
    <a href="dashboard.php">HOME</a>
  </nav>
  <div class="user">
    <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Konselor">
    <span><?= htmlspecialchars($_SESSION['nama']); ?></span>
  </div>
</header>

<main class="content">
  <h1 class="section-title">LAYANAN KONSELING</h1>

  <!-- Search bar -->
  <div class="search-box">
    <input type="text" placeholder="🔍  Search">
  </div>

  <div class="container">
    <!-- Kartu profil konselor -->
   <div class="card profile">
  <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Konselor">
  <h2><?= htmlspecialchars($konselor['nama']) ?></h2>
  <p><?= htmlspecialchars($konselor['bidang_keahlian']) ?></p>
  <p style="color: #555; font-size: 14px;">
      <i><?= htmlspecialchars($konselor['kontak']) ?></i>
  </p>
</div>


    <!-- Kartu form jadwal -->
    <div class="card schedule">
      <h2>JADWAL BOOKING</h2>

      <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($errors): ?>
        <div class="alert">
          <?php foreach ($errors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <label for="tanggal">Tanggal Konseling</label>
        <input type="date" id="tanggal" name="tanggal" required>

        <label for="jam_mulai">Jam Mulai</label>
        <input type="time" id="jam_mulai" name="jam_mulai" required>

        <label for="jam_selesai">Jam Selesai</label>
        <input type="time" id="jam_selesai" name="jam_selesai" required>

        <label for="status">Status</label>
        <select id="status" name="status" required>
          <option value="Tersedia">Tersedia</option>
          <option value="Dipesan">Dipesan</option>
          <option value="Selesai">Selesai</option>
        </select>

        <button type="submit" name="simpan">Simpan Jadwal</button>
      </form>
    </div>
  </div>
</main>

</body>
</html>
