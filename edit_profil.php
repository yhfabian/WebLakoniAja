<?php
session_start();
include 'db.php';

// Cek login
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$errors = [];

// Ambil data konselor
$stmt = mysqli_prepare($conn, "SELECT * FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$konselor = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Jika form disubmit
if (isset($_POST['update'])) {
    $nama    = trim($_POST['nama']);
    $bidang  = trim($_POST['bidang_keahlian']);
    $kontak  = trim($_POST['kontak']);

    if (empty($nama) || empty($bidang) || empty($kontak)) {
        $errors[] = "Semua field wajib diisi.";
    } else {
        $foto_nama = $konselor['foto']; // foto lama default

        // Cek jika ada upload foto baru
        if (!empty($_FILES['foto']['name'])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $foto_tmp = $_FILES['foto']['tmp_name'];
            $foto_nama = time() . "_" . basename($_FILES['foto']['name']);
            $target_file = $target_dir . $foto_nama;

            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                if (move_uploaded_file($foto_tmp, $target_file)) {
                    // hapus foto lama
                    if (!empty($konselor['foto']) && file_exists("uploads/" . $konselor['foto'])) {
                        unlink("uploads/" . $konselor['foto']);
                    }
                } else {
                    $errors[] = "Gagal mengupload foto baru.";
                }
            } else {
                $errors[] = "Format file tidak valid. Gunakan JPG atau PNG.";
            }
        }

        // Update database jika tidak ada error
        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "UPDATE konselor SET nama=?, bidang_keahlian=?, kontak=?, foto=? WHERE id_konselor=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $nama, $bidang, $kontak, $foto_nama, $id_konselor);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Update session nama untuk navbar
            $_SESSION['nama'] = $nama;

            // Simpan pesan sukses ke session lalu redirect
            $_SESSION['success'] = "Profil berhasil diperbarui!";
            header("Location: dashboard.php");
            exit();
        }
    }
}

$foto = !empty($konselor['foto']) ? 'uploads/' . $konselor['foto'] : 'assets/img/user.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil | LakoniAja</title>
    <link rel="stylesheet" href="assets/css/styledashboard.css?v=1.0">
    <style>
        body { background: #f0f6ff; font-family: 'Poppins', sans-serif; }
        .edit-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .edit-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0066cc;
        }
        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Poppins', sans-serif;
        }
        button {
            margin-top: 20px;
            width: 100%;
            background: #0066cc;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover {
            background: #004c99;
        }
        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .error {
            background: #ffe6e6;
            color: #b30000;
        }
        .foto-preview {
            display: block;
            margin: 10px auto;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #0066cc;
        }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit Profil Konselor</h2>

    <?php if ($errors): ?>
        <div class="alert error"><?= implode("<br>", $errors) ?></div>
    <?php endif; ?>

    <img src="<?= htmlspecialchars($foto) ?>" class="foto-preview" alt="Foto Konselor">

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($konselor['nama']) ?>" required>

        <label>Bidang Keahlian</label>
        <input type="text" name="bidang_keahlian" value="<?= htmlspecialchars($konselor['bidang_keahlian']) ?>" required>

        <label>Kontak (Email)</label>
        <input type="email" name="kontak" value="<?= htmlspecialchars($konselor['kontak']) ?>" required>

        <label>Foto Profil</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit" name="update">Simpan Perubahan</button>
    </form>
</div>

</body>
</html>
