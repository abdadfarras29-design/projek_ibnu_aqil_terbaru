<?php
require_once 'koneksi.php';
$res_statistik_query = mysqli_query($KONEKSI, "SELECT * FROM `jumlah siswa dll` ORDER BY id DESC LIMIT 1");
$statistik_sekolah = $res_statistik_query ? mysqli_fetch_assoc($res_statistik_query) : null;
$siswa_stat = $statistik_sekolah['siswa'] ?? '1250';
$guru_stat = $statistik_sekolah['guru'] ?? '75';
$prestasi_stat = $statistik_sekolah['prestasi'] ?? '150';
$rombel_stat = $statistik_sekolah['rombongan belajar'] ?? '30';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Website Sekolah</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="Screenshot_2026-02-22-13-16-05-58_1c337646f29875672b5a61192b9010f9.png"
                    alt="Logo SMP IBNU AQIL" class="logo-img">
                SMP IBNU AQIL
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" class="active">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Tentang ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="profile.php">Profil Sekolah</a></li>
                        <li><a href="visi-misi.php">Visi & Misi</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Kegiatan ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="berita.php">Berita</a></li>
                        <li><a href="fasilitas.php">Fasilitas</a></li>
                        <li><a href="ekskul.php">Ekstrakulikuler</a></li>
                        <li><a href="galeri.php">Galeri</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Hubungi ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="lokasi.php">Lokasi</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                        <li><a href="pesan.php">Kirim Pesan</a></li>
                    </ul>
                </li>
                <li><a href="login.php" class="nav-login-btn">Login</a></li>
            </ul>
            <div class="hamburger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-image-overlay"></div>
        <div class="hero-content">
            <h1>Selamat Datang Di SMP IBNU AQIL</h1>
            <p>Membentuk Generasi Cerdas, Berkarakter, dan Berprestasi untuk Masa Depan Gemilang</p>
            <a href="profile.php"><button class="cta-button">Kenali Kami Lebih Dekat</button></a>
        </div>
    </section>

    <!-- Home Section -->
    <section class="section active">
        <h2 class="section-title">Dashboard Sekolah</h2>

        <div class="stats-container">
            <div class="stat-box">
                <span class="stat-number" id="studentCount">0</span>
                <span class="stat-label">Siswa Aktif</span>
            </div>
            <div class="stat-box">
                <span class="stat-number" id="teacherCount">0</span>
                <span class="stat-label">Guru Berkualitas</span>
            </div>
            <div class="stat-box">
                <span class="stat-number" id="achievementCount">0</span>
                <span class="stat-label">Prestasi</span>
            </div>
            <div class="stat-box">
                <span class="stat-number" id="yearCount">0</span>
                <span class="stat-label">Rombongan Belajar</span>
            </div>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">📚</div>
                <h3>Kurikulum Unggulan</h3>
                <p>Menerapkan kurikulum nasional yang disesuaikan dengan kebutuhan zaman, mengintegrasikan teknologi dan
                    nilai-nilai karakter dalam pembelajaran.</p>
            </div>

    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas & Berkarakter</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dynamicSchoolData = {
                students: parseInt('<?php echo $siswa_stat; ?>') || 0,
                teachers: parseInt('<?php echo $guru_stat; ?>') || 0,
                achievements: parseInt('<?php echo $prestasi_stat; ?>') || 0,
                yearEstablished: parseInt('<?php echo $rombel_stat; ?>') || 0
            };
            if (typeof schoolData !== 'undefined') {
                Object.assign(schoolData, dynamicSchoolData);
            }
        });
    </script>
    <script src="script.js"></script>
</body>

</html>