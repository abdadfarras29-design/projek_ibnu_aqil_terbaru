<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'koneksi.php';

// Handle hapus pesan
if (isset($_GET['hapus_pesan']) && is_numeric($_GET['hapus_pesan'])) {
    $id_hapus = intval($_GET['hapus_pesan']);
    $stmt = mysqli_prepare($KONEKSI, "DELETE FROM pesan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_hapus);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: dashboard-superadmin.php?tab=messages&notif=hapus_berhasil");
    exit();
}

// Ambil semua pesan dari database
$result_pesan = mysqli_query($KONEKSI, "SELECT * FROM pesan ORDER BY id DESC");
$daftar_pesan = [];
while ($row = mysqli_fetch_assoc($result_pesan)) {
    $daftar_pesan[] = $row;
}
$total_pesan = count($daftar_pesan);

// Ambil total berita
$res_total_berita = mysqli_query($KONEKSI, "SELECT COUNT(*) as total FROM berita");
$total_berita = 0;
if ($res_total_berita) {
    $r = mysqli_fetch_assoc($res_total_berita);
    $total_berita = $r['total'];
}

// NATIVE SERVER CRUD GALERI
if (isset($_GET['hapus_galeri']) && is_numeric($_GET['hapus_galeri'])) {
    $id_hapus = intval($_GET['hapus_galeri']);
    $q_foto = mysqli_prepare($KONEKSI, "SELECT foto FROM galery WHERE id = ?");
    mysqli_stmt_bind_param($q_foto, "i", $id_hapus);
    mysqli_stmt_execute($q_foto);
    $res_foto = mysqli_stmt_get_result($q_foto);
    if ($row_foto = mysqli_fetch_assoc($res_foto)) {
        if (!empty($row_foto['foto']) && file_exists($row_foto['foto'])) {
            @unlink($row_foto['foto']);
        }
    }
    mysqli_stmt_close($q_foto);

    $stmt = mysqli_prepare($KONEKSI, "DELETE FROM galery WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_hapus);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "<script>window.location.href='dashboard-superadmin.php?tab=galeri&notif_galeri=hapus_berhasil';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_galeri'])) {
    $action_galeri = $_POST['action_galeri'];
    $judul = trim($_POST['judul_galeri'] ?? '');
    $kategori_val = trim($_POST['kategori_galeri'] ?? '');
    $deskripsi = trim($_POST['deskripsi_galeri'] ?? '');
    $foto_path = $_POST['foto_lama'] ?? '';
    if (isset($_FILES['foto_galeri']) && $_FILES['foto_galeri']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/galery/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = pathinfo($_FILES['foto_galeri']['name'], PATHINFO_EXTENSION);
        $filename = 'galery_' . time() . '_' . mt_rand(100, 999) . '.' . strtolower($ext);
        $dest = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['foto_galeri']['tmp_name'], $dest)) {
            $foto_path = $dest;
            if (!empty($_POST['foto_lama']) && file_exists($_POST['foto_lama'])) {
                @unlink($_POST["foto_lama"]);
            }
        }
    }

    if ($action_galeri === 'create') {
        $stmt = mysqli_prepare($KONEKSI, "INSERT INTO galery (foto, kategori, judul, deskripsi) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $foto_path, $kategori_val, $judul, $deskripsi);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif ($action_galeri === 'update' && !empty($_POST['id_galeri'])) {
        $id = intval($_POST['id_galeri']);
        $stmt = mysqli_prepare($KONEKSI, "UPDATE galery SET foto=?, kategori=?, judul=?, deskripsi=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $foto_path, $kategori_val, $judul, $deskripsi, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    $msg = ($action_galeri === 'create') ? 'tambah_berhasil' : 'edit_berhasil';
    echo "<script>window.location.href='dashboard-superadmin.php?tab=galeri&notif_galeri=$msg';</script>";
    exit();
}

$res_galeri = mysqli_query($KONEKSI, "SELECT * FROM galery ORDER BY id DESC");
$daftar_galeri = [];
while ($row = mysqli_fetch_assoc($res_galeri)) {
    $daftar_galeri[] = $row;
}

// NATIVE SERVER CRUD BERITA
if (isset($_GET['hapus_berita']) && is_numeric($_GET['hapus_berita'])) {
    $id_hapus = intval($_GET['hapus_berita']);
    $q_foto = mysqli_prepare($KONEKSI, "SELECT foto FROM berita WHERE id = ?");
    mysqli_stmt_bind_param($q_foto, "i", $id_hapus);
    mysqli_stmt_execute($q_foto);
    $res_foto = mysqli_stmt_get_result($q_foto);
    if ($row_foto = mysqli_fetch_assoc($res_foto)) {
        if (!empty($row_foto['foto']) && file_exists($row_foto['foto'])) {
            @unlink($row_foto['foto']);
        }
    }
    mysqli_stmt_close($q_foto);

    $stmt = mysqli_prepare($KONEKSI, "DELETE FROM berita WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_hapus);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "<script>window.location.href='dashboard-superadmin.php?tab=berita&notif_berita=hapus_berhasil';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_berita'])) {
    $action_berita = $_POST['action_berita'];
    $judul = trim($_POST['judul_berita'] ?? '');
    $kategori = trim($_POST['kategori_berita'] ?? '');
    $tanggal = $_POST['tanggal_berita'] ?? date('Y-m-d');
    $deskripsi = trim($_POST['deskripsi_berita'] ?? '');
    $foto_path = $_POST['foto_lama_berita'] ?? '';

    if (isset($_FILES['foto_berita']) && $_FILES['foto_berita']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/berita/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = pathinfo($_FILES['foto_berita']['name'], PATHINFO_EXTENSION);
        $filename = 'berita_' . time() . '_' . mt_rand(100, 999) . '.' . strtolower($ext);
        $dest = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['foto_berita']['tmp_name'], $dest)) {
            $foto_path = $dest;
            if (!empty($_POST['foto_lama_berita']) && file_exists($_POST['foto_lama_berita'])) {
                @unlink($_POST["foto_lama_berita"]);
            }
        }
    }

    if ($action_berita === 'create') {
        $stmt = mysqli_prepare($KONEKSI, "INSERT INTO berita (judul, foto, kategori, tanggal, deskripsi) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $judul, $foto_path, $kategori, $tanggal, $deskripsi);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif ($action_berita === 'update' && !empty($_POST['id_berita'])) {
        $id = intval($_POST['id_berita']);
        $stmt = mysqli_prepare($KONEKSI, "UPDATE berita SET judul=?, foto=?, kategori=?, tanggal=?, deskripsi=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $judul, $foto_path, $kategori, $tanggal, $deskripsi, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    $msg = ($action_berita === 'create') ? 'tambah_berhasil' : 'edit_berhasil';
    echo "<script>window.location.href='dashboard-superadmin.php?tab=berita&notif_berita=$msg';</script>";
    exit();
}

$res_berita_query = mysqli_query($KONEKSI, "SELECT * FROM berita ORDER BY id DESC");
$daftar_berita_native = [];
while ($row = mysqli_fetch_assoc($res_berita_query)) {
    $daftar_berita_native[] = $row;
}

// NATIVE SERVER CRUD EKSKUL
if (isset($_GET['hapus_ekskul']) && is_numeric($_GET['hapus_ekskul'])) {
    $id_hapus = intval($_GET['hapus_ekskul']);
    $stmt = mysqli_prepare($KONEKSI, "DELETE FROM esktrakulikuler WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_hapus);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "<script>window.location.href='dashboard-superadmin.php?tab=ekskul&notif_ekskul=hapus_berhasil';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_ekskul'])) {
    $action_ekskul = $_POST['action_ekskul'];
    $nama = trim($_POST['nama_ekskul'] ?? '');
    $deskripsi = trim($_POST['deskripsi_ekskul'] ?? '');

    if ($action_ekskul === 'create') {
        $stmt = mysqli_prepare($KONEKSI, "INSERT INTO esktrakulikuler (nama, deskripsi) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $nama, $deskripsi);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif ($action_ekskul === 'update' && !empty($_POST['id_ekskul'])) {
        $id = intval($_POST['id_ekskul']);
        $stmt = mysqli_prepare($KONEKSI, "UPDATE esktrakulikuler SET nama=?, deskripsi=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $nama, $deskripsi, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    $msg = ($action_ekskul === 'create') ? 'tambah_berhasil' : 'edit_berhasil';
    echo "<script>window.location.href='dashboard-superadmin.php?tab=ekskul&notif_ekskul=$msg';</script>";
    exit();
}

$res_ekskul_query = mysqli_query($KONEKSI, "SELECT * FROM esktrakulikuler ORDER BY id DESC");
$daftar_ekskul = [];
while ($row = mysqli_fetch_assoc($res_ekskul_query)) {
    $daftar_ekskul[] = $row;
}

// NATIVE SERVER CRUD STATISTIK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_statistik'])) {
    $siswa = trim($_POST['siswa'] ?? '0');
    $guru = trim($_POST['guru'] ?? '0');
    $prestasi = trim($_POST['prestasi'] ?? '0');
    $rombongan_belajar = trim($_POST['rombongan_belajar'] ?? '0');

    // Check if a row exists
    $cek = mysqli_query($KONEKSI, "SELECT id FROM `jumlah siswa dll` LIMIT 1");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $id = $row['id'];
        $stmt = mysqli_prepare($KONEKSI, "UPDATE `jumlah siswa dll` SET `siswa`=?, `guru`=?, `prestasi`=?, `rombongan belajar`=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $siswa, $guru, $prestasi, $rombongan_belajar, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($KONEKSI, "INSERT INTO `jumlah siswa dll` (`siswa`, `guru`, `prestasi`, `rombongan belajar`) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $siswa, $guru, $prestasi, $rombongan_belajar);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    echo "<script>window.location.href='dashboard-superadmin.php?tab=statistik&notif_statistik=berhasil';</script>";
    exit();
}

$res_statistik_query = mysqli_query($KONEKSI, "SELECT * FROM `jumlah siswa dll` ORDER BY id DESC LIMIT 1");
$statistik_sekolah = $res_statistik_query ? mysqli_fetch_assoc($res_statistik_query) : null;
if (!$statistik_sekolah) {
    $statistik_sekolah = ['siswa' => '1250', 'guru' => '75', 'prestasi' => '150', 'rombongan belajar' => '30'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin - Website Sekolah</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard-style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <!-- Navbar Dashboard -->
    <nav class="dashboard-navbar">
        <div class="nav-container">
            <div class="logo" style="display: flex; align-items: center; gap: 12px; font-weight: bold; color: white; font-size: 1.2rem; letter-spacing: 0.5px;">
                <img src="Screenshot_2026-02-22-13-16-05-58_1c337646f29875672b5a61192b9010f9.png" alt="Logo"
                    style="height: 40px; width: auto; object-fit: contain; border-radius: 50%; max-width: 100%;">
                <span style="border-left: 2px solid rgba(255,255,255,0.3); padding-left: 12px;">HALAMAN ADMIN IBNU AQIL</span>
            </div>
            <div class="user-menu">
                <span class="user-name" id="userName">
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <span class="user-role super-admin">SMP IBNU AQIL</span>
                <button onclick="window.location.href='menanyakan logout.php'" class="logout-btn">Logout</button>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="#" class="menu-item active" onclick="showTab('overview')">
                    <span class="icon"></span>
                    <span>Overview</span>
                </a>

                <a href="#" class="menu-item" onclick="showTab('content')">
                    <span class="icon"></span>
                    <span>Kelola Konten</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('berita')">
                    <span class="icon"></span>
                    <span>Kelola Berita</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('galeri')">
                    <span class="icon"></span>
                    <span>Kelola Galeri</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('ekskul')">
                    <span class="icon"></span>
                    <span>Kelola Ekskul</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('statistik')">
                    <span class="icon"></span>
                    <span>Kelola Statistik</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('messages')">
                    <span class="icon"></span>
                    <span>Pesan Masuk</span>
                </a>
                <hr style="margin: 1rem 0; border: none; border-top: 1px solid #e5e7eb;">
                <a href="index.php" class="menu-item">
                    <span class="icon"></span>
                    <span>Lihat Website</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <!-- Overview Tab -->
            <div id="overview" class="tab-content active">
                <div class="page-header" style="background: linear-gradient(135deg, var(--light-gray), #fff); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border-left: 6px solid var(--primary-green); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h1 style="color: var(--dark-green); margin: 0; font-size: 2.2rem; display: flex; align-items: center; gap: 0.8rem;">
                            <span style="display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; color: var(--primary-green);">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            </span>
                            Selamat Datang di Panel Admin!
                        </h1>
                        <p style="color: var(--text-gray); margin-top: 0.5rem; font-size: 1.1rem;">Berikut adalah ringkasan kinerja dan pintasan cepat sistem SMP IBNU AQIL hari ini.</p>
                    </div>
                </div>

                <!-- Modern Stats Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
                    <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 1.5rem; border-radius: 20px; color: #fff; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3); display: flex; align-items: center; justify-content: space-between; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div>
                            <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.2rem; font-weight: 500;">Total Siswa</p>
                            <h3 style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1;"><?php echo htmlspecialchars($statistik_sekolah['siswa']); ?></h3>
                        </div>
                        <div style="opacity: 0.8; display: flex; align-items: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 1.5rem; border-radius: 20px; color: #fff; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: space-between; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div>
                            <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.2rem; font-weight: 500;">Total Guru</p>
                            <h3 style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1;"><?php echo htmlspecialchars($statistik_sekolah['guru']); ?></h3>
                        </div>
                        <div style="opacity: 0.8; display: flex; align-items: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        </div>
                    </div>

                    <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 1.5rem; border-radius: 20px; color: #fff; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: space-between; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div>
                            <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.2rem; font-weight: 500;">Total Admin</p>
                            <h3 style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1;">2</h3>
                        </div>
                        <div style="opacity: 0.8; display: flex; align-items: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </div>
                    </div>

                    <div style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); padding: 1.5rem; border-radius: 20px; color: #fff; box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3); display: flex; align-items: center; justify-content: space-between; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div>
                            <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.2rem; font-weight: 500;">Total Prestasi</p>
                            <h3 style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1;"><?php echo htmlspecialchars($statistik_sekolah['prestasi']); ?></h3>
                        </div>
                        <div style="opacity: 0.8; display: flex; align-items: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                        </div>
                    </div>
                </div>

                <!-- Modern Quick Actions -->
                <div class="section-card" style="border: none; background: #fff; border-radius: 24px; box-shadow: 0 8px 30px rgba(0,0,0,0.04); padding: 2rem;">
                    <h2 style="font-size: 1.5rem; color: var(--text-dark); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--primary-green); display: flex;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        </span>
                        Jalan Pintas
                    </h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <button onclick="showTab('content')" style="background: #f8fafc; border: 2px solid transparent; padding: 1.5rem; border-radius: 16px; cursor: pointer; text-align: left; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary-green)'; this.style.backgroundColor='#f0fdf4';" onmouseout="this.style.borderColor='transparent'; this.style.backgroundColor='#f8fafc';">
                            <span style="display: block; margin-bottom: 1rem; color: var(--primary-green);">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </span>
                            <h3 style="margin: 0; font-size: 1.2rem; color: var(--dark-green);">Edit Konten Website</h3>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: var(--text-gray);">Perbarui profil, info dll.</p>
                        </button>

                        <button onclick="showTab('berita')" style="background: #f8fafc; border: 2px solid transparent; padding: 1.5rem; border-radius: 16px; cursor: pointer; text-align: left; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary-green)'; this.style.backgroundColor='#f0fdf4';" onmouseout="this.style.borderColor='transparent'; this.style.backgroundColor='#f8fafc';">
                            <span style="display: block; margin-bottom: 1rem; color: var(--primary-green);">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </span>
                            <h3 style="margin: 0; font-size: 1.2rem; color: var(--dark-green);">Kelola Berita</h3>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: var(--text-gray);">Tulis artikel terbaru.</p>
                        </button>

                        <button onclick="showTab('galeri')" style="background: #f8fafc; border: 2px solid transparent; padding: 1.5rem; border-radius: 16px; cursor: pointer; text-align: left; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary-green)'; this.style.backgroundColor='#f0fdf4';" onmouseout="this.style.borderColor='transparent'; this.style.backgroundColor='#f8fafc';">
                            <span style="display: block; margin-bottom: 1rem; color: var(--primary-green);">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </span>
                            <h3 style="margin: 0; font-size: 1.2rem; color: var(--dark-green);">Kelola Galeri</h3>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: var(--text-gray);">Unggah foto & dokumentasi.</p>
                        </button>

                        <button onclick="showTab('messages')" style="background: #f8fafc; border: 2px solid transparent; padding: 1.5rem; border-radius: 16px; cursor: pointer; text-align: left; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary-green)'; this.style.backgroundColor='#f0fdf4';" onmouseout="this.style.borderColor='transparent'; this.style.backgroundColor='#f8fafc';">
                            <span style="display: block; margin-bottom: 1rem; color: var(--primary-green);">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            <h3 style="margin: 0; font-size: 1.2rem; color: var(--dark-green);">Lihat Pesan Masuk</h3>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: var(--text-gray);">Cek pesan dari pengunjung.</p>
                        </button>
                    </div>
                </div>
            </div>



            <!-- Content Management Tab -->
            <div id="content" class="tab-content">
                <div class="page-header">

                    <h1>Kelola Konten</h1>
                    <p>Edit konten website sekolah</p>
                </div>

                <div class="content-grid">
                    <div class="content-card">
                        <h3>Statistik</h3>
                        <p>Kelola data statistik sekolah</p>
                        <button class="btn-primary" onclick="showTab('statistik')">Edit Statistik</button>
                    </div>
                    <div class="content-card">
                        <h3>Galeri Sekolah</h3>
                        <p>Kelola foto dan dokumentasi kegiatan</p>
                        <button class="btn-primary" onclick="showTab('galeri')">Edit Galeri</button>
                    </div>
                    <div class="content-card">
                        <h3>Ekstrakulikuler</h3>
                        <p>Kelola ekstrakurikuler</p>
                        <button class="btn-primary" onclick="showTab('ekskul')">Edit Ekskul</button>
                    </div>
                    <div class="content-card">
                        <h3>Berita Sekolah</h3>
                        <p>Kelola berita dan update artikel</p>
                        <button class="btn-primary" onclick="openBeritaModal()">Edit Berita</button>
                    </div>
                    <div class="content-card">
                        <h3>Pesan Masuk</h3>
                        <p>Lihat dan kelola pesan dari pengunjung</p>
                        <button class="btn-primary" onclick="showTab('messages')">Lihat Pesan</button>
                    </div>
                </div>
            </div>

            <!-- ===================== KELOLA BERITA TAB ===================== -->
            <div id="berita" class="tab-content">
                <div class="page-header">
                    <h1> Kelola Berita</h1>
                    <button class="btn-primary" onclick="bukaFormBeritaNative('tambah')" id="btnTambahBerita">+ Tambah Berita</button>
                </div>

                <?php if (isset($_GET['notif_berita'])): ?>
                    <div style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.4rem;"></span> Operasi berita berhasil.
                    </div>
                <?php endif; ?>

                <div id="formBeritaWrapNative" style="display:none; margin-bottom: 2rem;">
                    <div class="section-card" style="border-left:4px solid #10b981;">
                        <h2 id="formBeritaTitleNative" style="margin-bottom:1.5rem;">Tambah Berita Baru</h2>
                        <form action="dashboard-superadmin.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action_berita" id="beritaActionNative" value="create">
                            <input type="hidden" name="id_berita" id="beritaIdNative" value="">
                            <input type="hidden" name="foto_lama_berita" id="beritaFotoLamaNative" value="">
                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Judul Berita <span style="color:red;">*</span></label>
                                    <input type="text" name="judul_berita" id="beritaJudulNative" class="form-control" placeholder="Judul berita..." required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                </div>
                                <div class="form-group">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Kategori <span style="color:red;">*</span></label>
                                    <input type="text" name="kategori_berita" id="beritaKategoriNative" class="form-control" required list="listKategoriBeritaNative" placeholder="Ketik kategori..." style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                    <datalist id="listKategoriBeritaNative">
                                        <option value="pengumuman">
                                        <option value="prestasi">
                                        <option value="pilihan utama">
                                    </datalist>
                                </div>
                                <div class="form-group">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Tanggal <span style="color:red;">*</span></label>
                                    <input type="date" name="tanggal_berita" id="beritaTanggalNative" class="form-control" required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Upload Foto</label>
                                    <input type="file" name="foto_berita" id="beritaFotoNative" accept="image/*" style="width:100%;padding:.6rem;border:1.5px dashed #d1d5db;border-radius:8px;">
                                    <small style="display:block;margin-top:.5rem;color:#6b7280;" id="infoFotoLamaBerita"></small>
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Deskripsi Berita <span style="color:red;">*</span></label>
                                    <textarea name="deskripsi_berita" id="beritaDeskripsiNative" class="form-control" rows="5" placeholder="Isi berita..." required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;resize:vertical;"></textarea>
                                </div>
                            </div>
                            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                                <button type="submit" class="btn-primary"> Simpan Berita</button>
                                <button type="button" class="btn-secondary" onclick="document.getElementById('formBeritaWrapNative').style.display='none'">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="section-card">
                    <h2>Daftar Berita</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_berita_native)): ?>
                                    <tr><td colspan="7" style="text-align:center;padding:2rem;color:#9ca3af;"> Belum ada berita.</td></tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($daftar_berita_native as $b): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars(!empty($b['foto']) ? $b['foto'] : 'https://via.placeholder.com/60x40?text=No+Img'); ?>" onerror="this.src='https://via.placeholder.com/60x40?text=No+Img'" style="width:60px;height:40px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="previewModalBeritaNative(<?php echo htmlspecialchars(json_encode($b)); ?>)">
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($b['judul']); ?></strong></td>
                                            <td>
                                                <?php 
                                                    $kat = strtolower($b['kategori']);
                                                    $bg = '#6b7280';
                                                    if($kat === 'pengumuman') $bg = '#3b82f6';
                                                    elseif($kat === 'prestasi') $bg = '#f59e0b';
                                                    elseif($kat === 'pilihan utama') $bg = '#10b981';
                                                ?>
                                                <span style="background:<?php echo $bg; ?>;color:#fff;padding:.2rem .6rem;border-radius:50px;font-size:.75rem;font-weight:700;">
                                                    <?php echo htmlspecialchars($b['kategori']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($b['tanggal'])); ?></td>
                                            <td style="max-width:200px;"><?php echo htmlspecialchars(mb_strimwidth($b['deskripsi'] ?? '', 0, 60, '...')); ?></td>
                                            <td>
                                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                                    <button class="btn-small btn-view" onclick="previewModalBeritaNative(<?php echo htmlspecialchars(json_encode($b)); ?>)">Lihat</button>
                                                    <button class="btn-small btn-edit" onclick="editBeritaNative(<?php echo htmlspecialchars(json_encode($b)); ?>)">Edit</button>
                                                    <a href="dashboard-superadmin.php?hapus_berita=<?php echo $b['id']; ?>" class="btn-small btn-delete" onclick="confirmHapus(event, 'Hapus Berita', 'Yakin ingin menghapus berita \'<?php echo addslashes($b['judul']); ?>\'?')">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Detail Berita Native -->
                <div id="modalDetailBeritaNative" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);">
                    <div style="background:#fff;margin:5% auto;border-radius:20px;width:90%;max-width:680px;box-shadow:0 25px 60px rgba(0,0,0,0.35);overflow:hidden;position:relative;animation:fadeInModal .25s ease;">
                        <img id="modalBeritaFotoNative" src="" alt="Foto" style="width:100%;height:260px;object-fit:cover;display:block;">
                        <span onclick="document.getElementById('modalDetailBeritaNative').style.display='none'" style="position:absolute;top:1rem;right:1rem;background:rgba(0,0,0,.5);color:#fff;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;cursor:pointer;">&times;</span>
                        <div style="padding:2rem;">
                            <div style="font-size:.85rem;color:#10b981;font-weight:600;margin-bottom:.5rem;"> <span id="modalBeritaTanggalNative"></span></div>
                            <h2 id="modalBeritaJudulNative" style="font-size:1.4rem;color:#1f2937;margin-bottom:1rem;line-height:1.4;"></h2>
                            <p id="modalBeritaDeskripsiNative" style="color:#4b5563;line-height:1.7;white-space:pre-wrap;"></p>
                        </div>
                    </div>
                </div>
            </div>
<!-- ===================== KELOLA GALERI TAB ===================== -->
            <div id="galeri" class="tab-content">
                <div class="page-header">
                    <h1> Kelola Galeri</h1>
                    <button class="btn-primary" onclick="bukaFormGaleriNative('tambah')" id="btnTambahGaleri">+ Tambah Foto</button>
                </div>

                <?php if (isset($_GET['notif_galeri'])): ?>
                    <div style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.4rem;"></span> Operasi galeri berhasil.
                    </div>
                <?php endif; ?>

                <div id="formGaleriWrapNative" style="display:none; margin-bottom: 2rem;">
                    <div class="section-card" style="border-left:4px solid #3b82f6;">
                        <h2 id="formGaleriTitleNative" style="margin-bottom:1.5rem;">Tambah Foto Baru</h2>
                        <form action="dashboard-superadmin.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action_galeri" id="galeriActionNative" value="create">
                            <input type="hidden" name="id_galeri" id="galeriIdNative" value="">
                            <input type="hidden" name="foto_lama" id="galeriFotoLamaNative" value="">
                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Judul <span style="color:red;">*</span></label>
                                    <input type="text" name="judul_galeri" id="galeriJudulNative" class="form-control" placeholder="Judul foto/kegiatan..." required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Upload Foto</label>
                                    <input type="file" name="foto_galeri" id="galeriFotoNative" accept="image/*" style="width:100%;padding:.6rem;border:1.5px dashed #d1d5db;border-radius:8px;">
                                    <small style="display:block;margin-top:.5rem;color:#6b7280;" id="infoFotoLama"></small>
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Kategori <small style="color:#6b7280;">(Opsional)</small></label>
                                    <input type="text" name="kategori_galeri" id="galeriKeteranganNative" class="form-control" placeholder="Kegiatan, Ekstrakurikuler, Fasilitas, dll." style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Deskripsi Detail <small style="color:#6b7280;">(Opsional)</small></label>
                                    <textarea name="deskripsi_galeri" id="galeriDeskripsiNative" class="form-control" rows="3" placeholder="Deskripsi foto..." style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;resize:vertical;"></textarea>
                                </div>
                            </div>
                            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                                <button type="submit" class="btn-primary"> Simpan Foto</button>
                                <button type="button" class="btn-secondary" onclick="document.getElementById('formGaleriWrapNative').style.display='none'">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="section-card">
                    <h2>Koleksi Foto</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_galeri)): ?>
                                    <tr><td colspan="6" style="text-align:center;padding:2rem;color:#9ca3af;"> Belum ada foto di galeri.</td></tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($daftar_galeri as $g): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars(!empty($g['foto']) ? $g['foto'] : 'https://via.placeholder.com/60x40?text=No+Img'); ?>" onerror="this.src='https://via.placeholder.com/60x40?text=No+Img'" style="width:60px;height:40px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="previewModalGaleriNative(<?php echo htmlspecialchars(json_encode($g)); ?>)">
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($g['judul']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($g['kategori'] ?? '—'); ?></td>
                                            <td style="max-width:200px;"><?php echo htmlspecialchars(mb_strimwidth($g['deskripsi'] ?? '', 0, 50, '...')); ?></td>
                                            <td>
                                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                                    <button class="btn-small btn-view" onclick="previewModalGaleriNative(<?php echo htmlspecialchars(json_encode($g)); ?>)">Lihat</button>
                                                    <button class="btn-small btn-edit" onclick="editGaleriNative(<?php echo htmlspecialchars(json_encode($g)); ?>)">Edit</button>
                                                    <a href="dashboard-superadmin.php?hapus_galeri=<?php echo $g['id']; ?>" class="btn-small btn-delete" onclick="confirmHapus(event, 'Hapus Foto', 'Yakin ingin menghapus foto \'<?php echo addslashes($g['judul']); ?>\'?')">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Detail Galeri Native -->
                <div id="modalDetailGaleriNative" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.8);backdrop-filter:blur(4px);">
                    <div style="background:#fff;margin:5% auto;border-radius:12px;width:90%;max-width:800px;box-shadow:0 25px 60px rgba(0,0,0,0.5);overflow:hidden;position:relative;animation:fadeInModal .25s ease;">
                        <span onclick="document.getElementById('modalDetailGaleriNative').style.display='none'" style="position:absolute;top:1rem;right:1rem;background:rgba(255,255,255,.2);color:#fff;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;cursor:pointer;z-index:2;">&times;</span>
                        <img id="modalGaleriFotoNative" src="" alt="Foto" style="width:100%;max-height:500px;object-fit:contain;background:#111;display:block;">
                        <div style="padding:1.5rem 2rem;">
                            <h2 id="modalGaleriJudulNative" style="font-size:1.5rem;color:#1f2937;margin-bottom:.5rem;"></h2>
                            <p id="modalGaleriKeteranganNative" style="font-size:.9rem;color:#6b7280;font-weight:600;margin-bottom:1rem;"></p>
                            <p id="modalGaleriDeskripsiNative" style="color:#4b5563;line-height:1.6;"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== KELOLA EKSKUL TAB ===================== -->
            <div id="ekskul" class="tab-content">
                <div class="page-header">
                    <h1> Kelola Ekstrakurikuler</h1>
                    <button class="btn-primary" onclick="bukaFormEkskulNative('tambah')" id="btnTambahEkskul">+ Tambah Ekskul</button>
                </div>

                <?php if (isset($_GET['notif_ekskul'])): ?>
                    <div style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.4rem;"></span> Operasi ekstrakurikuler berhasil.
                    </div>
                <?php endif; ?>

                <div id="formEkskulWrapNative" style="display:none; margin-bottom: 2rem;">
                    <div class="section-card" style="border-left:4px solid #f59e0b;">
                        <h2 id="formEkskulTitleNative" style="margin-bottom:1.5rem;">Tambah Ekskul Baru</h2>
                        <form action="dashboard-superadmin.php" method="POST">
                            <input type="hidden" name="action_ekskul" id="ekskulActionNative" value="create">
                            <input type="hidden" name="id_ekskul" id="ekskulIdNative" value="">
                            
                            <div style="display:grid;grid-template-columns:1fr;gap:1.5rem;">
                                <div class="form-group">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Nama Ekskul <span style="color:red;">*</span></label>
                                    <input type="text" name="nama_ekskul" id="ekskulNamaNative" class="form-control" placeholder="Nama ekstrakurikuler..." required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                </div>

                                <div class="form-group">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Deskripsi <span style="color:red;">*</span></label>
                                    <textarea name="deskripsi_ekskul" id="ekskulDeskripsiNative" class="form-control" rows="4" placeholder="Deskripsi ekskul..." required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;resize:vertical;"></textarea>
                                </div>
                            </div>
                            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                                <button type="submit" class="btn-primary"> Simpan Ekskul</button>
                                <button type="button" class="btn-secondary" onclick="document.getElementById('formEkskulWrapNative').style.display='none'">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="section-card">
                    <h2>Daftar Ekstrakurikuler</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_ekskul)): ?>
                                    <tr><td colspan="5" style="text-align:center;padding:2rem;color:#9ca3af;"> Belum ada ekskul.</td></tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($daftar_ekskul as $e): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>

                                            <td><strong><?php echo htmlspecialchars($e['nama']); ?></strong></td>
                                            <td style="max-width:300px;"><?php echo htmlspecialchars(mb_strimwidth($e['deskripsi'] ?? '', 0, 80, '...')); ?></td>
                                            <td>
                                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                                    <button class="btn-small btn-edit" onclick="editEkskulNative(<?php echo htmlspecialchars(json_encode($e)); ?>)">Edit</button>
                                                    <a href="dashboard-superadmin.php?hapus_ekskul=<?php echo $e['id']; ?>" class="btn-small btn-delete" onclick="confirmHapus(event, 'Hapus Ekskul', 'Yakin ingin menghapus ekskul \'<?php echo addslashes($e['nama']); ?>\'?')">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Statistik Tab -->
            <div id="statistik" class="tab-content">
                <div class="page-header">
                    <h1> Kelola Statistik Sekolah</h1>
                    <p>Edit jumlah data statistik yang ditampilkan di halaman depan</p>
                </div>

                <?php if (isset($_GET['notif_statistik']) && $_GET['notif_statistik'] === 'berhasil'): ?>
                    <div style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.4rem;"></span> Statistik sekolah berhasil diperbarui.
                    </div>
                <?php endif; ?>

                <div class="section-card" style="border-left:4px solid #10b981;">
                    <form action="dashboard-superadmin.php" method="POST">
                        <input type="hidden" name="action_statistik" value="update">
                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                            <div class="form-group">
                                <label style="font-weight:600;display:block;margin-bottom:.5rem;">Siswa Aktif <span style="color:red;">*</span></label>
                                <input type="text" name="siswa" class="form-control" value="<?php echo htmlspecialchars($statistik_sekolah['siswa']); ?>" required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight:600;display:block;margin-bottom:.5rem;">Guru Berkualitas <span style="color:red;">*</span></label>
                                <input type="text" name="guru" class="form-control" value="<?php echo htmlspecialchars($statistik_sekolah['guru']); ?>" required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight:600;display:block;margin-bottom:.5rem;">Prestasi <span style="color:red;">*</span></label>
                                <input type="text" name="prestasi" class="form-control" value="<?php echo htmlspecialchars($statistik_sekolah['prestasi']); ?>" required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight:600;display:block;margin-bottom:.5rem;">Rombongan Belajar <span style="color:red;">*</span></label>
                                <input type="text" name="rombongan_belajar" class="form-control" value="<?php echo htmlspecialchars($statistik_sekolah['rombongan belajar'] ?? ''); ?>" required style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                            </div>
                        </div>
                        <div style="margin-top:1.5rem;">
                            <button type="submit" class="btn-primary"> Simpan Statistik</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Messages Tab -->
            <div id="messages" class="tab-content">
                <div class="page-header">
                    <h1>Pesan Masuk</h1>
                    <p>Total <strong><?php echo $total_pesan; ?></strong> pesan dari pengunjung website</p>
                </div>

                <?php if (isset($_GET['notif']) && $_GET['notif'] === 'hapus_berhasil'): ?>
                    <div
                        style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;border:1px solid #6ee7b7;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.4rem;"></span> Pesan berhasil dihapus.
                    </div>
                <?php endif; ?>

                <div class="section-card">
                    <h2>Daftar Pesan Pengunjung</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pengirim</th>
                                    <th>Email</th>
                                    <th>Subjek / Judul</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_pesan)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;padding:2rem;color:#9ca3af;">
                                             Belum ada pesan masuk.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1;
                                    foreach ($daftar_pesan as $pesan): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><strong><?php echo htmlspecialchars($pesan['username']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($pesan['email']); ?></td>
                                            <td><?php echo htmlspecialchars($pesan['judul']); ?></td>
                                            <td style="display:flex;gap:6px;flex-wrap:wrap;">
                                                <button class="btn-small btn-view" onclick="lihatPesan(
                                                '<?php echo addslashes(htmlspecialchars($pesan['username'])); ?>',
                                                '<?php echo addslashes(htmlspecialchars($pesan['email'])); ?>',
                                                '<?php echo addslashes(htmlspecialchars($pesan['judul'])); ?>',
                                                '<?php echo addslashes(htmlspecialchars($pesan['deskripsi'])); ?>'
                                            )">
                                                     Lihat Pesan
                                                </button>
                                                <a href="dashboard-superadmin.php?hapus_pesan=<?php echo $pesan['id']; ?>&tab=messages"
                                                    onclick="return confirm('Yakin ingin menghapus pesan dari <?php echo addslashes(htmlspecialchars($pesan['username'])); ?>?')"
                                                    class="btn-small btn-delete"
                                                    style="text-decoration:none;display:inline-flex;align-items:center;">
                                                     Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>


    <!-- Modal Lihat Pesan -->
    <div id="modalLihatPesan"
        style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);">
        <div
            style="background:#fff;margin:6% auto;padding:2rem 2.5rem;border-radius:16px;width:90%;max-width:600px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;animation:fadeInModal .25s ease;">
            <div
                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;border-bottom:2px solid #f3f4f6;padding-bottom:1rem;">
                <h2 style="color:#1f2937;margin:0;font-size:1.3rem;"> Detail Pesan</h2>
                <span onclick="tutupModalPesan()"
                    style="font-size:2rem;font-weight:bold;cursor:pointer;color:#9ca3af;line-height:1;">&times;</span>
            </div>
            <div style="display:grid;gap:1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div style="background:#f9fafb;padding:1rem;border-radius:10px;border:1px solid #e5e7eb;">
                        <p style="font-size:0.75rem;color:#6b7280;margin:0 0 4px;"> Nama Pengirim</p>
                        <p id="modalNama" style="font-weight:700;color:#1f2937;margin:0;font-size:1rem;"></p>
                    </div>
                    <div style="background:#f9fafb;padding:1rem;border-radius:10px;border:1px solid #e5e7eb;">
                        <p style="font-size:0.75rem;color:#6b7280;margin:0 0 4px;"> Email</p>
                        <p id="modalEmail"
                            style="font-weight:600;color:#2563eb;margin:0;font-size:0.9rem;word-break:break-all;"></p>
                    </div>
                </div>
                <div style="background:#f9fafb;padding:1rem;border-radius:10px;border:1px solid #e5e7eb;">
                    <p style="font-size:0.75rem;color:#6b7280;margin:0 0 4px;"> Subjek / Judul</p>
                    <p id="modalJudul" style="font-weight:700;color:#1f2937;margin:0;font-size:1rem;"></p>
                </div>
                <div style="background:#fffbeb;padding:1.25rem;border-radius:10px;border:1px solid #fcd34d;">
                    <p style="font-size:0.75rem;color:#92400e;margin:0 0 8px;font-weight:600;"> Isi Pesan</p>
                    <p id="modalDeskripsi" style="color:#1f2937;margin:0;line-height:1.7;white-space:pre-wrap;"></p>
                </div>
            </div>
            <div style="margin-top:1.5rem;text-align:right;">
                <button onclick="tutupModalPesan()"
                    style="background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;padding:.7rem 1.8rem;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;">Tutup</button>
            </div>
        </div>
    </div>
    <style>
        @keyframes fadeInModal {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script src="dashboard.js"></script>
    <script>
        // ======== MODAL HELPERS ========
        function openModal(id) { document.getElementById(id).style.display = 'block'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        function openBeritaModal() { showTab('berita'); }

        function lihatPesan(nama, email, judul, deskripsi) {
            document.getElementById('modalNama').textContent = nama;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalJudul').textContent = judul;
            document.getElementById('modalDeskripsi').textContent = deskripsi;
            document.getElementById('modalLihatPesan').style.display = 'block';
        }
        function tutupModalPesan() { document.getElementById('modalLihatPesan').style.display = 'none'; }

        window.onclick = function (event) {
            if (event.target.id === 'modalLihatPesan') tutupModalPesan();
            if (event.target.id === 'modalDetailBeritaNative') event.target.style.display = 'none';
        };

        // ======== BERITA CRUD NATIVE ========
        function bukaFormBeritaNative(mode) {
            document.getElementById('formBeritaWrapNative').style.display = 'block';
            if(mode === 'tambah') {
                document.getElementById('formBeritaTitleNative').textContent = 'Tambah Berita Baru';
                document.getElementById('beritaActionNative').value = 'create';
                document.getElementById('beritaIdNative').value = '';
                document.getElementById('beritaFotoLamaNative').value = '';
                document.getElementById('beritaJudulNative').value = '';
                document.getElementById('beritaKategoriNative').value = '';
                document.getElementById('beritaTanggalNative').value = new Date().toISOString().split('T')[0];
                document.getElementById('beritaDeskripsiNative').value = '';
                document.getElementById('infoFotoLamaBerita').innerHTML = '';
            }
            document.getElementById('formBeritaWrapNative').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function editBeritaNative(b) {
            document.getElementById('formBeritaWrapNative').style.display = 'block';
            document.getElementById('formBeritaTitleNative').textContent = 'Edit Berita';
            document.getElementById('beritaActionNative').value = 'update';
            document.getElementById('beritaIdNative').value = b.id;
            document.getElementById('beritaFotoLamaNative').value = b.foto || '';
            document.getElementById('beritaJudulNative').value = b.judul;
            document.getElementById('beritaKategoriNative').value = b.kategori || '';
            document.getElementById('beritaTanggalNative').value = b.tanggal;
            document.getElementById('beritaDeskripsiNative').value = b.deskripsi || '';
            
            if(b.foto) {
                document.getElementById('infoFotoLamaBerita').innerHTML = 'Foto saat ini: <b>' + b.foto + '</b>';
            } else {
                document.getElementById('infoFotoLamaBerita').innerHTML = '';
            }
            document.getElementById('formBeritaWrapNative').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function previewModalBeritaNative(b) {
            document.getElementById('modalBeritaFotoNative').src = b.foto || 'https://via.placeholder.com/600x400?text=No+Image';
            document.getElementById('modalBeritaTanggalNative').textContent = new Date(b.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
            document.getElementById('modalBeritaJudulNative').textContent = b.judul;
            document.getElementById('modalBeritaDeskripsiNative').textContent = b.deskripsi;
            document.getElementById('modalDetailBeritaNative').style.display = 'block';
        }
// ======== GALERI CRUD NATIVE ========
        function bukaFormGaleriNative(mode) {
            document.getElementById('formGaleriWrapNative').style.display = 'block';
            if(mode === 'tambah') {
                document.getElementById('formGaleriTitleNative').textContent = 'Tambah Foto Baru';
                document.getElementById('galeriActionNative').value = 'create';
                document.getElementById('galeriIdNative').value = '';
                document.getElementById('galeriFotoLamaNative').value = '';
                document.getElementById('galeriJudulNative').value = '';
                document.getElementById('galeriKeteranganNative').value = '';
                document.getElementById('galeriDeskripsiNative').value = '';
                document.getElementById('infoFotoLama').innerHTML = '';
            }
            document.getElementById('formGaleriWrapNative').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function editGaleriNative(g) {
            document.getElementById('formGaleriWrapNative').style.display = 'block';
            document.getElementById('formGaleriTitleNative').textContent = 'Edit Foto Galeri';
            document.getElementById('galeriActionNative').value = 'update';
            document.getElementById('galeriIdNative').value = g.id;
            document.getElementById('galeriFotoLamaNative').value = g.foto || '';
            document.getElementById('galeriJudulNative').value = g.judul;
            document.getElementById('galeriKeteranganNative').value = g.kategori || '';
            document.getElementById('galeriDeskripsiNative').value = g.deskripsi || '';
            
            if(g.foto) {
                document.getElementById('infoFotoLama').innerHTML = 'Foto saat ini tersimpan: <b>' + g.foto + '</b>. <br>Biarkan kosong jika tidak ingin ganti foto baru.';
            } else {
                document.getElementById('infoFotoLama').innerHTML = '';
            }
            document.getElementById('formGaleriWrapNative').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function previewModalGaleriNative(g) {
            document.getElementById('modalGaleriFotoNative').src = g.foto || 'https://via.placeholder.com/800x500?text=No+Image';
            document.getElementById('modalGaleriJudulNative').textContent = g.judul;
            document.getElementById('modalGaleriKeteranganNative').textContent = g.kategori ? 'Kategori: ' + g.kategori : '';
            document.getElementById('modalGaleriDeskripsiNative').textContent = g.deskripsi || '';
            document.getElementById('modalDetailGaleriNative').style.display = 'block';
        }

        // ======== EKSKUL CRUD NATIVE ========
        function bukaFormEkskulNative(mode) {
            document.getElementById('formEkskulWrapNative').style.display = 'block';
            if(mode === 'tambah') {
                document.getElementById('formEkskulTitleNative').textContent = 'Tambah Ekskul Baru';
                document.getElementById('ekskulActionNative').value = 'create';
                document.getElementById('ekskulIdNative').value = '';
                document.getElementById('ekskulNamaNative').value = '';
                document.getElementById('ekskulDeskripsiNative').value = '';
            }
            document.getElementById('formEkskulWrapNative').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function editEkskulNative(e) {
            document.getElementById('formEkskulWrapNative').style.display = 'block';
            document.getElementById('formEkskulTitleNative').textContent = 'Edit Ekskul';
            document.getElementById('ekskulActionNative').value = 'update';
            document.getElementById('ekskulIdNative').value = e.id;
            document.getElementById('ekskulNamaNative').value = e.nama;
            document.getElementById('ekskulDeskripsiNative').value = e.deskripsi || '';
            document.getElementById('formEkskulWrapNative').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // JS Click Listener 
        // Note: Removed the old "fetch" listeners for galeri
        // Attach click listener for Galeri sidebar
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[onclick*="showTab(\'galeri\')"]').forEach(el => {
                // Native Galeri uses PHP array rendering automatically
            });
            // Also keep Berita listener
            document.querySelectorAll('[onclick*="showTab(\'berita\')"]').forEach(el => {
                // Native Berita uses PHP array rendering automatically
            });
        });

        // Pastikan close modal ditambahkan event targetnya
        const __oldOnClick = window.onclick;
        window.onclick = function (event) {
            if (__oldOnClick) __oldOnClick(event);
            if (event.target.id === 'modalDetailGaleriNative') event.target.style.display = 'none';
            if (event.target.id === 'modalDetailBeritaNative') event.target.style.display = 'none';
        }
    </script>
</body>

</html>