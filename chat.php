<?php
session_start();
include 'db.php';

// Pastikan konselor login
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$nama_konselor = $_SESSION['nama'] ?? "Konselor";
$id_sesi = $_GET['id_sesi'] ?? 2;
$id_user_aktif = $_GET['id_user'] ?? 1;

// --------------------
// Ambil daftar user yang pernah chat
// --------------------
$query = "
    SELECT 
        u.id_user, 
        u.nama, 
        u.foto, 
        MAX(c.pesan) AS pesan, 
        MAX(c.waktu_kirim) AS waktu_kirim
    FROM chat c
    JOIN user u ON c.id_user = u.id_user
    WHERE c.id_konselor = ?
    GROUP BY u.id_user
    ORDER BY waktu_kirim DESC
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result_users = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Chat | LakoniAja</title>
  <link rel="stylesheet" href="assets/css/chat.css?v=3.0">
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">
      <img src="assets/img/logo.png" alt="Logo">
      <h2>LAKONI AJA</h2>
    </div>
    <nav>
      <a href="dashboard.php">HOME</a>
      <a href="form_jadwal.php">SCHEDULE</a>
      <a href="chat.php" class="active">CHAT</a>
      <a href="testimoni.php">TESTIMONI</a>
      <a href="logout.php">LOGOUT</a>
    </nav>
  </header>

  <!-- Wrapper -->
  <main class="chat-wrapper">

    <!-- SIDEBAR USER -->
    <aside class="sidebar-users">
      <h3>Daftar Konseli</h3>
      <?php while ($u = mysqli_fetch_assoc($result_users)): 
        $foto_user = !empty($u['foto']) ? 'uploads/' . $u['foto'] : 'assets/img/default_user.png';

        // Tentukan status online (<= 5 menit terakhir)
        $selisih_detik = time() - strtotime($u['waktu_kirim']);
        $status_online = ($selisih_detik <= 300) ? "online" : "offline"; // 300 detik = 5 menit
      ?>
      <div class="user-card <?= ($u['id_user'] == $id_user_aktif) ? 'active' : '' ?>" 
           onclick="window.location='chat.php?id_user=<?= $u['id_user'] ?>'">

        <div class="user-avatar">
          <img src="<?= htmlspecialchars($foto_user) ?>" alt="<?= htmlspecialchars($u['nama']) ?>">
          <span class="status-dot <?= $status_online ?>"></span>
        </div>

        <div class="user-info">
          <h4><?= htmlspecialchars($u['nama']) ?></h4>
          <p><?= htmlspecialchars(substr($u['pesan'], 0, 35)) ?>...</p>
        </div>
        <span class="time"><?= date("H:i", strtotime($u['waktu_kirim'])) ?></span>
      </div>
      <?php endwhile; ?>
    </aside>

    <!-- AREA CHAT -->
    <section class="chat-container">
      <div class="chat-header">
        <h2>💬 Chat Konseling</h2>
        <p>Login sebagai <b><?= htmlspecialchars($nama_konselor) ?></b></p>
      </div>

      <div id="chat-box" class="chat-box">
        <p class="loading">Memuat pesan...</p>
      </div>

      <form id="chat-form" class="chat-form">
        <input type="hidden" id="id_sesi" value="<?= htmlspecialchars($id_sesi) ?>">
        <input type="hidden" id="id_konselor" value="<?= htmlspecialchars($id_konselor) ?>">
        <input type="hidden" id="id_user" value="<?= htmlspecialchars($id_user_aktif) ?>">
        <input type="text" id="pesan" placeholder="Ketik pesan..." autocomplete="off" required>
        <button type="submit">Kirim</button>
      </form>
    </section>

  </main>

  <script>
  const chatBox = document.getElementById("chat-box");
  const chatForm = document.getElementById("chat-form");
  const API_URL = "http://localhost/lakoni_aja/api/";

  async function loadMessages() {
    const idSesi = document.getElementById("id_sesi").value;
    const idUser = document.getElementById("id_user").value;
    const res = await fetch(`${API_URL}get_messages.php?id_sesi=${idSesi}&id_user=${idUser}`);
    const data = await res.json();

    if (data.status === "success") {
      chatBox.innerHTML = "";
      data.data.forEach(msg => {
        const div = document.createElement("div");
        div.classList.add("message");
        div.classList.add(msg.id_konselor ? "from-konselor" : "from-user");
        div.innerHTML = `<p>${msg.pesan}</p><small>${msg.waktu_kirim}</small>`;
        chatBox.appendChild(div);
      });
      chatBox.scrollTop = chatBox.scrollHeight;
    }
  }

  chatForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
      id_user: document.getElementById("id_user").value,
      id_konselor: document.getElementById("id_konselor").value,
      id_sesi: document.getElementById("id_sesi").value,
      pesan: document.getElementById("pesan").value
    };

    const res = await fetch(`${API_URL}send_message.php`, {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(payload)
    });

    const result = await res.json();
    if (result.status === "success") {
      document.getElementById("pesan").value = "";
      loadMessages();
    } else {
      alert(result.message);
    }
  });

  setInterval(loadMessages, 3000);
  loadMessages();
  </script>
</body>
</html>
