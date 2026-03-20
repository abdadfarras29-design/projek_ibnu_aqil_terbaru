<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Website Sekolah</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
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

    <!-- Contact Section -->
    <section class="section active" style="margin-top: 100px;">
        <h2 class="section-title">Hubungi Kami</h2>
        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-icon">📧</div>
                <h3>Email</h3>
                <p>Kirim email kepada kami</p>
                <a href="mailto:info@sekolahkita.sch.id">info@sekolahkita.sch.id</a>
            </div>
            <div class="contact-card">
                <div class="contact-icon">📱</div>
                <h3>WhatsApp</h3>
                <p>Hubungi via WhatsApp</p>
                <a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890</a>
            </div>
            <div class="contact-card">
                <div class="contact-icon">☎️</div>
                <h3>Telepon</h3>
                <p>Hubungi via telepon</p>
                <a href="tel:+622112345678">021-1234-5678</a>
            </div>
        </div>

        <div class="profile-container" style="margin-top: 3rem;">
            <h3 style="color: var(--dark-green); margin-bottom: 1.5rem;">Jam Operasional</h3>
            <div class="profile-grid">
                <div class="profile-item">
                    <h4>Senin - Jumat</h4>
                    <p>07:00 - 16:00 WIB</p>
                </div>
                <div class="profile-item">
                    <h4>Sabtu</h4>
                    <p>07:00 - 12:00 WIB</p>
                </div>
                <div class="profile-item">
                    <h4>Minggu & Libur</h4>
                    <p>Tutup</p>
                </div>
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

</html>