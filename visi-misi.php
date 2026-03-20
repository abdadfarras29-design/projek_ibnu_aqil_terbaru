<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visi & Misi - Website Sekolah</title>
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

    <!-- Vision Mission Section -->
    <style>
        .vm-hero {
            text-align: center;
            padding: 4rem 2rem 2rem;
            position: relative;
        }

        .vm-hero::before {
            content: "";
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
        }

        .vm-title {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .vm-subtitle {
            color: var(--text-gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .vision-mission-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 4rem;
        }

        /* Kartu Visi */
        .vm-card.vision {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 24px;
            padding: 4rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.04);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .vm-card.vision:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(16, 185, 129, 0.1);
        }

        .vision-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .vision-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.5;
            font-style: italic;
        }

        .quote-left,
        .quote-right {
            position: absolute;
            font-size: 8rem;
            color: rgba(16, 185, 129, 0.05);
            font-family: serif;
            line-height: 1;
        }

        .quote-left {
            top: 0rem;
            left: 1rem;
        }

        .quote-right {
            bottom: -2rem;
            right: 2rem;
        }

        /* Kartu Misi */
        .vm-card.mission {
            background: var(--white);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            position: relative;
        }

        .mission-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px dashed rgba(16, 185, 129, 0.2);
        }

        .mission-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--dark-green);
        }

        .mission-list {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .mission-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .mission-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .mission-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background: rgba(16, 185, 129, 0.05);
            z-index: -1;
            transition: width 0.4s ease;
        }

        .mission-item:hover {
            transform: translateX(5px);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .mission-item:hover::before {
            width: 100%;
        }

        .mission-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 900;
            box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3);
        }

        .mission-text {
            color: var(--text-dark);
            font-size: 1.05rem;
            line-height: 1.6;
            font-weight: 500;
        }
    </style>

    <section style="margin-top: 100px;">
        <div class="vm-hero">
            <h1 class="vm-title">Visi & Misi</h1>
            <p class="vm-subtitle">Komitmen kami dalam membentuk generasi penerus bangsa yang berkualitas, berprestasi,
                dan berakhlakul karimah.</p>
        </div>

        <div class="vision-mission-wrapper">
            <!-- Vision -->
            <div class="vm-card vision">
                <div class="quote-left">"</div>
                <div class="vision-icon">Visi</div>
                <div class="vision-text">
                    Mewujudkan generasi yang unggul dalam IPTEK dan kokoh dalam IMTAK.
                </div>
                <div class="quote-right">"</div>
            </div>

            <!-- Mission -->
            <div class="vm-card mission">
                <div class="mission-header">
                    <h2 class="mission-title">Misi Kami</h2>
                </div>

                <ul class="mission-list">
                    <li class="mission-item">
                        <div class="mission-icon">1</div>
                        <div class="mission-text">Menyelenggarakan pendidikan berkualitas dengan kurikulum yang adaptif
                            dan inovatif.</div>
                    </li>
                    <li class="mission-item">
                        <div class="mission-icon">2</div>
                        <div class="mission-text">Mengembangkan potensi akademik dan non-akademik siswa secara optimal
                            dan terpadu.</div>
                    </li>
                    <li class="mission-item">
                        <div class="mission-icon">3</div>
                        <div class="mission-text">Membentuk karakter siswa yang berakhlak mulia, jujur, tangguh, dan
                            bertanggung jawab.</div>
                    </li>
                    <li class="mission-item">
                        <div class="mission-icon">4</div>
                        <div class="mission-text">Menciptakan lingkungan belajar yang kondusif, inspiratif, aman, dan
                            nyaman.</div>
                    </li>
                    <li class="mission-item">
                        <div class="mission-icon">5</div>
                        <div class="mission-text">Meningkatkan kompetensi tenaga pendidik dan kependidikan secara
                            berkualitas dan berkelanjutan.</div>
                    </li>
                    <li class="mission-item">
                        <div class="mission-icon">6</div>
                        <div class="mission-text">Membangun kerja sama strategis dengan berbagai pihak untuk
                            memaksimalkan pendidikan.</div>
                    </li>
                </ul>
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