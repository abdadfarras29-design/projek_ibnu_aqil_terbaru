<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fasilitas - Website Sekolah</title>
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
                    <a href="#" class="dropbtn active">Kegiatan ▾</a>
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

    <style>
        .premium-hero { background: linear-gradient(135deg, var(--dark-green), var(--primary-green)); padding: 8rem 2rem 5rem; text-align: center; color: white; border-radius: 0 0 50px 50px; margin-bottom: 4rem; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2); position: relative; overflow: hidden; }
        .premium-hero::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%); pointer-events: none; }
        .premium-hero h2 { font-size: 3rem; margin-bottom: 1.5rem; font-weight: 800; letter-spacing: -1px; position: relative; z-index: 1; }
        .premium-hero p { font-size: 1.25rem; opacity: 0.95; max-width: 700px; margin: 0 auto; line-height: 1.6; position: relative; z-index: 1; font-weight: 300;}
        
        .premium-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2.5rem; padding: 0 2rem; max-width: 1250px; margin: 0 auto 6rem; }
        .premium-card { background: #fff; border-radius: 28px; padding: 2.5rem; box-shadow: 0 15px 35px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.03); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; z-index: 1; display: flex; flex-direction: column;}
        .premium-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(16,185,129,0.03) 0%, transparent 100%); opacity: 0; transition: opacity 0.4s ease; z-index: -1; }
        .premium-card:hover { transform: translateY(-12px); box-shadow: 0 25px 50px rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); }
        .premium-card:hover::before { opacity: 1; }
        

        
        .premium-card h3 { font-size: 1.5rem; color: #1f2937; margin-bottom: 1rem; font-weight: 700; letter-spacing: -0.5px; }
        .premium-card p { color: #6b7280; line-height: 1.8; font-size: 1.05rem; margin: 0; flex-grow: 1; }
    </style>

    <!-- Facilities Section -->
    <section>
        <div class="premium-hero">
            <h2>Fasilitas Terbaik Kami</h2>
            <p>Berkomitmen menyediakan sarana dan prasarana modern berkualitas tinggi untuk menunjang tumbuh kembang akademis dan karakter seluruh siswa.</p>
        </div>

        <div class="premium-grid">
            <div class="premium-card">
                <h3>Ruang Kelas Modern</h3>
                <p>36 ruang kelas yang dilengkapi dengan AC, proyektor mutakhir, dan papan tulis interaktif untuk mendongkrak efektivitas KBM secara utuh.</p>
            </div>
            
            <div class="premium-card">
                <h3>Laboratorium Sains & IT</h3>
                <p>Laboratorium Fisika, Kimia, Biologi, serta Komputer bersertifikat dengan instrumen canggih guna mendukung eksplorasi tak terbatas.</p>
            </div>
            
            <div class="premium-card">
                <h3>Perpustakaan Digital</h3>
                <p>Perpustakaan elegan dengan lebih dari 10.000 tajuk ilmu ditambah ketersediaan pustaka e-library dan ruang baca senyap yang nyaman.</p>
            </div>
            
            <div class="premium-card">
                <h3>Area Olahraga Center</h3>
                <p>Kawasan terpadu lapangan bola basket standar, voli, futsal, serta lintasan atletik karet demi fisik siswa yang sehat dan kuat.</p>
            </div>
            
            <div class="premium-card">
                <h3>Aula Grand Serbaguna</h3>
                <p>Gedung aula raksasa berkapasitas daya tampung 500 figur, cocok untuk helatan akbar, pagelaran seni, maupun yudisium kelulusan.</p>
            </div>
            
            <div class="premium-card">
                <h3>Cafetaria & Kantin Sehat</h3>
                <p>Sentra kuliner siswa bersertifikat higienis yang diawasi para ahli gizi, menyajikan aneka gizi seimbang harian untuk putra-putri.</p>
            </div>
            
            <div class="premium-card">
                <h3>Masjid Ibnu Aqil</h3>
                <p>Ruang utama ibadah yang arsitekturnya sejuk nan syahdu, memfasilitasi kebutuhan kajian Islam, jamaah rutin, dan penguatan rohani.</p>
            </div>
            
            <div class="premium-card">
                <h3>Armada Transportasi</h3>
                <p>Kendaraan rute antar jemput siswa berstandar <i>safety-first</i> yang sigap tiba ke pelosok rute demi memastikan kemudahan mobilitas.</p>
            </div>
            
            <div class="premium-card">
                <h3>Jaringan Fiber Optik & WiFi</h3>
                <p>Titik sambung nirkabel di sekujur kompleks sekolah tanpa batas koneksi untuk jaminan sumber daya pembelajaran daring berkecepatan dewa.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy;2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas & Berkarakter</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>