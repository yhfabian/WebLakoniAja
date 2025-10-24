<?php
session_start();
include 'db.php';

// Cek login
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];

// Ambil data konselor dari database
$stmt = mysqli_prepare($conn, "SELECT * FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$konselor = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Tentukan foto profil (jika tidak ada gunakan default)
$foto_nav = !empty($konselor['foto']) ? 'uploads/' . $konselor['foto'] : 'assets/img/user.png';
$foto_main = !empty($konselor['foto']) ? 'uploads/' . $konselor['foto'] : 'assets/img/user.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Konselor | Lakoni Aja</title>
    <link rel="stylesheet" href="assets/css/styledashboard.css?v=1.0"/>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-left">
            <img src="assets/img/logo.png" alt="Lakoni Aja Logo" class="logo">
            <span>LAKONI AJA</span>
        </div>

        <div class="navbar-right">
            <ul class="nav-links">
                <li><a href="testimoni.php">TESTIMONI</a></li>
                <li><a href="form_jadwal.php">SCHEDULE</a></li>
                <li><a href="#">CHAT</a></li>
                <li><a href="#" class="active">HOME</a></li>
            </ul>

        <div class="user-profile-nav">
    <span><?= htmlspecialchars($konselor['nama']); ?></span>
    <img src="<?= !empty($konselor['foto']) ? 'uploads/' . htmlspecialchars($konselor['foto']) : 'assets/img/user.png'; ?>" 
         alt="User Avatar" class="avatar">
    <i class="fas fa-bell notification-icon"></i>

    <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <!-- KONTEN DASHBOARD -->
     <?php if (isset($_SESSION['success'])): ?>
  <div style="
      background: #e6ffe6;
      color: #008000;
      text-align: center;
      padding: 10px;
      border-radius: 6px;
      margin: 10px auto;
      width: 80%;
      font-weight: 600;
  ">
    <?= htmlspecialchars($_SESSION['success']); ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

    <main class="dashboard-container">
        
        <aside class="sidebar">
            
            <section class="card profile-card">
                <img src="<?= htmlspecialchars($foto_main); ?>" alt="Foto Konselor" class="profile-pic-main">
                <h2><?= htmlspecialchars($konselor['nama']); ?></h2>
                <p><?= htmlspecialchars($konselor['bidang_keahlian']); ?></p>
                <a href="edit_profil.php" class="btn-edit">✏️ Edit Profil</a>

            </section>

            <section class="card mood-tracker">
                <div class="mood-labels">
                    <span>VERY HAPPY</span>
                    <span>HAPPY</span>
                    <span>SAD</span>
                    <span>LAZY</span>
                </div>
                <div class="chart-area">
                    <div class="bar-group">
                        <div class="bar" style="height: 10%; background-color: #d3d3d3;"></div>
                        <span>MON</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar" style="height: 20%;"></div>
                        <span>TUE</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar" style="height: 15%; background-color: #d3d3d3;"></div>
                        <span>WED</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar" style="height: 90%;"></div>
                        <span>THURS</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar" style="height: 8%; background-color: #d3d3d3;"></div>
                        <span>FRI</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar" style="height: 12%; background-color: #d3d3d3;"></div>
                        <span>SAT</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar" style="height: 60%;"></div>
                        <span>SUN</span>
                    </div>
                </div>
            </section>
        </aside>

        <section class="main-content">
            
            <div class="quick-cards">
                <div class="card empty-card">
                    <img src="assets/img/calendar.png" alt="Logo Kalender" style="width: 100px;">
                </div>
                <div class="card empty-card">
                    <img src="assets/img/friend.png" alt="Logo Friend" style="width: 100px;">
                </div>
            </div>

            <section class="card testimonials">
                <h3>TESTIMONI</h3>
                
                <article class="testimonial-item">
                    <div class="author-info">
                        <img src="assets/img/mahasiswa.jpg" alt="Zafar Muhammad" class="avatar-small">
                        <h4>Zafar Muhammad</h4>
                    </div>
                    <p>Sesi konsultasi pertama bersama kak Risa membuat saya jauh lebih tenang.</p>
                    <div class="actions">
                        <span><i class="fas fa-comment"></i> Comments</span>
                        <span><i class="fas fa-heart"></i> Likes</span>
                    </div>
                </article>

                <article class="testimonial-item">
                    <div class="author-info">
                        <img src="assets/img/wanda.png" alt="Wanda Calista" class="avatar-small">
                        <h4>Wanda Calista</h4>
                    </div>
                    <p>Tak butuh uang, butuhnya kasih sayang. Terima kasih konselor polije!</p>
                    <div class="actions">
                        <span><i class="fas fa-comment"></i> Comments</span>
                        <span><i class="fas fa-heart"></i> Likes</span>
                    </div>
                </article>
            </section>
        </section>
    </main>

</body>
</html>
