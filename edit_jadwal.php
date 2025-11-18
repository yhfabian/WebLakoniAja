<?php
session_start();
include 'db.php';

// Pastikan konselor sudah login
if (!isset($_SESSION['id_konselor'])) {
  header("Location: login.php");
  exit();
}

$id_konselor = $_SESSION['id_konselor'];

// Ambil data konselor
$stmt = mysqli_prepare($conn, "SELECT nama, foto FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$konselor = mysqli_fetch_assoc($result);

$nama_konselor = $konselor['nama'] ?? 'Konselor';
$foto = !empty($konselor['foto']) ? 'uploads/' . $konselor['foto'] : 'assets/img/user.png';

// Ambil ID jadwal
$id_jadwal = $_GET['id'] ?? null;
if (!$id_jadwal) {
  header("Location: form_jadwal.php");
  exit();
}

// Ambil data jadwal
$query = "SELECT * FROM jadwal WHERE id_jadwal = ? AND id_konselor = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_jadwal, $id_konselor);
mysqli_stmt_execute($stmt);
$jadwal = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$jadwal) {
  header("Location: form_jadwal.php");
  exit();
}

// Proses update
$success_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggal = $_POST['tanggal'];
  $jam_mulai = $_POST['jam_mulai'];
  $jam_selesai = $_POST['jam_selesai'];
  $status = $_POST['status'];

  $update = "UPDATE jadwal SET tanggal=?, jam_mulai=?, jam_selesai=?, status=? WHERE id_jadwal=?";
  $stmt = mysqli_prepare($conn, $update);
  mysqli_stmt_bind_param($stmt, "ssssi", $tanggal, $jam_mulai, $jam_selesai, $status, $id_jadwal);
  
  if (mysqli_stmt_execute($stmt)) {
    $success_message = "✅ Jadwal berhasil diperbarui!";
    $jadwal['tanggal'] = $tanggal;
    $jadwal['jam_mulai'] = $jam_mulai;
    $jadwal['jam_selesai'] = $jam_selesai;
    $jadwal['status'] = $status;
  } else {
    $success_message = "❌ Gagal memperbarui jadwal.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Jadwal | LakoniAja</title>

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- CSS utama dan tambahan -->
  <link rel="stylesheet" href="assets/css/styledashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="assets/css/edit_jadwal.css?v=<?php echo time(); ?>">
</head>

<body>
  <div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <img src="assets/img/logo.png" alt="Lakoni Aja">
        <h2>Lakoni<br>Aja</h2>
      </div>

      <nav class="menu">
        <a href="dashboard_konselor.php">🏠<br>Dashboard</a>
        <a href="form_jadwal.php" class="active">📅<br>Jadwal</a>
        <a href="chat.php">💬<br>Chat</a>
        <a href="testimoni.php">⭐<br>Testimoni</a>
        <a href="logout.php" style="color:#ff4b5c;">🚪<br>Logout</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="content">
      <h1 class="title">Edit Jadwal</h1>

      <div class="edit-container">
        <?php if (!empty($success_message)): ?>
          <div class="alert-box"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-edit">
          <label for="tanggal">Tanggal</label>
          <input type="date" name="tanggal" id="tanggal" value="<?= htmlspecialchars($jadwal['tanggal']) ?>" required>

          <label for="jam_mulai">Jam Mulai</label>
          <input type="time" name="jam_mulai" id="jam_mulai" value="<?= htmlspecialchars($jadwal['jam_mulai']) ?>" required>

          <label for="jam_selesai">Jam Selesai</label>
          <input type="time" name="jam_selesai" id="jam_selesai" value="<?= htmlspecialchars($jadwal['jam_selesai']) ?>" required>

          <label for="status">Status</label>
          <select name="status" id="status" required>
            <option value="tersedia" <?= $jadwal['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
            <option value="penuh" <?= $jadwal['status'] == 'penuh' ? 'selected' : '' ?>>Penuh</option>
          </select>

          <div class="form-buttons">
            <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            <a href="form_jadwal.php" class="btn-back">← Kembali</a>
          </div>
        </form>
      </div>
    </main>

    <!-- Right Panel -->
    <aside class="right-panel">
      <div class="profile-card">
        <img src="<?= htmlspecialchars($foto) ?>" class="profile-img" alt="Profile">
        <h3><?= htmlspecialchars($nama_konselor) ?></h3>
        <p class="email">Konselor Polije</p>
      </div>

      <div class="reminder-card">
        <h4>CATATAN</h4>
        <p class="title">Pastikan jadwal sudah diperbarui!</p>
        <span class="desc">Ubah hanya jika jadwal berubah atau ada pembatalan.</span>
      </div>
    </aside>

  </div>
</body>
</html>
