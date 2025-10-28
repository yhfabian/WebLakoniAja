<?php
session_start();
include 'db.php';

// Cek login konselor
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$nama_konselor = $_SESSION['nama'] ?? "Konselor";

// Ambil ID sesi dari query (contoh: chat.php?id_sesi=1)
$id_sesi = $_GET['id_sesi'] ?? 2;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Chat | LakoniAja</title>
  <link rel="stylesheet" href="assets/css/chat.css?v=1.0">
</head>
<body>
  <header class="navbar">
    <div class="logo">
      <img src="assets/img/logo.png" alt="Logo">
      <h2>LAKONI AJA</h2>
    </div>
    <nav>
      <a href="dashboard.php">HOME</a>
      <a href="form_jadwal.php">BOOKING</a>
      <a href="chat.php" class="active">CHAT</a>
      <a href="testimoni.php">TESTIMONI</a>
      <a href="logout.php">LOGOUT</a>
    </nav>
  </header>

  <main class="chat-container">
    <div class="chat-header">
      <h2>💬 Chat Sesi #<?= htmlspecialchars($id_sesi) ?></h2>
      <p>Anda login sebagai: <b><?= htmlspecialchars($nama_konselor) ?></b></p>
    </div>

    <div id="chat-box" class="chat-box">
      <p class="loading">Memuat pesan...</p>
    </div>

    <form id="chat-form" class="chat-form">
      <input type="hidden" id="id_sesi" value="2">
      <input type="hidden" id="id_konselor" value="<?= $id_konselor ?>">
      <input type="hidden" id="id_user" value="1"> <!-- contoh user id -->
      <input type="text" id="pesan" placeholder="Ketik pesan..." autocomplete="off" required>
      <button type="submit">Kirim</button>
    </form>
  </main>

  <script>
  const chatBox = document.getElementById("chat-box");
  const chatForm = document.getElementById("chat-form");

  const API_URL = "http://localhost/lakoni_aja/api/";

  // Ambil pesan dari server
  async function loadMessages() {
    const idSesi = document.getElementById("id_sesi").value;
    const res = await fetch(`${API_URL}get_messages.php?id_sesi=${idSesi}`);
    const data = await res.json();

    if (data.status === "success") {
      chatBox.innerHTML = "";
      data.data.forEach(msg => {
        const div = document.createElement("div");
        div.classList.add("message");
        div.classList.add(msg.id_konselor ? "from-konselor" : "from-user");
        div.innerHTML = `
          <p>${msg.pesan}</p>
          <small>${msg.waktu_kirim}</small>
        `;
        chatBox.appendChild(div);
      });
      chatBox.scrollTop = chatBox.scrollHeight;
    }
  }

  // Kirim pesan ke server
  chatForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
      id_user: document.getElementById("id_user").value,
      id_konselor: document.getElementById("id_konselor").value,
      id_sesi: document.getElementById("id_sesi").value,
      pesan: document.getElementById("pesan").value
    };

    console.log(payload); // 🔍 Lihat di console browser

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

  // Auto refresh pesan setiap 3 detik
  setInterval(loadMessages, 3000);
  loadMessages();
  </script>
</body>
</html>
