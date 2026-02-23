<?php
require_once 'koneksi.php';

$success_message = "";
$error_message   = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $subjek   = trim($_POST['subjek'] ?? '');
    $pesan    = trim($_POST['pesan'] ?? '');

    if (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
        $error_message = "Semua field harus diisi!";
    } else {
        $stmt = mysqli_prepare($KONEKSI,
            "INSERT INTO pesan (username, email, judul, deskripsi) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $subjek, $pesan);

        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Terima kasih <strong>$nama</strong>, pesan Anda telah kami terima. Kami akan segera menghubungi Anda.";
        } else {
            $error_message = "Gagal mengirim pesan. Silakan coba lagi.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan - Website Sekolah</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===========================
           FORM STYLES (INTERNAL)
           =========================== */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
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
                        <li><a href="fasilitas.php">Fasilitas</a></li>
                        <li><a href="ekskul.php">Ekstrakulikuler</a></li>
                    </ul>
                </li>
                <li><a href="berita.php">Berita</a></li>
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

    <!-- Message Section -->
    <section class="section active" style="margin-top: 100px;">
        <h2 class="section-title">Kirim Pesan</h2>

        <div class="form-container">
            <?php if (!empty($success_message)): ?>
                <div style="background-color: #d1fae5; color: #065f46; padding: 1.2rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #6ee7b7; display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">✅</span>
                    <span><?php echo $success_message; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div style="background-color: #fee2e2; color: #991b1b; padding: 1.2rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #fca5a5; display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">❌</span>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <?php if (empty($success_message)): ?>
                <p style="text-align: center; color: var(--text-gray); margin-bottom: 2rem;">
                    Silakan isi formulir di bawah ini untuk mengirimkan kritik, saran, atau pertanyaan kepada kami.
                </p>
            <?php endif; ?>

            <?php if (empty($success_message)): ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                        placeholder="Masukkan nama lengkap Anda"
                        value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        placeholder="Masukkan alamat email Anda"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="subjek">Subjek</label>
                    <input type="text" id="subjek" name="subjek" class="form-control" placeholder="Judul pesan"
                        value="<?php echo htmlspecialchars($_POST['subjek'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="pesan">Pesan</label>
                    <textarea id="pesan" name="pesan" class="form-control" placeholder="Tuliskan pesan Anda di sini..."
                        required><?php echo htmlspecialchars($_POST['pesan'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <span>✉️</span> Kirim Pesan
                </button>
            </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SMP IBNU AQIL. All Rights Reserved.</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Membentuk Generasi Cerdas & Berkarakter</p>
    </footer>

    <script src="script.js"></script>
    <script>
        // Simple client-side validation or enhancement could go here
    </script>
</body>

</html>