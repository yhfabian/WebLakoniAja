<?php
session_start();
include 'db.php';

// Cek login konselor
if (!isset($_SESSION['id_konselor'])) {
    header("Location: login.php");
    exit();
}

$id_konselor = $_SESSION['id_konselor'];

// Ambil data konselor dari database
$stmt = mysqli_prepare($conn, "SELECT nama, foto FROM konselor WHERE id_konselor = ?");
mysqli_stmt_bind_param($stmt, "i", $id_konselor);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$konselor = mysqli_fetch_assoc($result);

// Simpan data ke variabel
$nama_konselor = $konselor['nama'] ?? 'Konselor';
$foto = !empty($konselor['foto']) 
    ? 'uploads/' . $konselor['foto'] 
    : 'assets/img/user.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Konselor</title>
  <link rel="stylesheet" href="assets/css/styledashboard.css?v=<?php echo time(); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body>
  <div class="dashboard-container">

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


    <!-- === MAIN CONTENT === -->
    <main class="content">
      <h1 class="title">Halo, <span><?= htmlspecialchars($nama_konselor) ?>!</span></h1>

      <!-- Welcome Section -->
      <section class="main-dashboard">
        <div class="welcome-card">
          <div class="welcome-left">
            <h3>Selamat Datang Kembali 🌼</h3>
            <p>Semoga harimu menyenangkan! Yuk cek jadwal konseling hari ini.</p>
          </div>
        </div>

       <div class="main-dashboard">
        <div class="rm-card" onclick="window.location.href='rekam_medis.php'">
        <img src="assets/img/patient.png" alt="rekamedis">
        <p>Rekam Medis</p>
        </div>
        <div class="card">
        <div class="rm-card" onclick="window.location.href='jadwalkonselor.php'">
          <img src="assets/img/calendar.png" alt="jadwal">
          <p>Jadwal Saya</p>
        </div>

</div>

      </section>
  
      <!-- Calendar -->
      <div class="calendar-card">
        <h3>Kalender Kegiatan</h3>
        <iframe 
          src="https://calendar.google.com/calendar/embed?src=id.indonesian%23holiday%40group.v.calendar.google.com&ctz=Asia%2FJakarta" 
          style="border:0" 
          width="100%" 
          height="300" 
          frameborder="0" 
          scrolling="no">
        </iframe>
      </div>
    </main>

    <!-- === RIGHT PANEL === -->
    <aside class="right-panel">
      <div class="profile-card">
        <img src="<?= $foto ?>" class="profile-img" alt="profile">
        <h3><?= htmlspecialchars($nama_konselor) ?></h3>
        <p class="email">Konselor Polije</p>
       <button class="btn-edit-photo" onclick="openPhotoPopup()">Ubah Foto</button>
      </div>

      <!-- REMINDER CARD / CATATAN -->
      <div class="reminder-card">
    <h4 style="text-align:center; margin-bottom:20px;">CATATAN</h4>

    <div id="noteList"></div>

    <!-- Tombol Tambah Catatan -->
    <button id="addNoteBtn" class="note-add-btn">+</button>
    </div>

    </aside>
    <!-- POPUP TAMBAH CATATAN -->
    <div id="notePopup" class="note-popup">
    <div class="note-popup-box">
    <h3 class="popup-title">Tambah Catatan</h3>

    <textarea id="noteInput" rows="4" class="popup-textarea" placeholder="Tulis catatan..."></textarea>

    <div class="popup-buttons">
      <button id="saveNoteBtn" class="popup-btn save">Simpan</button>
      <button onclick="closeNotePopup()" class="popup-btn cancel">Batal</button>
    </div>
    </div>
</div>
<!-- POPUP KONFIRMASI HAPUS -->
<div id="confirmPopup" class="confirm-overlay">
  <div class="confirm-box">
    <p>Yakin catatan dihapus?</p>
    <button class="yes">Ya</button>
    <button class="no">Tidak</button>
  </div>
</div>


  </div>
  <!-- POPUP UBAH FOTO -->
<div class="popup" id="photoPopup">
    <div class="popup-content">
        <h3>Ubah Foto Profil</h3>

        <form action="edit_profil.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="foto" required accept="image/*" class="input">

            <div class="btn-group">
                <button type="submit" class="btn-save">Upload</button>
                <button type="button" class="btn-cancel" onclick="closePhotoPopup()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPhotoPopup(){
    document.getElementById("photoPopup").style.display = "flex";
}
function closePhotoPopup(){
    document.getElementById("photoPopup").style.display = "none";
}
</script>

<script>
  const addNoteBtn = document.getElementById("addNoteBtn");
  const notePopup = document.getElementById("notePopup");
  const noteInput = document.getElementById("noteInput");
  const saveNoteBtn = document.getElementById("saveNoteBtn");
  const noteList = document.getElementById("noteList");
  const confirmPopup = document.getElementById("confirmPopup");

  let notes = JSON.parse(localStorage.getItem("notes")) || [];
  let noteToDelete = null;

  function renderNotes() {
    noteList.innerHTML = "";
    notes.forEach((note, index) => {
      const div = document.createElement("div");
      div.className = "note-item";
      div.innerHTML = `<span>${note}</span>`;

      const delBtn = document.createElement("button");
      delBtn.textContent = "✕";
      delBtn.onclick = () => {
        noteToDelete = index;
        confirmPopup.style.display = "flex";
      };

      div.appendChild(delBtn);
      noteList.appendChild(div);
    });
  }

  addNoteBtn.onclick = () => {
    notePopup.style.display = "flex";
  };

  function closeNotePopup() {
    notePopup.style.display = "none";
    noteInput.value = "";
  }

  saveNoteBtn.onclick = () => {
    const text = noteInput.value.trim();
    if (text) {
      notes.push(text);
      localStorage.setItem("notes", JSON.stringify(notes));
      renderNotes();
      closeNotePopup();
    } else {
      alert("Isi catatan terlebih dahulu!");
    }
  };

  // KONFIRMASI HAPUS
  confirmPopup.querySelector(".yes").onclick = () => {
    if (noteToDelete !== null) {
      notes.splice(noteToDelete, 1);
      localStorage.setItem("notes", JSON.stringify(notes));
      renderNotes();
      noteToDelete = null;
    }
    confirmPopup.style.display = "none";
  };

  confirmPopup.querySelector(".no").onclick = () => {
    noteToDelete = null;
    confirmPopup.style.display = "none";
  };

  renderNotes();
</script>

</body>
</html>
