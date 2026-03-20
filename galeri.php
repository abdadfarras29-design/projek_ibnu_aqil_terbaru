<?php
require_once 'koneksi.php';

$stmt = mysqli_prepare($KONEKSI, "SELECT * FROM galery ORDER BY id DESC");
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$galeri = [];
while ($row = mysqli_fetch_assoc($res)) {
    $galeri[] = $row;
}
mysqli_stmt_close($stmt);


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - Website Sekolah</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .premium-hero { background: linear-gradient(135deg, var(--dark-green), var(--primary-green)); padding: 8rem 2rem 5rem; text-align: center; color: white; border-radius: 0 0 50px 50px; margin-bottom: 4rem; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2); position: relative; overflow: hidden; }
        .premium-hero::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%); pointer-events: none; }
        .premium-hero h2 { font-size: 3rem; margin-bottom: 1.5rem; font-weight: 800; letter-spacing: -1px; position: relative; z-index: 1; }
        .premium-hero p { font-size: 1.25rem; opacity: 0.95; max-width: 700px; margin: 0 auto; line-height: 1.6; position: relative; z-index: 1; font-weight: 300;}
        
        .premium-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2.5rem; padding: 0 2rem; max-width: 1250px; margin: 0 auto 6rem; }
        
        .galeri-card { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; display: flex; flex-direction: column; cursor: pointer;}
        .galeri-card:hover { transform: translateY(-12px); box-shadow: 0 25px 45px rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.2); }
        
        .galeri-img-wrap { width: 100%; height: 240px; background: #e5e7eb; overflow: hidden; position: relative; }
        .galeri-img-wrap::after { content: ''; position: absolute; inset: 0; background: linear-gradient(0deg, rgba(0,0,0,0.4) 0%, transparent 50%); opacity: 0; transition: opacity 0.4s ease; }
        .galeri-card:hover .galeri-img-wrap::after { opacity: 1; }
        
        .galeri-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
        .galeri-card:hover .galeri-img { transform: scale(1.1); }
        
        .play-icon { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.8); background: rgba(255,255,255,0.9); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2; opacity: 0; transition: all 0.4s ease; color: var(--primary-green); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        .galeri-card:hover .play-icon { opacity: 1; transform: translate(-50%, -50%) scale(1); }

        .galeri-content { padding: 1.8rem; flex-grow: 1; display: flex; flex-direction: column; }
        .galeri-kategori { display: inline-block; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-green); background: rgba(16,185,129,0.1); padding: 5px 12px; border-radius: 50px; margin-bottom: 1rem; align-self: flex-start; }
        .galeri-judul { font-size: 1.35rem; color: #1f2937; margin-bottom: 0.8rem; font-weight: 700; letter-spacing: -0.3px; line-height: 1.3; }
        .galeri-deskripsi { color: #6b7280; font-size: 1rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin: 0; }
        
        /* Modal Photo Focus */
        #modalEnlarge { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.95); backdrop-filter: blur(8px); opacity: 0; transition: opacity 0.3s ease; }
        #modalEnlarge.show { opacity: 1; }
        
        .modal-enlarge-content { display: block; max-width: 85%; max-height: 80vh; margin: 2rem auto 1rem; border-radius: 12px; box-shadow: 0 30px 60px rgba(0,0,0,0.4); transform: scale(0.9); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); object-fit: contain;}
        #modalEnlarge.show .modal-enlarge-content { transform: scale(1); }
        
        .modal-caption-wrap { text-align: center; color: white; max-width: 800px; margin: 0 auto; padding: 0 2rem; }
        .modal-enlarge-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -0.5px; }
        .modal-enlarge-desc { font-size: 1.1rem; color: #cbd5e1; opacity: 0.9; line-height: 1.6;}
        
        .modal-close { position: absolute; top: 2rem; right: 3rem; color: #fff; background: rgba(255,255,255,0.1); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; cursor: pointer; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2); }
        .modal-close:hover { background: #ef4444; border-color: #ef4444; transform: rotate(90deg); }
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
                    <a href="#" class="dropbtn active">Kegiatan ▾</a>
                    <ul class="dropdown-content">
                        <li><a href="berita.php">Berita</a></li>
                        <li><a href="fasilitas.php">Fasilitas</a></li>
                        <li><a href="ekskul.php">Ekstrakulikuler</a></li>
                        <li><a href="galeri.php" style="color:var(--primary-green)!important;font-weight:bold;">Galeri</a></li>
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

    <!-- Galeri Section -->
    <section>
        <div class="premium-hero">
            <h2>Galeri Momentum Sekolah</h2>
            <p>Rangkuman bingkai dokumentatif terbaik sepanjang perjalanan aktivitas, dedikasi, dan memori masa sekolah di lingkungan nyata kami.</p>
        </div>
        
        <div class="premium-grid">
            <?php foreach ($galeri as $item): 
                $foto_src = htmlspecialchars($item['foto'] ? $item['foto'] : 'https://via.placeholder.com/600x400?text=No+Image');
                $kategori = !empty($item['kategori']) ? htmlspecialchars($item['kategori']) : 'DOKUMENTASI';
                $judul    = htmlspecialchars($item['judul']);
                $desk     = htmlspecialchars($item['deskripsi']);
            ?>
                <div class="galeri-card" onclick="bukaEnlargeModal('<?php echo $foto_src; ?>', '<?php echo addslashes($judul); ?>', '<?php echo addslashes($desk); ?>')">
                    <div class="galeri-img-wrap">
                        <img src="<?php echo $foto_src; ?>" alt="<?php echo $judul; ?>" class="galeri-img" loading="lazy">
                        <div class="play-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                        </div>
                    </div>
                    <div class="galeri-content">
                        <span class="galeri-kategori"><?php echo $kategori; ?></span>
                        <h3 class="galeri-judul"><?php echo $judul; ?></h3>
                        <?php if (!empty($desk)): ?>
                            <p class="galeri-deskripsi"><?php echo nl2br($desk); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Modal Gambar Membesar -->
    <div id="modalEnlarge" onclick="if(event.target.id==='modalEnlarge'){ tutupEnlargeModal(); }">
        <div class="modal-close" onclick="tutupEnlargeModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </div>
        <img class="modal-enlarge-content" id="imgEnlarged" src="" alt="Enlarged Phase">
        <div class="modal-caption-wrap">
            <div class="modal-enlarge-title" id="captionTitle"></div>
            <div class="modal-enlarge-desc" id="captionDesc"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas & Berkarakter</p>
    </footer>

    <script src="script.js"></script>
    <script>
        function bukaEnlargeModal(src, title, desc) {
            const modal = document.getElementById('modalEnlarge');
            const img = document.getElementById('imgEnlarged');
            
            modal.style.display = "block";
            // trigger reflow untuk transisi CSS
            void modal.offsetWidth; 
            modal.classList.add('show');
            
            img.src = src;
            document.getElementById('captionTitle').textContent = title;
            document.getElementById('captionDesc').textContent = desc;
            document.body.style.overflow = "hidden";
        }

        function tutupEnlargeModal() {
            const modal = document.getElementById('modalEnlarge');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = "none";
                document.body.style.overflow = "auto";
            }, 300); // Tunggu animasi pudar
        }
    </script>
</body>

</html>

