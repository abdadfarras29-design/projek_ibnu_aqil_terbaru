<?php
require_once 'koneksi.php';

// Ambil ID dari URL
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: berita.php");
    exit();
}

// Ambil berita berdasarkan ID
$stmt = mysqli_prepare($KONEKSI, "SELECT * FROM berita WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$berita = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$berita) {
    header("Location: berita.php");
    exit();
}

// Ambil berita terkait (kategori sama, bukan berita ini, maks 3)
$stmt2 = mysqli_prepare($KONEKSI, "SELECT * FROM berita WHERE kategori = ? AND id != ? ORDER BY tanggal DESC LIMIT 3");
mysqli_stmt_bind_param($stmt2, "si", $berita['kategori'], $id);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$related = [];
while ($r = mysqli_fetch_assoc($result2)) {
    $related[] = $r;
}
mysqli_stmt_close($stmt2);

// Jika kurang dari 3, tambah dari berita lain
if (count($related) < 3) {
    $needed = 3 - count($related);
    $existing_ids = array_merge([$id], array_column($related, 'id'));
    $id_list = implode(',', array_map('intval', $existing_ids));
    $stmt3 = mysqli_query($KONEKSI, "SELECT * FROM berita WHERE id NOT IN ($id_list) ORDER BY tanggal DESC LIMIT $needed");
    while ($r = mysqli_fetch_assoc($stmt3)) {
        $related[] = $r;
    }
}

function formatTanggal($tgl) {
    if (!$tgl) return '';
    $ts = strtotime($tgl);
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date('d', $ts) . ' ' . $bulan[(int)date('m', $ts)] . ' ' . date('Y', $ts);
}

