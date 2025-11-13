<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$nama_konselor = $_SESSION['nama'] ?? "Konselor";
$id_sesi = $_GET['id_sesi'] ?? '';
$id_user_aktif = $_GET['id_user'] ?? '';

$query = "
    SELECT DISTINCT
        u.id_user, 
        u.nama, 
        u.foto, 
        MAX(c.pesan) AS pesan, 
        MAX(c.waktu_kirim) AS waktu_kirim,
        c.id_sesi
    FROM chat c
    JOIN user u ON c.id_user = u.id_user
    WHERE c.id_konselor = ?
    GROUP BY u.id_user, u.nama, u.foto, c.id_sesi
    ORDER BY waktu_kirim DESC
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result_users = mysqli_stmt_get_result($stmt);

if (empty($id_user_aktif) || empty($id_sesi)) {
    $first_user = mysqli_fetch_assoc($result_users);
    if ($first_user) {
        $id_user_aktif = $first_user['id_user'];
        $id_sesi = $first_user['id_sesi'];
        mysqli_data_seek($result_users, 0);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Chat | LakoniAja</title>
  <link rel="stylesheet" href="assets/css/chat.css?v=3.0">
</head>
<body>

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

  <main class="chat-wrapper">

    <aside class="sidebar-users">
      <h3>Daftar Konseli</h3>
      <?php 
      if (mysqli_num_rows($result_users) > 0): 
        while ($u = mysqli_fetch_assoc($result_users)): 
          $foto_user = !empty($u['foto']) ? 'uploads/' . $u['foto'] : 'assets/img/default_user.png';
          $selisih_detik = time() - strtotime($u['waktu_kirim']);
          $status_online = ($selisih_detik <= 300) ? "online" : "offline";
      ?>
      <div class="user-card <?= ($u['id_user'] == $id_user_aktif && $u['id_sesi'] == $id_sesi) ? 'active' : '' ?>" 
           onclick="window.location='chat.php?id_user=<?= $u['id_user'] ?>&id_sesi=<?= $u['id_sesi'] ?>'">

        <div class="user-avatar">
          <img src="<?= htmlspecialchars($foto_user) ?>" alt="<?= htmlspecialchars($u['nama']) ?>">
          <span class="status-dot <?= $status_online ?>"></span>
        </div>

        <div class="user-info">
          <h4><?= htmlspecialchars($u['nama']) ?></h4>
          <p><?= htmlspecialchars(substr($u['pesan'], 0, 35)) ?>...</p>
          <small>Sesi: <?= $u['id_sesi'] ?></small>
        </div>
        <span class="time"><?= date("H:i", strtotime($u['waktu_kirim'])) ?></span>
      </div>
      <?php 
        endwhile; 
      else: 
      ?>
        <p class="no-users">Belum ada percakapan</p>
      <?php endif; ?>
    </aside>

    <section class="chat-container">
      <div class="chat-header">
        <h2>💬 Chat Konseling</h2>
        <p>Login sebagai <b><?= htmlspecialchars($nama_konselor) ?></b></p>
        <?php if (!empty($id_user_aktif) && !empty($id_sesi)): 
          mysqli_data_seek($result_users, 0);
          $nama_user_aktif = "User";
          while ($u = mysqli_fetch_assoc($result_users)) {
              if ($u['id_user'] == $id_user_aktif && $u['id_sesi'] == $id_sesi) {
                  $nama_user_aktif = $u['nama'];
                  break;
              }
          }
        ?>
        <p>Chat dengan: <b><?= htmlspecialchars($nama_user_aktif) ?></b> | Sesi: <b><?= $id_sesi ?></b></p>
        <?php endif; ?>
      </div>

      <div id="chat-box" class="chat-box">
        <p class="loading">Memuat pesan...</p>
      </div>

      <?php if (!empty($id_user_aktif) && !empty($id_sesi)): ?>
      <form id="chat-form" class="chat-form">
        <input type="hidden" id="id_sesi" value="<?= htmlspecialchars($id_sesi) ?>">
        <input type="hidden" id="id_konselor" value="<?= htmlspecialchars($id_konselor) ?>">
        <input type="hidden" id="id_user" value="<?= htmlspecialchars($id_user_aktif) ?>">
        <input type="text" id="pesan" placeholder="Ketik pesan..." autocomplete="off" required>
        <button type="submit">Kirim</button>
      </form>
      <?php else: ?>
      <div class="no-chat-selected">
        <p>Pilih konseli untuk memulai chat</p>
      </div>
      <?php endif; ?>
    </section>

  </main>

  <script>
  const chatBox = document.getElementById("chat-box");
  const chatForm = document.getElementById("chat-form");
  const API_URL = 'http://localhost/webLakoniAja/api/';

  async function loadMessages() {
    const idSesi = document.getElementById("id_sesi").value;
    const idUser = document.getElementById("id_user").value;
    const idKonselor = document.getElementById("id_konselor").value;
    
    try {
        const url = `${API_URL}get_messages.php?id_sesi=${idSesi}&id_user=${idUser}&id_konselor=${idKonselor}`;
        const res = await fetch(url);
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();

        if (data.status === "success") {
            renderMessages(data.data);
        } else {
            chatBox.innerHTML = `<p class="error">${data.message}</p>`;
        }
    } catch (error) {
        chatBox.innerHTML = '<p class="error">Gagal memuat pesan</p>';
    }
  }

  function renderMessages(messages) {
    chatBox.innerHTML = "";
    
    if (messages.length === 0) {
        chatBox.innerHTML = '<p class="no-messages">Belum ada pesan</p>';
    } else {
        messages.forEach(msg => {
            const div = document.createElement("div");
            div.classList.add("message");
            
            if (msg.pengirim === 'konselor') {
                div.classList.add("from-konselor");
            } else {
                div.classList.add("from-user");
            }
            
            div.innerHTML = `
                <p>${escapeHtml(msg.pesan)}</p>
                <small>${formatTime(msg.waktu_kirim)}</small>
            `;
            chatBox.appendChild(div);
        });
    }
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  chatForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const pesanInput = document.getElementById("pesan");
    const payload = {
        id_user: document.getElementById("id_user").value,
        id_konselor: document.getElementById("id_konselor").value,
        id_sesi: document.getElementById("id_sesi").value,
        pesan: pesanInput.value.trim()
    };

    if (!payload.pesan) {
        alert("Pesan tidak boleh kosong");
        return;
    }

    const submitBtn = chatForm.querySelector('button');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Mengirim...';

    try {
        const res = await fetch(`${API_URL}send_message.php`, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)
        });

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        const result = await res.json();

        if (result.status === "success") {
            pesanInput.value = "";
            loadMessages();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        alert("Error mengirim pesan: " + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
  });

  function formatTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
  }

  function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
  }

  <?php if (!empty($id_user_aktif) && !empty($id_sesi)): ?>
  setInterval(loadMessages, 3000);
  loadMessages();
  <?php endif; ?>
  </script>
</body>
</html>