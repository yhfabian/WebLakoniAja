<!doctype html>
<!-- File: chat_modern.php  (FULL-REPLACE modern UI) -->
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$nama_konselor = $_SESSION['nama'] ?? "Konselor";
$id_booking_aktif = $_GET['id_booking'] ?? '';
$id_user_aktif = $_GET['id_user'] ?? '';

// ===== CEK WAKTU EXPIRED =====
$chat_expired = false;

if (!empty($id_booking_aktif)) {
    $query_expiry = "SELECT j.tanggal, j.jam_selesai                      FROM booking b                      JOIN jadwal j ON b.id_jadwal = j.id_jadwal                      WHERE b.id_booking = ?";
    $stmt_expiry = mysqli_prepare($conn, $query_expiry);
    mysqli_stmt_bind_param($stmt_expiry, "i", $id_booking_aktif);
    mysqli_stmt_execute($stmt_expiry);
    $result_expiry = mysqli_stmt_get_result($stmt_expiry);

    if ($result_expiry->num_rows > 0) {
        $expiry_data = mysqli_fetch_assoc($result_expiry);
        $waktu_selesai = strtotime($expiry_data['tanggal'] . " " . $expiry_data['jam_selesai']);
        $waktu_expired = $waktu_selesai + (10 * 60);
        $chat_expired = (time() > $waktu_expired);
    }
}

$query = "     SELECT DISTINCT         u.id_user,          u.nama,          MAX(c.pesan) AS pesan,          MAX(c.waktu_kirim) AS waktu_kirim,         c.id_booking,         b.tanggal_booking,         j.tanggal as tanggal_sesi,         j.jam_mulai,         j.jam_selesai     FROM chat c     JOIN user u ON c.id_user = u.id_user     JOIN booking b ON c.id_booking = b.id_booking     JOIN jadwal j ON b.id_jadwal = j.id_jadwal     WHERE c.id_konselor = ?     GROUP BY u.id_user, u.nama, c.id_booking, b.tanggal_booking, j.tanggal, j.jam_mulai, j.jam_selesai     ORDER BY waktu_kirim DESC ";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result_users = mysqli_stmt_get_result($stmt);

if (empty($id_user_aktif) || empty($id_booking_aktif)) {
    mysqli_data_seek($result_users, 0);
    $first_user = mysqli_fetch_assoc($result_users);
    if ($first_user) {
        $id_user_aktif = $first_user['id_user'];
        $id_booking_aktif = $first_user['id_booking'];
        mysqli_data_seek($result_users, 0);
    }
}

$user_can_reply = false;
$nama_user_aktif = "Konseli";

if (!empty($id_user_aktif) && !empty($id_booking_aktif)) {
    mysqli_data_seek($result_users, 0);
    while ($u = mysqli_fetch_assoc($result_users)) {
        if ($u['id_user'] == $id_user_aktif && $u['id_booking'] == $id_booking_aktif) {
            $nama_user_aktif = $u['nama'];

            $query_check = "SELECT COUNT(*) as count FROM chat                             WHERE id_booking = ?                             AND id_user = ?                             AND id_konselor IS NULL";
            $stmt_check = mysqli_prepare($conn, $query_check);
            mysqli_stmt_bind_param($stmt_check, "ii", $id_booking_aktif, $id_user_aktif);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            $check_data = mysqli_fetch_assoc($result_check);

            $user_can_reply = ($check_data['count'] > 0);
            break;
        }
    }
    mysqli_data_seek($result_users, 0);
}

$form_disabled = $chat_expired || !$user_can_reply;
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Modern Chat UI — LakoniAja</title>
  <link rel="stylesheet" href="assets/css/chat_modern.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body>

