<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];
$nama_konselor = $_SESSION['nama'] ?? "Konselor";

// Query yang lebih sederhana dan aman
$query = "SELECT DISTINCT 
            u.id_user, 
            u.nama,
            c.id_booking,
            MAX(c.waktu_kirim) as last_time
          FROM chat c
          JOIN user u ON c.id_user = u.id_user
          WHERE c.id_konselor = ?
          GROUP BY u.id_user, u.nama, c.id_booking
          ORDER BY last_time DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result_users = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat Konseling - LakoniAja</title>
  <link rel="stylesheet" href="assets/css/chat_modern.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    /* ====================
       STYLE TAMBAHAN
    ==================== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      height: 100vh;
      overflow: hidden;
    }
    
    /* Container utama */
    .chat-app {
      display: flex;
      height: 100vh;
      max-height: 100vh;
      background: white;
    }
    
    /* Sidebar kiri */
    .chat-list {
      width: 360px;
      background: white;
      border-right: 1px solid #e0e0e0;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    
    .list-header {
      padding: 16px;
      background: #f8f9fa;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .profile {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }
    
    .profile img {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
    }
    
    .profile-info h2 {
      font-size: 16px;
      margin: 0;
      color: #333;
    }
    
    .profile-info p {
      font-size: 13px;
      color: #666;
      margin: 4px 0 0 0;
    }
    
    .search input {
      width: 100%;
      padding: 10px 16px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      background: #f0f2f5;
    }
    
    .search input:focus {
      outline: none;
      border-color: #007bff;
    }
    
    /* Daftar chat */
    .users-wrap {
      flex: 1;
      overflow-y: auto;
      padding: 0;
    }
    
    .chat-item {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
      transition: background 0.2s;
      position: relative;
    }
    
    .chat-item:hover {
      background: #f5f5f5;
    }
    
    .chat-item.active {
      background: #e3f2fd;
    }
    
    .chat-avatar {
      position: relative;
      margin-right: 12px;
    }
    
    .chat-avatar i {
      font-size: 40px;
      color: #4c5dd0;
    }
    
    .online-dot {
      position: absolute;
      bottom: 2px;
      right: 2px;
      width: 12px;
      height: 12px;
      background: #4CAF50;
      border: 2px solid white;
      border-radius: 50%;
    }
    
    .chat-info {
      flex: 1;
      min-width: 0;
    }
    
    .chat-info h4 {
      font-size: 16px;
      color: #333;
      margin: 0 0 4px 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    
    .chat-preview {
      font-size: 14px;
      color: #666;
      margin: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    
    .chat-time {
      font-size: 12px;
      color: #999;
      position: absolute;
      top: 12px;
      right: 16px;
    }
    
    .unread-badge {
      position: absolute;
      bottom: 12px;
      right: 16px;
      background: #4c5dd0;
      color: white;
      font-size: 11px;
      font-weight: bold;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* Panel chat kanan */
    .chat-panel {
      flex: 1;
      display: flex;
      flex-direction: column;
      height: 100%;
      position: relative;
    }
    
    .chat-header {
      padding: 12px 20px;
      background: white;
      border-bottom: 1px solid #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .chat-user-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .chat-user-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    
    .chat-user-info h3 {
      font-size: 16px;
      margin: 0;
      color: #333;
    }
    
    .chat-status {
      font-size: 13px;
      color: #666;
      margin-top: 2px;
    }
    
    /* Area pesan */
    .messages-container {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background: #efeae2;
      background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" opacity="0.05"><path d="M0,0 L100,100 M100,0 L0,100"/></svg>');
    }
    
    .message {
      margin-bottom: 16px;
      max-width: 70%;
      clear: both;
    }
    
    .message.sent {
      float: right;
      text-align: right;
    }
    
    .message.received {
      float: left;
    }
    
    .message-bubble {
      padding: 12px 16px;
      border-radius: 18px;
      line-height: 1.4;
      word-wrap: break-word;
      position: relative;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .message.sent .message-bubble {
      background: #4c5dd0;
      color: white;
      border-bottom-right-radius: 4px;
    }
    
    .message.received .message-bubble {
      background: white;
      color: #333;
      border-bottom-left-radius: 4px;
    }
    
    .message-time {
      font-size: 11px;
      color: #999;
      margin-top: 4px;
    }
    
    /* Input area */
    .chat-input-area {
      padding: 16px 20px;
      background: white;
      border-top: 1px solid #e0e0e0;
    }
    
    .chat-input-form {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    
    .chat-input-form input {
      flex: 1;
      padding: 12px 16px;
      border: 1px solid #ddd;
      border-radius: 24px;
      font-size: 14px;
      outline: none;
    }
    
    .chat-input-form input:focus {
      border-color: #4c5dd0;
    }
    
    .send-button {
      background: #4c5dd0;
      color: white;
      border: none;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s;
    }
    
    .send-button:hover {
      background: #3a4bc0;
    }
    
    .send-button:disabled {
      background: #ccc;
      cursor: not-allowed;
    }
    
    /* Empty state */
    .empty-chat {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-align: center;
      padding: 40px;
      color: #666;
    }
    
    .empty-chat i {
      font-size: 64px;
      color: #ddd;
      margin-bottom: 16px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .chat-list {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s;
      }
      
      .chat-list.active {
        transform: translateX(0);
      }
      
      .mobile-menu-button {
        display: block;
        background: none;
        border: none;
        font-size: 20px;
        color: #333;
        cursor: pointer;
        margin-right: 12px;
      }
      
      .back-button {
        display: flex;
        align-items: center;
        gap: 8px;
        background: none;
        border: none;
        font-size: 16px;
        color: #333;
        cursor: pointer;
        padding: 8px 0;
      }
    }
    
    @media (min-width: 769px) {
      .mobile-menu-button,
      .back-button {
        display: none !important;
      }
    }
  </style>
</head>
<body>

<!-- SIDEBAR NAVIGASI -->
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

<!-- CHAT APP CONTAINER -->
<div class="chat-app">
  <!-- SIDEBAR KIRI: DAFTAR KONSELI -->
  <aside class="chat-list" id="chatList">
    <div class="list-header">
      <div class="profile">
        <img src="assets/img/logo.png" alt="logo">
        <div class="profile-info">
          <h2>LAKONI AJA</h2>
          <p>Chat Konseling — <?= htmlspecialchars($nama_konselor) ?></p>
        </div>
      </div>
      <div class="search">
        <input type="text" placeholder="Cari konseli..." id="searchInput">
      </div>
    </div>

    <div class="users-wrap" id="usersWrap">
      <?php if (mysqli_num_rows($result_users) > 0): ?>
        <?php while ($user = mysqli_fetch_assoc($result_users)): 
          // Format waktu
          $lastTime = $user['last_time'];
          $timeDisplay = '';
          if ($lastTime) {
            $timestamp = strtotime($lastTime);
            $now = time();
            $diff = $now - $timestamp;
            
            if ($diff < 60) {
              $timeDisplay = 'Baru saja';
            } elseif ($diff < 3600) {
              $timeDisplay = floor($diff / 60) . 'm';
            } elseif ($diff < 86400) {
              $timeDisplay = floor($diff / 3600) . 'j';
            } else {
              $timeDisplay = floor($diff / 86400) . 'h';
            }
          }
        ?>
        <div class="chat-item" 
             data-user-id="<?= $user['id_user'] ?>"
             data-booking-id="<?= $user['id_booking'] ?>"
             data-user-name="<?= htmlspecialchars($user['nama']) ?>"
             onclick="selectChat(this)">
          <div class="chat-avatar">
            <i class="fas fa-user-circle"></i>
            <span class="online-dot"></span>
          </div>
          <div class="chat-info">
            <h4><?= htmlspecialchars($user['nama']) ?></h4>
            <p class="chat-preview">Klik untuk melihat percakapan</p>
          </div>
          <div class="chat-time"><?= $timeDisplay ?></div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="empty-chat">
          <div>
            <i class="fas fa-comments"></i>
            <h3>Belum ada percakapan</h3>
            <p>Mulai sesi konseling untuk melihat chat di sini</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </aside>

  <!-- PANEL KANAN: AREA CHAT -->
  <main class="chat-panel" id="chatPanel">
    <!-- Tombol menu mobile -->
    <button class="mobile-menu-button" id="mobileMenuButton">
      <i class="fas fa-bars"></i>
    </button>
    
    <!-- Header untuk chat aktif -->
    <div class="chat-header" id="chatHeader" style="display: none;">
      <button class="back-button" id="backButton">
        <i class="fas fa-arrow-left"></i>
        Kembali
      </button>
      <div class="chat-user-info">
        <img src="assets/img/logo.png" alt="user">
        <div>
          <h3 id="activeUserName">Nama Konseli</h3>
          <p class="chat-status" id="activeUserStatus">Online</p>
        </div>
      </div>
      <div class="chat-actions">
        <span class="badge" id="chatBadge">Active</span>
      </div>
    </div>
    
    <!-- Container pesan -->
    <div class="messages-container" id="messagesContainer">
      <div class="empty-chat" id="emptyChatState">
        <div>
          <i class="fas fa-comments"></i>
          <h3>Selamat datang di Chat</h3>
          <p>Pilih konseli dari daftar di sebelah kiri untuk memulai percakapan</p>
        </div>
      </div>
    </div>
    
    <!-- Input area -->
    <div class="chat-input-area" id="chatInputArea" style="display: none;">
      <form class="chat-input-form" id="chatForm">
        <input type="text" 
               placeholder="Ketik pesan..." 
               id="messageInput"
               autocomplete="off">
        <button type="submit" class="send-button" id="sendButton">
          <i class="fas fa-paper-plane"></i>
        </button>
      </form>
    </div>
  </main>
</div>

<script>
// ================================
// VARIABEL GLOBAL
// ================================
const API_URL = 'http://localhost/webLakoniAja/api/';
const KONSELOR_ID = <?= $id_konselor ?>;
let currentChat = null;
let refreshInterval = null;

// ================================
// FUNGSI UTAMA: PILIH CHAT
// ================================
function selectChat(chatElement) {
  const userId = chatElement.dataset.userId;
  const bookingId = chatElement.dataset.bookingId;
  const userName = chatElement.dataset.userName;
  
  currentChat = { userId, bookingId, userName };
  
  // Update UI sidebar
  document.querySelectorAll('.chat-item').forEach(item => {
    item.classList.remove('active');
  });
  chatElement.classList.add('active');
  
  // Di mobile: sembunyikan sidebar
  if (window.innerWidth <= 768) {
    document.getElementById('chatList').classList.remove('active');
  }
  
  // Tampilkan header chat
  document.getElementById('chatHeader').style.display = 'flex';
  document.getElementById('activeUserName').textContent = userName;
  document.getElementById('chatInputArea').style.display = 'block';
  document.getElementById('emptyChatState').style.display = 'none';
  
  // Tampilkan loading
  const messagesContainer = document.getElementById('messagesContainer');
  messagesContainer.innerHTML = `
    <div class="empty-chat">
      <div>
        <i class="fas fa-spinner fa-spin"></i>
        <p>Memuat percakapan...</p>
      </div>
    </div>
  `;
  
  // Load chat
  loadChat(userId, bookingId, userName);
}

// ================================
// LOAD CHAT DARI API
// ================================
async function loadChat(userId, bookingId, userName) {
  try {
    // 1. Ambil pesan dari API
    const messages = await fetchMessages(bookingId);
    
    // 2. Render pesan
    renderMessages(messages);
    
    // 3. Cek status chat
    await checkChatStatus(bookingId);
    
    // 4. Mulai auto-refresh
    startAutoRefresh();
    
    // 5. Scroll ke bawah
    setTimeout(() => {
      const container = document.getElementById('messagesContainer');
      container.scrollTop = container.scrollHeight;
    }, 100);
    
  } catch (error) {
    console.error('Error loading chat:', error);
    showError('Gagal memuat percakapan');
  }
}

// ================================
// AMBIL PESAN DARI API
// ================================
async function fetchMessages(bookingId) {
  const formData = new FormData();
  formData.append('id_booking', bookingId);
  
  const response = await fetch(`${API_URL}get_messages.php`, {
    method: 'POST',
    body: formData
  });
  
  if (!response.ok) {
    throw new Error('Gagal mengambil pesan');
  }
  
  const result = await response.json();
  
  if (result.status !== 'success') {
    throw new Error(result.message || 'Gagal mengambil pesan');
  }
  
  return result.data || [];
}

// ================================
// CEK STATUS CHAT
// ================================
async function checkChatStatus(bookingId) {
  try {
    // Cek apakah user sudah kirim pesan pertama
    const messages = await fetchMessages(bookingId);
    const hasUserMessage = messages.some(msg => msg.pengirim === 'user');
    
    const input = document.getElementById('messageInput');
    const button = document.getElementById('sendButton');
    
    if (hasUserMessage) {
      input.placeholder = 'Ketik pesan...';
      button.disabled = false;
      document.getElementById('activeUserStatus').textContent = 'Online';
      document.getElementById('chatBadge').textContent = 'Active';
      document.getElementById('chatBadge').className = 'badge';
    } else {
      input.placeholder = 'Menunggu pesan pertama dari konseli...';
      button.disabled = true;
      document.getElementById('activeUserStatus').textContent = 'Menunggu...';
    }
    
  } catch (error) {
    console.warn('Gagal cek status chat:', error);
  }
}

// ================================
// RENDER PESAN
// ================================
function renderMessages(messages) {
  const container = document.getElementById('messagesContainer');
  
  if (!messages || messages.length === 0) {
    container.innerHTML = `
      <div class="empty-chat">
        <div>
          <i class="fas fa-comment-slash"></i>
          <h3>Belum ada pesan</h3>
          <p>Mulai percakapan dengan mengirim pesan pertama</p>
        </div>
      </div>
    `;
    return;
  }
  
  // Urutkan pesan dari yang terlama
  messages.sort((a, b) => new Date(a.waktu_kirim) - new Date(b.waktu_kirim));
  
  let html = '';
  let lastDate = null;
  
  messages.forEach(msg => {
    const isKonselor = msg.pengirim === 'konselor';
    const msgDate = new Date(msg.waktu_kirim);
    const timeStr = formatTime(msg.waktu_kirim);
    const dateStr = msgDate.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
    
    // Tampilkan tanggal jika berbeda
    if (dateStr !== lastDate) {
      html += `<div style="text-align: center; margin: 20px 0;">
                <span style="background: rgba(0,0,0,0.1); padding: 4px 12px; border-radius: 12px; font-size: 12px; color: #666;">
                  ${dateStr}
                </span>
              </div>`;
      lastDate = dateStr;
    }
    
    html += `
      <div class="message ${isKonselor ? 'sent' : 'received'}">
        <div class="message-bubble">
          ${escapeHtml(msg.pesan)}
        </div>
        <div class="message-time">${timeStr}</div>
      </div>
    `;
  });
  
  container.innerHTML = html;
}

// ================================
// KIRIM PESAN
// ================================
document.getElementById('chatForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  if (!currentChat) return;
  
  const input = document.getElementById('messageInput');
  const button = document.getElementById('sendButton');
  const message = input.value.trim();
  
  if (!message) return;
  
  // Disable input sementara
  const originalButton = button.innerHTML;
  input.disabled = true;
  button.disabled = true;
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  
  try {
    const formData = new FormData();
    formData.append('id_booking', currentChat.bookingId);
    formData.append('id_konselor', KONSELOR_ID);
    formData.append('pesan', message);
    
    const response = await fetch(`${API_URL}send_message.php`, {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.status === 'success') {
      // Reset input
      input.value = '';
      
      // Refresh chat
      await refreshChat();
    } else {
      throw new Error(result.message || 'Gagal mengirim pesan');
    }
    
  } catch (error) {
    console.error('Send error:', error);
    alert('Gagal mengirim pesan: ' + error.message);
  } finally {
    // Enable kembali
    input.disabled = false;
    button.disabled = false;
    button.innerHTML = originalButton;
    input.focus();
  }
});

// ================================
// AUTO-REFRESH
// ================================
async function refreshChat() {
  if (!currentChat) return;
  
  try {
    const messages = await fetchMessages(currentChat.bookingId);
    renderMessages(messages);
    
    // Scroll ke bawah
    setTimeout(() => {
      const container = document.getElementById('messagesContainer');
      container.scrollTop = container.scrollHeight;
    }, 100);
    
  } catch (error) {
    console.error('Refresh error:', error);
  }
}

function startAutoRefresh() {
  // Hentikan interval lama
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
  
  // Mulai interval baru
  refreshInterval = setInterval(() => {
    if (currentChat) {
      refreshChat();
    }
  }, 3000);
}

// ================================
// FUNGSI BANTU
// ================================
function formatTime(datetime) {
  const date = new Date(datetime);
  return date.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  });
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function showError(message) {
  const container = document.getElementById('messagesContainer');
  container.innerHTML = `
    <div class="empty-chat">
      <div>
        <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
        <h3>Terjadi Kesalahan</h3>
        <p>${escapeHtml(message)}</p>
        <button onclick="location.reload()" 
                style="margin-top: 16px; padding: 8px 16px; background: #4c5dd0; color: white; border: none; border-radius: 6px; cursor: pointer;">
          Coba Lagi
        </button>
      </div>
    </div>
  `;
}

// ================================
// PENCARIAN
// ================================
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const chatItems = document.querySelectorAll('.chat-item');
  
  chatItems.forEach(item => {
    const userName = item.dataset.userName.toLowerCase();
    if (userName.includes(searchTerm)) {
      item.style.display = 'flex';
    } else {
      item.style.display = 'none';
    }
  });
});

// ================================
// RESPONSIVE MOBILE
// ================================
document.getElementById('mobileMenuButton').addEventListener('click', function() {
  document.getElementById('chatList').classList.add('active');
});

document.getElementById('backButton').addEventListener('click', function() {
  document.getElementById('chatList').classList.add('active');
});

// Tutup sidebar saat klik di luar (mobile)
document.addEventListener('click', function(event) {
  const chatList = document.getElementById('chatList');
  const mobileButton = document.getElementById('mobileMenuButton');
  
  if (window.innerWidth <= 768 && 
      chatList.classList.contains('active') &&
      !chatList.contains(event.target) &&
      event.target !== mobileButton &&
      !mobileButton.contains(event.target)) {
    chatList.classList.remove('active');
  }
});

// ================================
// INISIALISASI
// ================================
document.addEventListener('DOMContentLoaded', function() {
  // Cek parameter URL untuk auto-load chat
  const urlParams = new URLSearchParams(window.location.search);
  const userId = urlParams.get('id_user');
  const bookingId = urlParams.get('id_booking');
  
  if (userId && bookingId) {
    const chatItem = document.querySelector(`.chat-item[data-user-id="${userId}"][data-booking-id="${bookingId}"]`);
    if (chatItem) {
      setTimeout(() => selectChat(chatItem), 500);
    }
  }
  
  // Responsive check
  function checkResponsive() {
    if (window.innerWidth <= 768) {
      document.getElementById('mobileMenuButton').style.display = 'block';
    } else {
      document.getElementById('mobileMenuButton').style.display = 'none';
      document.getElementById('chatList').classList.remove('active');
    }
  }
  
  checkResponsive();
  window.addEventListener('resize', checkResponsive);
});

// Cleanup
window.addEventListener('beforeunload', function() {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
});
</script>

</body>
</html>