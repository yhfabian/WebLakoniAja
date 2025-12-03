<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

// ==============================
//     AMBIL DATA DARI DATABASE
// ==============================

// Total user
$totalUser = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM user")
)['total'];

// Total konselor
$totalKonselor = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM konselor")
)['total'];

// Total jadwal
$totalJadwal = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM jadwal")
)['total'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/css/dashboard_admin.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="sidebar">
    <div class="list_sidebar">
        <h2>Dashboard Admin</h2>
        <a href="dashboard_admin.php" class="active">📊 Dashboard</a>
        <a href="kelola_user.php">👤 Kelola User</a>
        <a href="kelola_konselor.php">🧑‍⚕️ Kelola Konselor</a>
        <a href="kelola_jadwal.php">📅 Kelola Jadwal</a>
    </div>

    <div class="keluar">
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="content">
    
    <div class="header-bar">
        <div class="Title">
            <h2>Lakoni Aja</h2>
        </div>

        <div class="user-profile">
            <span>Admin Petugas</span>
            <div class="avatar">A</div>
        </div>
    </div>

    <h1>Dashboard</h1>
    <p class="subtitle">Selamat datang di panel admin konseling</p>

    <div class="card-container">

        <!-- Card Total User -->
        <div class="card">
            <div>
                <h3>Total User</h3>
                <div class="value"><?= $totalUser ?></div>
            </div>
            <div class="card-icon">👥</div>
        </div>

        <!-- Card Total Konselor -->
        <div class="card">
            <div>
                <h3>Total Konselor</h3>
                <div class="value"><?= $totalKonselor ?></div>
            </div>
            <div class="card-icon">🧠</div>
        </div>

        <!-- Card Total Jadwal -->
        <div class="card">
            <div>
                <h3>Jadwal Aktif</h3>
                <div class="value"><?= $totalJadwal ?></div>
            </div>
            <div class="card-icon">📅</div>
        </div>

    </div>

    <footer class="footer">
        <p>© 2025 Lakoni Aja - Sistem Konseling Polije</p>
    </footer>

</div>

</body>
</html>