$badge_map = [
    'pengumuman'    => ['bg' => '#3b82f6', 'text' => '#fff', 'label' => 'Pengumuman', 'icon' => '📢'],
    'prestasi'      => ['bg' => '#f59e0b', 'text' => '#fff', 'label' => 'Prestasi',    'icon' => '🏆'],
    'pilihan utama' => ['bg' => '#10b981', 'text' => '#fff', 'label' => 'Pilihan Utama','icon' => '⭐'],
];
$kat      = $berita['kategori'];
$badge    = $badge_map[$kat] ?? ['bg' => '#6b7280', 'text' => '#fff', 'label' => ucfirst($kat), 'icon' => '📰'];
$tgl_fmt  = formatTanggal($berita['tanggal']);
$page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($berita['judul']); ?> - SMP IBNU AQIL</title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($berita['deskripsi'], 0, 160)); ?>">
    <meta property="og:title"       content="<?php echo htmlspecialchars($berita['judul']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($berita['deskripsi']); ?>">
    <?php if (!empty($berita['foto'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($berita['foto']); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; }

        /* ── Breadcrumb ── */
        .breadcrumb-bar {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: .75rem 0;
            margin-top: 72px;
        }
        .breadcrumb-inner {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .85rem;
            color: #9ca3af;
        }
        .breadcrumb-inner a {
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
        }
        .breadcrumb-inner a:hover { text-decoration: underline; }
        .breadcrumb-inner .sep { color: #d1d5db; }

        /* ── Hero Banner ── */
        .detail-hero {
            width: 100%;
            max-height: 480px;
            overflow: hidden;
            position: relative;
            background: #1f2937;
        }
        .detail-hero img {
            width: 100%;
            max-height: 480px;
            object-fit: cover;
            display: block;
            opacity: .85;
            transition: opacity .4s;
        }
        .detail-hero img:hover { opacity: 1; }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,.65) 0%, rgba(0,0,0,0) 60%);
        }
        .hero-badge-wrap {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 900px;
            padding: 0 1.5rem;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem 1rem;
            border-radius: 50px;
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
            backdrop-filter: blur(4px);
        }
        .no-hero {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 240px;
            font-size: 5rem;
        }

        /* ── Article Wrapper ── */
        .article-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }

        /* ── Meta Strip ── */
        .meta-strip {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .meta-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem 1rem;
            border-radius: 50px;
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
        }
        .meta-date {
            color: #6b7280;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        /* ── Article Title ── */
        .article-title {
            font-size: clamp(1.6rem, 4vw, 2.4rem);
            font-weight: 800;
            color: #111827;
            line-height: 1.3;
            margin-bottom: 1.5rem;
            letter-spacing: -.5px;
        }

        /* ── Divider ── */
        .article-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #059669);
            border-radius: 2px;
            margin-bottom: 2rem;
        }

        /* ── Article Body ── */
        .article-body {
            font-size: 1.1rem;
            line-height: 1.9;
            color: #374151;
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            border: 1px solid #f0f0f0;
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* ── Share Section ── */
        .share-section {
            margin-top: 2.5rem;
            background: linear-gradient(135deg, #f0fdf4, #fff);
            border-radius: 16px;
            padding: 1.75rem 2rem;
            border: 1px solid #d1fae5;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .share-label {
            font-weight: 700;
            color: #374151;
            font-size: .95rem;
        }
        .share-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem 1.1rem;
            border-radius: 50px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform .2s, box-shadow .2s;
            border: none;
            cursor: pointer;
        }
        .share-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.15); }
        .share-wa       { background: #25d366; color: #fff; }
        .share-fb       { background: #1877f2; color: #fff; }
        .share-copy     { background: #4b5563; color: #fff; }
        .share-copied   { background: #10b981 !important; }

        /* ── Back Button ── */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .65rem 1.5rem;
            background: #fff;
            border: 2px solid #10b981;
            color: #10b981;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            font-size: .9rem;
            transition: all .25s;
            margin-bottom: 2rem;
        }
        .back-btn:hover {
            background: #10b981;
            color: #fff;
            transform: translateX(-4px);
        }

        /* ── Related News ── */
        .related-section {
            max-width: 900px;
            margin: 0 auto 4rem;
            padding: 0 1.5rem;
        }
        .related-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .related-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, #10b981, transparent);
            border-radius: 2px;
        }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        .related-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,.07);
            border: 1px solid #f3f4f6;
            transition: all .3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .related-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(16,185,129,.12);
        }
        .related-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #e5e7eb;
        }
        .related-img-ph {
            width: 100%;
            height: 150px;
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        .related-body {
            padding: 1rem 1.2rem 1.2rem;
        }
        .related-date {
            font-size: .78rem;
            color: #10b981;
            font-weight: 600;
            margin-bottom: .4rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }
        .related-card h4 {
            font-size: .95rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .related-kat {
            display: inline-block;
            margin-top: .5rem;
            padding: .15rem .6rem;
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 700;
            color: #fff;
        }

        @media (max-width: 640px) {
            .article-body { padding: 1.5rem; font-size: 1rem; }
            .share-section { flex-direction: column; align-items: flex-start; }
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
                        <li><a href="visi-misi.php">Visi &amp; Misi</a></li>
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
                <span></span><span></span><span></span>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="breadcrumb-inner">
            <a href="index.php">🏠 Home</a>
            <span class="sep">›</span>
            <a href="berita.php">Berita</a>
            <span class="sep">›</span>
            <span style="color:#374151;font-weight:500;">
                <?php echo htmlspecialchars(mb_strimwidth($berita['judul'], 0, 55, '...')); ?>
            </span>
        </div>
    </div>

    <!-- Hero Image -->
    <?php if (!empty($berita['foto'])): ?>
    <div class="detail-hero">
        <img src="<?php echo htmlspecialchars($berita['foto']); ?>"
             alt="<?php echo htmlspecialchars($berita['judul']); ?>"
             onerror="this.parentElement.innerHTML='<div class=\'no-hero\'>📰</div>'">
        <div class="hero-overlay"></div>
        <div class="hero-badge-wrap">
            <span class="hero-badge" style="background: <?php echo $badge['bg']; ?>;">
                <?php echo $badge['icon']; ?> <?php echo htmlspecialchars($badge['label']); ?>
            </span>
        </div>
    </div>
    <?php else: ?>
    <div class="detail-hero">
        <div class="no-hero">📰</div>
    </div>
    <?php endif; ?>

    <!-- Article -->
    <div class="article-wrapper">

        <a href="berita.php" class="back-btn">← Kembali ke Berita</a>

        <!-- Meta -->
        <div class="meta-strip">
            <span class="meta-badge" style="background:<?php echo $badge['bg']; ?>;">
                <?php echo $badge['icon']; ?> <?php echo htmlspecialchars($badge['label']); ?>
            </span>
            <span class="meta-date">
                📅 <?php echo $tgl_fmt; ?>
            </span>
        </div>

        <!-- Title -->
        <h1 class="article-title"><?php echo htmlspecialchars($berita['judul']); ?></h1>
        <div class="article-divider"></div>

        <!-- Body -->
        <div class="article-body"><?php echo htmlspecialchars($berita['deskripsi']); ?></div>

        <!-- Share -->
        <div class="share-section">
            <span class="share-label">🔗 Bagikan berita ini:</span>
            <a class="share-btn share-wa"
               href="https://wa.me/?text=<?php echo urlencode($berita['judul'] . ' — ' . $page_url); ?>"
               target="_blank" rel="noopener">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.138.564 4.138 1.541 5.875L.057 23.93l6.23-1.459A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.961 0-3.785-.538-5.343-1.47l-.383-.228-3.983.932.974-3.87-.249-.398A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                WhatsApp
            </a>
            <a class="share-btn share-fb"
               href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($page_url); ?>"
               target="_blank" rel="noopener">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
                Facebook
            </a>
            <button class="share-btn share-copy" id="copyBtn" onclick="salinLink()">
                📋 Salin Link
            </button>
        </div>

    </div><!-- /article-wrapper -->

    <!-- Related News -->
    <?php if (!empty($related)): ?>
    <div class="related-section">
        <h2 class="related-title">📰 Berita Lainnya</h2>
        <div class="related-grid">
            <?php foreach ($related as $rel): ?>
            <?php
                $rkat   = $rel['kategori'];
                $rbadge = $badge_map[$rkat] ?? ['bg'=>'#6b7280','label'=>ucfirst($rkat),'icon'=>'📰'];
            ?>
            <a href="detail-berita.php?id=<?php echo $rel['id']; ?>" class="related-card">
                <?php if (!empty($rel['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($rel['foto']); ?>"
                         alt="<?php echo htmlspecialchars($rel['judul']); ?>"
                         class="related-img"
                         onerror="this.parentElement.innerHTML='<div class=\'related-img-ph\'>📰</div>'">
                <?php else: ?>
                    <div class="related-img-ph">📰</div>
                <?php endif; ?>
                <div class="related-body">
                    <div class="related-date">📅 <?php echo formatTanggal($rel['tanggal']); ?></div>
                    <h4><?php echo htmlspecialchars($rel['judul']); ?></h4>
                    <span class="related-kat" style="background:<?php echo $rbadge['bg']; ?>;">
                        <?php echo $rbadge['icon']; ?> <?php echo htmlspecialchars($rbadge['label']); ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas &amp; Berkarakter</p>
    </footer>

    <script src="script.js"></script>
    <script>
        function salinLink() {
            const btn = document.getElementById('copyBtn');
            navigator.clipboard.writeText(window.location.href).then(() => {
                btn.textContent = '✅ Tersalin!';
                btn.classList.add('share-copied');
                setTimeout(() => {
                    btn.textContent = '📋 Salin Link';
                    btn.classList.remove('share-copied');
                }, 2500);
            }).catch(() => {
                // Fallback
                const el = document.createElement('textarea');
                el.value = window.location.href;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                btn.textContent = '✅ Tersalin!';
                btn.classList.add('share-copied');
                setTimeout(() => {
                    btn.textContent = '📋 Salin Link';
                    btn.classList.remove('share-copied');
                }, 2500);
            });
        }
    </script>
</body>

</html>
