<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

// --- Hapus jadwal ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal WHERE id_jadwal = $id");
    $_SESSION['success'] = "Jadwal berhasil dihapus!";
    header("Location: kelola_jadwal.php");
    exit();
}

// --- Ambil semua jadwal konseling ---
$query = "
    SELECT j.*, k.nama AS nama_konselor 
    FROM jadwal j 
    JOIN konselor k ON j.id_konselor = k.id_konselor 
    ORDER BY j.tanggal DESC, j.jam_mulai ASC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Jadwal</title>

    <link rel="stylesheet" href="assets/css/dashboard_admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/kelola_jadwal.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <div class="list_sidebar">
        <h2>Dashboard Admin</h2>
        <a href="dashboard_admin.php">📊 Dashboard</a>
        <a href="kelola_user.php">👤 Kelola User</a>
        <a href="kelola_konselor.php">🧑‍⚕️ Kelola Konselor</a>
        <a href="kelola_jadwal.php" class="active">📅 Kelola Jadwal</a>
    </div>
    <div class="keluar">
        <a class="logout" href="logout.php">Logout</a>
    </div>
</div>

<div class="content">

    <div class="main-header">
        <h1 class="brand-title">Lakoni Aja</h1>
        <div class="profile-section">
            <span class="profile-name">Admin Petugas</span>
            <div class="profile-avatar">A</div>
        </div>
    </div>

    <hr class="header-line">

    <h1>Kelola Jadwal</h1>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="table-container">

        <table class="table-jadwal">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Konselor</th>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Status</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php 
            $no = 1;
            if (mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)):
            ?>

                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_konselor']); ?></td>
                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
                    <td><?= htmlspecialchars(substr($row['jam_mulai'], 0, 5)); ?></td>
                    <td><?= htmlspecialchars(substr($row['jam_selesai'], 0, 5)); ?></td>

                    <td>
                        <?php 
                            $status = strtolower($row['status']);
                            $class = ($status == "tersedia") ? 'status-tersedia' : 'status-booked';
                        ?>
                        <span class="<?= $class ?>"><?= $row['status']; ?></span>
                    </td>

                    <td>
                        <a href="edit_jadwal.php?id=<?= $row['id_jadwal']; ?>" 
                           class="btn-edit">Edit</a>

                        <a href="kelola_jadwal.php?hapus=<?= $row['id_jadwal']; ?>"
                           onclick="return confirm('Yakin ingin menghapus jadwal ini?')"
                           class="btn-delete">Hapus</a>
                    </td>
                </tr>

            <?php 
                endwhile;
            else:
            ?>

                <tr>
                    <td colspan="7" style="text-align:center;">Tidak ada jadwal ditemukan.</td>
                </tr>

            <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<footer class="footer">
    <p>© 2025 Lakoni Aja - Sistem Konseling Polije</p>
</footer>

</body>
</html>
