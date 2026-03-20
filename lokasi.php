<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi - Website Sekolah</title>
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
                    <a href="#" class="dropbtn active">Hubungi ▾</a>
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

    <!-- Location Section -->
    <section class="section active" style="margin-top: 100px;">
        <h2 class="section-title">Lokasi Sekolah</h2>
        <div class="profile-container">
            <h3 style="color: var(--dark-green); margin-bottom: 1rem;">Alamat Lengkap</h3>
            <p style="line-height: 1.8; color: var(--text-gray); font-size: 1.1rem;">
                Jl. Raya Laladon No.256, Laladon<br>
                Kec. Ciomas, Kabupaten Bogor<br>
                Provinsi Jawa Barat 16610<br>
                Indonesia
            </p>

            <div class="map-container">
                <!-- koordinat lokasi sekolah -->
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1146.5153848715959!2d106.7543699960822!3d-6.5789587598387245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c53be8234c07%3A0xb3f01c4fad0a0492!2sIBNU%20AQIL%20Education%20Center!5e0!3m2!1sid!2sid!4v1770696079034!5m2!1sid!2sid"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>


    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas & Berkarakter</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>