<!-- === SIDEBAR === -->
  <div class="sidebar">

    <div>
        <a href="dashboard.php" class="item active icon top">
            <i class="ri-home-5-line"></i>
        </a>
    </div>

    <div class="menu">
        <a href="jadwalkonselor.php" class="item">
            <i class="ri-calendar-event-line"></i>
        </a>
        <a href="chat.php" class="item">
            <i class="ri-message-3-line"></i>
        </a>
        <a href="testimoni.php" class="item">
            <i class="ri-chat-smile-3-line"></i>
        </a>
        <a href="rekam_medis.php" class="item">
            <i class="ri-file-list-3-line"></i>
        </a>
        <a href="artikel.php" class="item">
            <i class="ri-article-line"></i>
        </a>
    </div>

    <div>
        <a href="logout.php" class="icon bottom">
        <i class="ri-logout-circle-r-line"></i>
        </a>
    </div>

  </div>

  <div class="chat-app">

    <!-- LEFT: CHAT LIST -->
    <aside class="chat-list">
      <div class="list-header">
        <div class="profile">
          <img src="assets/img/logo.png" alt="logo">
          <div>
            <strong>LAKONI AJA</strong>
            <div class="small">Chat Konseling — <span><?= htmlspecialchars($nama_konselor) ?></span></div>
          </div>
        </div>
        <div class="search">
          <input id="search" type="text" placeholder="Cari konseli...">
        </div>
      </div>

      <div id="users-wrap" class="users-wrap">
        <?php if (mysqli_num_rows($result_users) > 0):
            while ($u = mysqli_fetch_assoc($result_users)):
              $selisih_detik = time() - strtotime($u['waktu_kirim']);
              $status_online = ($selisih_detik <= 300) ? "online" : "offline";
              $isActive = ($u['id_user'] == $id_user_aktif && $u['id_booking'] == $id_booking_aktif);
        ?>
        <div class="chat-item <?= $isActive ? 'active' : '' ?>" data-user="<?= $u['id_user'] ?>" data-booking="<?= $u['id_booking'] ?>">
          <div class="avatar">
            <i class="fas fa-user-circle"></i>
            <span class="dot <?= $status_online ?>"></span>
          </div>
          <div class="meta">
            <strong><?= htmlspecialchars($u['nama']) ?></strong>
            <p class="preview"><?= htmlspecialchars(substr($u['pesan'] ?? 'Belum ada pesan', 0, 50)) ?><?= (strlen($u['pesan'] ?? '')>50)?'...':'' ?></p>
          </div>
        </div>
        <?php endwhile; else: ?>
          <p class="no-users">Belum ada percakapan</p>
        <?php endif; ?>
      </div>

    </aside>

    <!-- RIGHT: CHAT WINDOW -->
    <main class="chat-panel">

      <header class="panel-header">
        <div class="panel-left">
          <img id="panel-avatar" src="assets/img/logo.png" alt="avatar">
          <div>
            <h3 id="panel-name"><?= htmlspecialchars($nama_user_aktif) ?></h3>
            <div class="small" id="panel-status"><?php echo $chat_expired ? 'Chat ditutup' : 'Online'; ?></div>
          </div>
        </div>
        <div class="panel-right">
          <div class="badge <?php echo $chat_expired ? 'expired' : ''; ?>">
            <?php if ($chat_expired): ?>
              <i class="fas fa-ban"></i> Closed
            <?php else: ?>
              <i class="fas fa-check"></i> Active
            <?php endif; ?>
          </div>
        </div>
      </header>

      <section id="messages" class="messages">
        <p class="loading">Memuat pesan...</p>
      </section>

      <footer class="input-area <?php echo $form_disabled ? 'disabled' : ''; ?>">
        <form id="chat-form">
          <input type="hidden" id="id_booking" value="<?= htmlspecialchars($id_booking_aktif) ?>">
          <input type="hidden" id="id_konselor" value="<?= htmlspecialchars($id_konselor) ?>">
          <input type="hidden" id="id_user" value="<?= htmlspecialchars($id_user_aktif) ?>">

          <input id="message-input" type="text" placeholder="<?php echo $chat_expired ? 'Chat telah ditutup' : ($user_can_reply ? 'Ketik pesan...' : 'Menunggu konseli...'); ?>" <?= $form_disabled ? 'disabled' : 'required' ?> autocomplete="off">
          <button id="send-btn" class="send-btn" <?= $form_disabled ? 'disabled' : '' ?> title="Kirim">
            <?php if ($chat_expired): ?>
              <i class="fas fa-ban"></i>
            <?php else: ?>
              <i class="fas fa-paper-plane"></i>
            <?php endif; ?>
          </button>
        </form>
      </footer>

    </main>

  </div>

  <script>
    // Config from PHP
    const API_URL = 'http://localhost/webLakoniAja/api/';
    const isUserStartedChat = <?= json_encode($user_can_reply ? true : false) ?>;
    let _isUserStartedChat = isUserStartedChat;
    let isChatExpired = <?= json_encode($chat_expired ? true : false) ?>;

    const usersWrap = document.getElementById('users-wrap');
    const messagesEl = document.getElementById('messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');

    // Attach click listeners to chat items (delegation)
    usersWrap.addEventListener('click', (e) => {
      const item = e.target.closest('.chat-item');
      if (!item) return;
      const idUser = item.dataset.user;
      const idBooking = item.dataset.booking;
      // navigate to same page with query params
      window.location.href = `chat_modern.php?id_user=${idUser}&id_booking=${idBooking}`;
    });

    async function loadMessages() {
      const idBooking = document.getElementById('id_booking')?.value;
      if (!idBooking) return;

      try {
        const formData = new FormData();
        formData.append('id_booking', idBooking);

        const res = await fetch(`${API_URL}get_messages.php`, { method: 'POST', body: formData });
        if (!res.ok) throw new Error(`HTTP error: ${res.status}`);
        const data = await res.json();

        if (data.status === 'success') {
          renderMessages(data.data);
          checkUserHasMessaged(data.data);
        } else {
          messagesEl.innerHTML = `<p class="error">${escapeHtml(data.message)}</p>`;
        }
      } catch (err) {
        console.error(err);
        messagesEl.innerHTML = '<p class="error">Gagal memuat pesan</p>';
      }
    }

    function renderMessages(messages) {
      messagesEl.innerHTML = '';
      if (!messages || messages.length === 0) {
        messagesEl.innerHTML = '<p class="no-messages">Belum ada pesan</p>';
        return;
      }
      messages.sort((a,b)=> new Date(a.waktu_kirim) - new Date(b.waktu_kirim));
      messages.forEach(msg => {
        const div = document.createElement('div');
        div.classList.add('msg');
        div.classList.add(msg.pengirim === 'user' ? 'other' : 'me');
        div.innerHTML = `<div class="bubble">${escapeHtml(msg.pesan)}</div><div class="time">${escapeHtml(msg.waktu_kirim)}</div>`;
        messagesEl.appendChild(div);
      });
      messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function checkUserHasMessaged(messages) {
      if (isChatExpired) return;
      const userMessages = messages.filter(m => m.pengirim === 'user');
      const hasMessage = userMessages.length > 0;
      if (hasMessage !== _isUserStartedChat) {
        _isUserStartedChat = hasMessage;
        updateFormStatus(hasMessage);
      }
    }

    function updateFormStatus(canReply) {
      const footer = document.querySelector('.input-area');
      if (!chatForm || !messageInput || !sendBtn) return;
      if (canReply && !isChatExpired) {
        footer.classList.remove('disabled');
        messageInput.disabled = false;
        messageInput.placeholder = 'Ketik pesan...';
        sendBtn.disabled = false;
      } else {
        footer.classList.add('disabled');
        messageInput.disabled = true;
        sendBtn.disabled = true;
      }
    }

    if (chatForm) {
      chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (isChatExpired) { alert('Chat telah ditutup.'); return; }
        if (!_isUserStartedChat) { alert('Tunggu konseli mengirim pesan pertama.'); return; }

        const payload = {
          id_booking: document.getElementById('id_booking').value,
          id_konselor: document.getElementById('id_konselor').value,
          id_user: document.getElementById('id_user').value,
          pesan: messageInput.value.trim()
        };
        if (!payload.pesan) { alert('Pesan tidak boleh kosong'); return; }

        const original = sendBtn.innerHTML;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
          const formData = new FormData();
          formData.append('id_booking', payload.id_booking);
          formData.append('id_konselor', payload.id_konselor);
          formData.append('id_user', payload.id_user);
          formData.append('pesan', payload.pesan);

          const res = await fetch(`${API_URL}send_message.php`, { method: 'POST', body: formData });
          if (!res.ok) throw new Error(`HTTP error: ${res.status}`);
          const result = await res.json();
          if (result.status === 'success') {
            messageInput.value = '';
            await loadMessages();
          } else throw new Error(result.message || 'Gagal mengirim');
        } catch (err) {
          console.error(err);
          alert('Gagal mengirim pesan');
        } finally {
          sendBtn.disabled = false;
          sendBtn.innerHTML = original;
        }
      });
    }

    function escapeHtml(unsafe){
      if (!unsafe) return '';
      return String(unsafe)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
    }

    // Auto-refresh messages when a chat is selected
    <?php if (!empty($id_user_aktif) && !empty($id_booking_aktif)): ?>
    setInterval(loadMessages, 3000);
    loadMessages();
    <?php endif; ?>

  </script>

  <!-- NOTE: Below is the CSS file content. Save this as: assets/css/chat_modern.css -->
  <!-- START OF CSS FILE -->
  <style id="__css_note" media="none">/* 
    ---- COPY the following block (without the <style> tags) into assets/css/chat_modern.css ----
  */</style>

<script>
// Auto expand textarea seperti WhatsApp
const textarea = document.querySelector('.chat-input textarea');
if (textarea) {
  textarea.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  });
}
</script>
</body>
</html>



