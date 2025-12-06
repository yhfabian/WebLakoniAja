-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 06 Des 2025 pada 14.32
-- Versi server: 8.0.30
-- Versi PHP: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lakoni_aja`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `email`, `foto`, `created_at`) VALUES
(1, 'admin01', '$2y$10$o7ltmeiTLRhoEneVmXgzW.RBooSwUJp8BfFWJcBmRXZ8VW.DTOnBu', 'admingaul', 'admin03@gmail.com', NULL, '2025-10-29 08:02:45'),
(3, 'admin', '$2y$10$kxdP7/NL8i2PYy5V1z5Uhe17dBGLsJDRcDrSjkRk3WcXqBuxcbElS', 'Admin Utama', NULL, NULL, '2025-10-29 08:14:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `artikel`
--

CREATE TABLE `artikel` (
  `id_artikel` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `link_sumber` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` datetime DEFAULT CURRENT_TIMESTAMP,
  `tanggal_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_konselor` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `artikel`
--

INSERT INTO `artikel` (`id_artikel`, `judul`, `isi`, `gambar`, `link_sumber`, `tanggal_dibuat`, `tanggal_update`, `id_konselor`) VALUES
(4, 'SKIZOFRENIA', 'Skizofrenia adalah gangguan mental jangka panjang yang menyebabkan penderita mengalami distorsi realitas seperti halusinasi dan delusi, kekacauan berpikir, serta perubahan perilaku dan emosi. Gangguan ini dapat sangat memengaruhi kemampuan seseorang untuk berpikir logis, berinteraksi sosial, dan menjalani kehidupan sehari-hari. Gejalanya dapat muncul dari masa remaja hingga usia 30 tahun dan membutuhkan penanganan medis serta dukungan dari lingkungan sekitar.  ', '1764739948_6104.jpg', 'https://id.wikipedia.org/wiki/Skizofrenia', '2025-12-03 12:32:28', '2025-12-03 12:32:28', 1),
(5, 'GANGGUAN MENTAL', 'Gangguan mental , juga disebut sebagai penyakit mental , [ 6 ] kondisi kesehatan mental , [ 7 ] atau disabilitas psikiatrik , [ 2 ] adalah pola perilaku atau mental yang menyebabkan tekanan signifikan atau gangguan fungsi pribadi. [ 8 ] Gangguan mental juga dicirikan oleh gangguan yang signifikan secara klinis dalam kognisi, pengaturan emosi, atau perilaku seseorang, sering kali dalam konteks sosial . [ 9 ] [ 10 ] Gangguan tersebut dapat terjadi sebagai episode tunggal, dapat berlangsung lama, atau dapat kambuh-remisi . Ada banyak jenis gangguan mental, dengan tanda dan gejala yang sangat bervariasi antara gangguan tertentu. [ 10 ] [ 11 ] Gangguan mental adalah salah satu aspek kesehatan mental .\r\n\r\nPenyebab gangguan mental seringkali tidak jelas. Teori-teori menggabungkan temuan dari berbagai bidang. Gangguan dapat dikaitkan dengan area atau fungsi otak tertentu. Gangguan biasanya didiagnosis atau dinilai oleh tenaga kesehatan mental profesional , seperti psikolog klinis , psikiater , perawat psikiatri, atau pekerja sosial klinis , menggunakan berbagai metode seperti tes psikometri , tetapi seringkali mengandalkan observasi dan pertanyaan. Keyakinan budaya dan agama, serta norma-norma sosial , harus dipertimbangkan saat membuat diagnosis. [ 12 ]\r\n\r\nLayanan untuk gangguan jiwa biasanya tersedia di rumah sakit jiwa , klinik rawat jalan , atau di masyarakat . Perawatan diberikan oleh tenaga kesehatan jiwa profesional. Pilihan perawatan yang umum adalah psikoterapi atau pengobatan psikiatrik , sementara perubahan gaya hidup, intervensi sosial, dukungan sebaya , dan swadaya juga merupakan pilihan. Dalam sebagian kecil kasus, mungkin terdapat penahanan atau perawatan paksa ', '1764740752_3450.jpg', 'https://en.wikipedia.org/wiki/Mental_disorder', '2025-12-03 12:45:52', '2025-12-03 12:45:52', 1),
(6, 'GANGGUAN KECEMASAN', 'Ringkasan\r\nSetiap orang terkadang dapat merasa cemas, tetapi orang dengan gangguan kecemasan seringkali mengalami rasa takut dan khawatir yang intens dan berlebihan. Perasaan ini biasanya disertai dengan ketegangan fisik serta gejala perilaku dan kognitif lainnya. Perasaan ini sulit dikendalikan, menyebabkan tekanan yang signifikan, dan dapat berlangsung lama jika tidak ditangani. Gangguan kecemasan mengganggu aktivitas sehari-hari dan dapat mengganggu kehidupan keluarga, sosial, sekolah, atau pekerjaan seseorang.\r\n\r\nDiperkirakan 4,4% populasi global saat ini mengalami gangguan kecemasan (1) . Pada tahun 2021, 359 juta orang di dunia mengalami gangguan kecemasan, menjadikan gangguan kecemasan sebagai gangguan mental yang paling umum (1) .\r\n\r\nMeskipun terdapat pengobatan yang sangat efektif untuk gangguan kecemasan, hanya sekitar 1 dari 4 orang yang membutuhkan (27,6%) yang menerima pengobatan (2) . Hambatan dalam mendapatkan perawatan meliputi kurangnya kesadaran bahwa gangguan kecemasan dapat diobati, kurangnya investasi dalam layanan kesehatan mental, kurangnya tenaga kesehatan terlatih, dan stigma sosial.\r\n\r\nGejala dan pola\r\nOrang dengan gangguan kecemasan mungkin mengalami ketakutan atau kekhawatiran yang berlebihan terhadap situasi tertentu (misalnya, serangan panik atau situasi sosial) atau, dalam kasus gangguan kecemasan umum, terhadap berbagai situasi sehari-hari. Mereka biasanya mengalami gejala-gejala ini dalam jangka waktu yang panjang – setidaknya beberapa bulan. Biasanya mereka menghindari situasi yang membuat mereka cemas.\r\n\r\nGejala gangguan kecemasan lainnya mungkin termasuk:\r\n\r\nkesulitan berkonsentrasi atau membuat keputusan\r\nmerasa mudah tersinggung, tegang atau gelisah\r\nmengalami mual atau gangguan perut\r\nmengalami palpitasi jantung\r\nberkeringat, gemetar atau gemetar\r\nkesulitan tidur\r\nmemiliki perasaan akan datangnya bahaya, kepanikan, atau malapetaka.\r\nGangguan kecemasan meningkatkan risiko depresi dan gangguan penggunaan zat serta risiko pikiran dan perilaku bunuh diri.\r\n\r\nAda beberapa jenis gangguan kecemasan, termasuk:\r\n\r\ngangguan kecemasan umum (kekhawatiran yang terus-menerus dan berlebihan tentang aktivitas atau kejadian sehari-hari);\r\ngangguan panik (serangan panik dan ketakutan akan serangan panik berkelanjutan);\r\ngangguan kecemasan sosial (rasa takut dan khawatir yang tinggi terhadap situasi sosial yang mungkin membuat seseorang merasa terhina, malu, atau ditolak);\r\nagoraphobia (rasa takut, khawatir, dan penghindaran berlebihan terhadap situasi yang dapat menyebabkan seseorang panik atau merasa terjebak, tidak berdaya, atau malu);\r\ngangguan kecemasan akan perpisahan (rasa takut atau khawatir berlebihan karena akan berpisah dengan orang-orang yang memiliki ikatan emosional yang dalam dengan seseorang);\r\nfobia spesifik (ketakutan yang intens dan tidak rasional terhadap objek atau situasi tertentu yang menyebabkan perilaku penghindaran dan tekanan yang signifikan); dan\r\nmutisme selektif (ketidakmampuan berbicara secara konsisten dalam situasi sosial tertentu, meskipun memiliki kemampuan berbicara dengan nyaman di lingkungan lain, terutama memengaruhi anak-anak).\r\nSeseorang dapat mengalami lebih dari satu gangguan kecemasan secara bersamaan. Gejalanya sering kali dimulai sejak masa kanak-kanak atau remaja dan berlanjut hingga dewasa. Anak perempuan dan wanita dewasa lebih mungkin mengalami gangguan kecemasan dibandingkan anak laki-laki dan pria dewasa.', '1764743218_8963.jpg', 'https://www.who.int/news-room/fact-sheets/detail/anxiety-disorders', '2025-12-03 13:26:58', '2025-12-03 13:26:58', 1),
(7, 'Gejala Kesehatan Mental', 'Kesehatan jiwa atau sebutan lainnya kesehatan mental adalah kesehatan yang berkaitan dengan kondisi emosi, kejiwaan, dan psikis seseorang.\r\n\r\nPerlu kamu ketahui bahwa peristiwa dalam hidup yang berdampak besar pada kepribadian dan perilaku seseorang bisa berpengaruh pada kesehatan mentalnya.\r\n\r\nMisalnya, pelecehan saat usia dini, stres berat dalam jangka waktu lama tanpa adanya penanganan, dan mengalami kekerasan dalam rumah tangga.\r\n\r\nBerbagai kondisi tersebut bisa membuat kondisi kejiwaan seseorang terganggu, sehingga muncul gejala gangguan kesehatan jiwa. \r\n\r\nAkan tetapi, masalah kesehatan mental bisa mengubah cara seseorang dalam mengatasi stres, berhubungan dengan orang lain, membuat pilihan, dan memicu hasrat untuk menyakiti diri sendiri.\r\n\r\nBeberapa jenis gangguan mental yang umum terjadi antara lain depresi, gangguan bipolar, kecemasan, gangguan stres pasca trauma (PTSD), gangguan obsesif kompulsif (OCD), dan psikosis.\r\n\r\nSelain itu, ada beberapa penyakit mental hanya terjadi pada jenis pengidap tertentu, seperti postpartum depression hanya menyerang ibu setelah melahirkan.', '1764980715_4628.png', 'https://www.halodoc.com/kesehatan/kesehatan-mental?srsltid=AfmBOopLl-E1v43Tlv-gicZEOvD-MkDJJ0WbOPWUGvG1tdgPrZXxz56A', '2025-12-06 07:25:15', '2025-12-06 07:25:15', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking`
--

CREATE TABLE `booking` (
  `id_booking` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_jadwal` int DEFAULT NULL,
  `jenis_konseling` enum('Offline','Online') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_booking` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `booking`
--

INSERT INTO `booking` (`id_booking`, `id_user`, `id_jadwal`, `jenis_konseling`, `tanggal_booking`) VALUES
(3, 12, 22, 'Online', '2025-12-04 17:44:44'),
(4, 12, 23, 'Online', '2025-12-04 17:44:58'),
(5, 15, 28, 'Online', '2025-12-06 04:30:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat`
--

CREATE TABLE `chat` (
  `id_chat` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_konselor` int DEFAULT NULL,
  `pesan` text NOT NULL,
  `waktu_kirim` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_booking` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `chat`
--

INSERT INTO `chat` (`id_chat`, `id_user`, `id_konselor`, `pesan`, `waktu_kirim`, `id_booking`) VALUES
(89, 12, 1, 'Hai Pak, saya Raffin. Bagaimana kabar Bapak hari ini? Saya ingin membicarakan beberapa hal terkait masalah yang saya alami adalah sudah mengatur waktu karena banyak tugas', '2025-12-04 18:03:31', 4),
(90, NULL, 1, 'Hai Raffin, saya baik, terima kasih. Tentu, silakan ceritakan lebih lanjut tentang masalah mengatur waktu dan tugas yang sedang kamu hadapi', '2025-12-04 18:04:39', 4),
(91, 12, NULL, 'Baik Pak, mungkin saya susah mengatur waktu karena sangking banyaknya tugas yang diberikan', '2025-12-05 02:01:26', 4),
(92, NULL, 1, 'Lebih baik anda istirahat terlebih dahulu', '2025-12-05 03:38:49', 4),
(93, 12, NULL, 'Hai', '2025-12-05 03:49:36', 4),
(94, NULL, 1, 'baik dengan saya agus sebagai  konselor', '2025-12-05 03:50:01', 4),
(95, NULL, 1, 'P', '2025-12-06 04:00:37', 4),
(99, NULL, 1, 'P', '2025-12-06 05:02:11', 4),
(106, 15, 1, 'P', '2025-12-06 05:50:52', 5),
(107, NULL, 1, 'P', '2025-12-06 14:22:03', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int NOT NULL,
  `id_konselor` int DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status` enum('Tersedia','Dipesan','Selesai') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_konselor`, `tanggal`, `jam_mulai`, `jam_selesai`, `status`) VALUES
(22, 1, '2025-12-09', '10:00:00', '12:00:00', 'Tersedia'),
(23, 1, '2025-12-05', '10:30:00', '12:30:00', 'Dipesan'),
(24, 1, '2025-12-08', '12:00:00', '14:00:00', 'Tersedia'),
(27, 1, '2025-12-05', '07:00:00', '09:00:00', 'Tersedia'),
(28, 1, '2025-12-06', '11:30:00', '13:30:00', 'Tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konselor`
--

CREATE TABLE `konselor` (
  `id_konselor` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `bidang_keahlian` varchar(100) DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `konselor`
--

INSERT INTO `konselor` (`id_konselor`, `nama`, `username`, `password`, `nip`, `bidang_keahlian`, `kontak`, `foto`) VALUES
(1, 'Agus Susanto', 'agus', '$2y$10$pFLi1L.UZNlaw6omXPDmwO4xmPRDy1kVHlmiCzOCS6Qjn91GOPrgu', '12341234', 'tekanan tugas', 'agus78@gmail.com', 'konselor_1_1764223849.jpg'),
(25, 'Andi Prasetyo', 'andi', '$2y$10$qU6eqNHzOlZq.pnvfNNqLOxVFSm1LPZTdri8tT6qvEnK/K.OlCVDi', NULL, 'broken home', 'andi19@gmail.com', NULL),
(26, 'Budi Antoko', 'Budi', '$2y$10$xy4dUCYPZ33KjazsWUlSf.15Cfs2qFfeY5mSoIUoKA4papjATsN/G', NULL, 'Pesikis', '082132625335', NULL),
(28, 'Javar', 'Jvr', '$2y$10$yVz7n0YZ2kiQsf5s.qF3QOPTZ8XyetQvEcEB5bPQAlc71BgZH9pn.', NULL, 'Tangi', 'Jvr@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitoring`
--

CREATE TABLE `monitoring` (
  `id_monitoring` int NOT NULL,
  `id_user` int NOT NULL,
  `id_konselor` int NOT NULL,
  `tanggal` date NOT NULL,
  `catatan` text,
  `diagnosis` varchar(255) DEFAULT NULL,
  `rekomendasi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `monitoring`
--

INSERT INTO `monitoring` (`id_monitoring`, `id_user`, `id_konselor`, `tanggal`, `catatan`, `diagnosis`, `rekomendasi`, `created_at`, `updated_at`) VALUES
(13, 1, 1, '2025-11-23', 'pusing', 'kurang waras', 'ngopi', '2025-11-21 03:05:13', '2025-11-28 07:31:23'),
(14, 6, 1, '2025-12-02', 'stres dijekar dl', 'stres akademik', 'sholat', '2025-12-02 04:41:07', '2025-12-02 04:41:07'),
(15, 5, 1, '2025-12-03', 'awww', 'ihh', 'aduh', '2025-12-02 18:07:20', '2025-12-02 18:07:20'),
(16, 11, 1, '2025-12-07', 'pusing frontend', 'stres akademik', 'lebih sering belajar frontend', '2025-12-03 14:18:56', '2025-12-03 14:18:56'),
(17, 12, 1, '2025-12-05', 'Baik-baik saja', 'Kurang istirahat', 'Turu', '2025-12-04 18:08:57', '2025-12-04 18:08:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reschedule`
--

CREATE TABLE `reschedule` (
  `id_reschedule` int NOT NULL,
  `id_booking` int NOT NULL,
  `tanggal_booking` timestamp NOT NULL,
  `jenis_konseling` varchar(15) NOT NULL,
  `tanggal_booking_baru` timestamp NOT NULL,
  `jenis_konseling_baru` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimoni`
--

CREATE TABLE `testimoni` (
  `id_testimoni` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_konselor` int DEFAULT NULL,
  `komentar` text,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `testimoni`
--

INSERT INTO `testimoni` (`id_testimoni`, `id_user`, `id_konselor`, `komentar`, `tanggal`) VALUES
(1, 1, 1, 'waw saya tambah ceria setelah konselor', '2025-12-01 11:24:46'),
(3, 11, 1, 'pusing saya berkurang', '2025-12-04 02:15:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `no_hp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `nim`, `email`, `tanggal_lahir`, `no_hp`, `username`, `password`) VALUES
(1, 'Yohanes Fabian', 'E41241221', 'e41241221@student.polije.ac.id', NULL, '082132625335', 'fab900', 'Fabian1221'),
(5, 'Zio Vallentino', 'E41244321', 'e41244321@student.polije.ac.id', NULL, '081234567891', 'Zio', '$2y$10$.ug9jDp3xlJh6PapmlDGyORIjjoP5aHD6A9BcGwQ/YiZYRXvdlF8u'),
(6, 'Raffin Tiano', 'E41240853', 'e41240853@student.polije.ac.id', NULL, '082345678901', 'raffin', '$2y$10$1XHX8GHdvtOdhzVaPfw.Zu8fCdfQJgLz1tUNilrVlxF6eW.MhzK9S'),
(8, 'Javier David Levi Butarbutar', 'E41241055', 'e41241055@student.polije.ac.id', NULL, '08213454421', 'javi', '$2y$10$9tALPh3AYwyDzEe/9zdIhumkDtObNbHOy3gqWX0Hnq9HQHpUg19kK'),
(11, 'Zafar Muhammad', 'E41241092', 'e41241092@student.polije.ac.id', NULL, '', 'Zafar', '$2y$10$devBRTIVgSuisMR4B2D3AOfIiBI7Qh1Xi2QaUkzJETwPEFvKhMvBW'),
(12, 'Raffin Tiano', 'E43214321', 'E43214321@student.polije.ac.id', '2025-12-04', '085212344321', 'Fin21', '$2y$10$HxEXTPMiUYU6RXIVJos9DuNb5R4.D2ShfbouRWzuZQG6WCyKnFRcC'),
(15, 'Pradana Zaka', 'W12341234', 'W12341234@student.polije.ac.id', '2005-12-31', '088008800880', 'Zakar', '$2y$10$/9rsm8oftToqnq9rrG5gAOxfGHyD/zxoX6F44T/mGug7falkA.7he');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id_artikel`),
  ADD KEY `id_konselor` (`id_konselor`);

--
-- Indeks untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indeks untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_chat`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_konselor` (`id_konselor`),
  ADD KEY `fk_chat_booking` (`id_booking`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_konselor` (`id_konselor`);

--
-- Indeks untuk tabel `konselor`
--
ALTER TABLE `konselor`
  ADD PRIMARY KEY (`id_konselor`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  ADD PRIMARY KEY (`id_monitoring`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_konselor` (`id_konselor`);

--
-- Indeks untuk tabel `reschedule`
--
ALTER TABLE `reschedule`
  ADD PRIMARY KEY (`id_reschedule`),
  ADD KEY `id_booking` (`id_booking`);

--
-- Indeks untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  ADD PRIMARY KEY (`id_testimoni`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_konselor` (`id_konselor`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id_artikel` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `chat`
--
ALTER TABLE `chat`
  MODIFY `id_chat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `konselor`
--
ALTER TABLE `konselor`
  MODIFY `id_konselor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  MODIFY `id_monitoring` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  MODIFY `id_testimoni` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`id_konselor`) REFERENCES `konselor` (`id_konselor`);

--
-- Ketidakleluasaan untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id_jadwal`);

--
-- Ketidakleluasaan untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`id_konselor`) REFERENCES `konselor` (`id_konselor`),
  ADD CONSTRAINT `fk_chat_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`);

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_konselor`) REFERENCES `konselor` (`id_konselor`);

--
-- Ketidakleluasaan untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  ADD CONSTRAINT `monitoring_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `monitoring_ibfk_2` FOREIGN KEY (`id_konselor`) REFERENCES `konselor` (`id_konselor`);

--
-- Ketidakleluasaan untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  ADD CONSTRAINT `testimoni_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `testimoni_ibfk_2` FOREIGN KEY (`id_konselor`) REFERENCES `konselor` (`id_konselor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
