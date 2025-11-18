<?php
session_start();
include 'db.php';

// Pastikan user atau konselor sudah login
if (!isset($_SESSION['id_konselor']) && !isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}


// Proses simpan testimoni
if (isset($_POST['simpan'])) {
    $pesan = trim($_POST['pesan']);
    $rating = $_POST['rating'];

    if (empty($pesan)) {
        $errors[] = "Pesan testimoni tidak boleh kosong.";
    } else {
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO testimoni (id_pengirim, tipe_pengirim, pesan, rating, tanggal)
             VALUES (?, ?, ?, ?, NOW())"
        );
        mysqli_stmt_bind_param($stmt, "issi", $id_pengirim, $tipe_pengirim, $pesan, $rating);
        $simpan = mysqli_stmt_execute($stmt);

        if ($simpan) {
            $success = "Testimoni berhasil dikirim!";
        } else {
            $errors[] = "Gagal menyimpan testimoni.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Testimoni - Lakoni Aja</title>
  <link rel="stylesheet" href="assets/css/form_testimoni.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
  <div class="dashboard">
    <header class="header">
      <div class="logo">
        <h3>LAKONI AJA</h3>
      </div>
      <nav>
        <ul>
                <li><a href="testimoni.php" class="active">TESTIMONI</a></li>
                <li><a href="form_jadwal.php">SCHEDULE</a></li>
                <li><a href="chat.php">CHAT</a></li>
                <li><a href="dashboard.php">HOME</a></li>
        </ul>
      </nav>
      <div class="user">
        <h4>RISA RAHMAWATI</h4>
        <img src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png" alt="User">
      </div>
    </header>

    <main class="content">
      <h2 class="section-title">TESTIMONI</h2>

      <div class="testimoni-container">
        <!-- Testimoni 1 -->
        <div class="testimoni-card">
          <img src="https://randomuser.me/api/portraits/men/10.jpg" class="avatar" alt="Zafar">
          <div class="text-content">
            <h4>Zafar Muhammad</h4>
            <p>Sesi konsultasi pertama bersama kak Risa membuat saya jauh lebih tenang dan termotivasi.</p>
            <div class="actions">
              <span>💬 12 Comments</span>
              <span>❤️ 58 Likes</span>
            </div>
          </div>
        </div>

        <!-- Testimoni 2 -->
        <div class="testimoni-card">
          <img src="https://randomuser.me/api/portraits/women/21.jpg" class="avatar" alt="Wanda">
          <div class="text-content">
            <h4>Wanda Calista</h4>
            <p>Tak butuh uang, butuhnya kasih sayang. Terima kasih konselor paling sabar & tulus 💙</p>
            <div class="actions">
              <span>💬 7 Comments</span>
              <span>❤️ 45 Likes</span>
            </div>
          </div>
        </div>

        <!-- Testimoni 3 -->
        <div class="testimoni-card">
          <img src="https://randomuser.me/api/portraits/men/19.jpg" class="avatar" alt="Rizky">
          <div class="text-content">
            <h4>Rizky Andriansyah</h4>
            <p>Bimbingan yang menenangkan dan solutif. Sekarang saya bisa berpikir lebih positif tiap harinya.</p>
            <div class="actions">
              <span>💬 9 Comments</span>
              <span>❤️ 62 Likes</span>
            </div>
          </div>
        </div>

        <!-- Testimoni 4 -->
        <div class="testimoni-card">
          <img src="https://randomuser.me/api/portraits/women/44.jpg" class="avatar" alt="Nadia">
          <div class="text-content">
            <h4>Nadia Putri</h4>
            <p>Kak Risa benar-benar pendengar yang baik. Saya merasa dihargai dan didengarkan sepenuhnya 🕊️</p>
            <div class="actions">
              <span>💬 10 Comments</span>
              <span>❤️ 70 Likes</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>

