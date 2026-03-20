<?php
require_once 'koneksi.php';

// Ambil berita pilihan utama (featured)
$q_featured = mysqli_query($KONEKSI, "SELECT * FROM berita WHERE kategori = 'pilihan utama' ORDER BY tanggal DESC, id DESC LIMIT 1");
$featured = mysqli_fetch_assoc($q_featured);

// Ambil berita lainnya (bukan pilihan utama, atau jika tidak ada pilihan utama, semua berita)
if ($featured) {
    $q_news = mysqli_prepare($KONEKSI, "SELECT * FROM berita WHERE id != ? ORDER BY tanggal DESC, id DESC");
    mysqli_stmt_bind_param($q_news, "i", $featured['id']);
    mysqli_stmt_execute($q_news);
    $result_news = mysqli_stmt_get_result($q_news);
} else {
    // Jika tidak ada pilihan utama, ambil semua berita
    $result_news = mysqli_query($KONEKSI, "SELECT * FROM berita ORDER BY tanggal DESC, id DESC");
}

$news_list = [];
while ($row = mysqli_fetch_assoc($result_news)) {
    $news_list[] = $row;
}

function formatTanggal($tgl) {
    if (!$tgl) return '';
    $ts = strtotime($tgl);
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date('d', $ts) . ' ' . $bulan[(int)date('m', $ts)] . ' ' . date('Y', $ts);
}

$badge_colors = [
    'pengumuman'    => ['bg' => '#3b82f6', 'label' => 'PENGUMUMAN'],
    'prestasi'      => ['bg' => '#f59e0b', 'label' => 'PRESTASI'],
    'pilihan utama' => ['bg' => '#10b981', 'label' => 'PILIHAN UTAMA'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Sekolah - SMP IBNU AQIL</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
            margin-top: 3rem;
        }

        .news-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15);
        }

        .news-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background-color: #f3f4f6;
        }

        .news-image-placeholder {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .news-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .news-date {
            font-size: 0.85rem;
            color: var(--primary-green);
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .news-card h3 {
            font-size: 1.4rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .news-card p {
            color: var(--text-gray);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
            width: fit-content;
        }

        .read-more {
            margin-top: auto;
            color: var(--dark-green);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: gap 0.2s;
        }

        .read-more:hover {
            gap: 0.8rem;
        }

        .featured-news {
            margin-bottom: 4rem;
            background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
            border-radius: 30px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        @media (max-width: 992px) {
            .featured-news {
                grid-template-columns: 1fr;
            }
        }

        .featured-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .featured-img-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
        }

        .featured-content {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .badge-special {
            background: #fbbf24;
            color: #78350f;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            width: fit-content;
            margin-bottom: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: #f9fafb;
            border-radius: 20px;
            margin-top: 3rem;
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #6b7280;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #9ca3af;
        }
    </style>
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
                        <li><a href="berita.php" class="active">Berita</a></li>
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

    <!-- Header Section -->
    <section class="section active" style="margin-top: 100px;">
        <h2 class="section-title">Berita Terkini</h2>

        <?php if (!$featured && empty($news_list)): ?>
            <!-- Empty state -->
            <div class="empty-state">
                <div class="icon">📰</div>
                <h3>Belum Ada Berita</h3>
                <p>Belum ada berita yang dipublikasikan. Pantau terus halaman ini!</p>
            </div>

        <?php else: ?>

            <?php if ($featured): ?>
            <!-- Featured News -->
            <div class="featured-news">
                <?php if (!empty($featured['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($featured['foto']); ?>"
                         alt="<?php echo htmlspecialchars($featured['judul']); ?>"
                         class="featured-img"
                         onerror="this.parentElement.innerHTML='<div class=\'featured-img-placeholder\'>📰</div>'">
                <?php else: ?>
                    <div class="featured-img-placeholder">📰</div>
                <?php endif; ?>
                <div class="featured-content">
                    <div class="badge-special">PILIHAN UTAMA</div>
                    <div class="news-date">📅 <?php echo formatTanggal($featured['tanggal']); ?></div>
                    <h2><?php echo htmlspecialchars($featured['judul']); ?></h2>
                    <p><?php echo htmlspecialchars($featured['deskripsi']); ?></p>
                    <a href="detail-berita.php?id=<?php echo $featured['id']; ?>" class="read-more">Baca Selengkapnya →</a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($news_list)): ?>
            <div class="news-grid">
                <?php foreach ($news_list as $berita): ?>
                <?php
                    $kat   = $berita['kategori'];
                    $bgKat = $badge_colors[$kat]['bg'] ?? '#6b7280';
                    $lblKat = $badge_colors[$kat]['label'] ?? strtoupper($kat);
                ?>
                <div class="news-card">
                    <?php if (!empty($berita['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($berita['foto']); ?>"
                             alt="<?php echo htmlspecialchars($berita['judul']); ?>"
                             class="news-image"
                             onerror="this.parentElement.innerHTML='<div class=\'news-image-placeholder\'>📰</div>'">
                    <?php else: ?>
                        <div class="news-image-placeholder">📰</div>
                    <?php endif; ?>
                    <div class="news-content">
                        <span class="news-badge" style="background: <?php echo $bgKat; ?>;"><?php echo $lblKat; ?></span>
                        <div class="news-date">📅 <?php echo formatTanggal($berita['tanggal']); ?></div>
                        <h3><?php echo htmlspecialchars($berita['judul']); ?></h3>
                        <p><?php echo htmlspecialchars($berita['deskripsi']); ?></p>
                        <a href="detail-berita.php?id=<?php echo $berita['id']; ?>" class="read-more">Baca Selengkapnya →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php elseif ($featured): ?>
            <!-- Only featured news, no other news -->
            <div style="text-align:center; padding:2rem; color:#9ca3af;">
                <p>Hanya ada satu berita pilihan utama. Berita lainnya akan segera hadir!</p>
            </div>
            <?php endif; ?>

        <?php endif; ?>

    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas & Berkarakter</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>