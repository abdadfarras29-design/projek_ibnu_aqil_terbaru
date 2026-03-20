<?php
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Website Sekolah</title>
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
                <li><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn active">Tentang ▾</a>
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

    <!-- Profile Section -->
    <section class="section active" style="margin-top: 100px;">
        <h2 class="section-title">Profil Sekolah</h2>
        <div class="profile-container">
            <div class="profile-grid">
                <div class="profile-item">
                    <h4>Nama Sekolah</h4>
                    <p>SMP IBNU AQIL</p>
                </div>
                <div class="profile-item">
                    <h4>NPSN</h4>
                    <p>20231052</p>
                </div>
                <div class="profile-item">
                    <h4>Tahun Berdiri</h4>
                    <p>1992</p>
                </div>
                <div class="profile-item">
                    <h4>Akreditasi</h4>
                    <p>A (Amat Baik)</p>
                </div>
                <div class="profile-item">
                    <h4>Kepala Sekolah</h4>
                    <p>Ade Irawan</p>
                </div>
                <div class="profile-item">
                    <h4>Status Sekolah</h4>
                    <p>Swasta</p>
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <h3 style="color: var(--dark-green); margin-bottom: 1rem;">Tentang Kami</h3>
                <p style="line-height: 1.8; color: var(--text-gray); text-align: justify;">
                    Sekolah Ibnu Aqil Bogor adalah lembaga pendidikan yang mengintegrasikan pembelajaran akademik dengan
                    nilai-nilai keislaman. Kami berkomitmen membentuk peserta didik yang berakhlak mulia, berilmu, dan
                    berkarakter
                </p>
                <p style="line-height: 1.8; color: var(--text-gray); text-align: justify; margin-top: 1rem;">
                    Dengan dukungan tenaga pendidik profesional serta lingkungan belajar yang kondusif, Sekolah Ibnu
                    Aqil menghadirkan pendidikan yang inspiratif untuk menyiapkan generasi yang siap menghadapi
                    tantangan masa depan
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas & Berkarakter</p>
    </footer>

    <script src="script.js"></script>
</body>