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
    echo "<script>localStorage.setItem('activeTabDashboard', 'galeri'); window.location.href='dashboard-superadmin.php?notif_galeri=hapus_berhasil';</script>";
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
    echo "<script>localStorage.setItem('activeTabDashboard', 'galeri'); window.location.href='dashboard-superadmin.php?notif_galeri=$msg';</script>";
    exit();
}

$res_galeri = mysqli_query($KONEKSI, "SELECT * FROM galery ORDER BY id DESC");
$daftar_galeri = [];
while ($row = mysqli_fetch_assoc($res_galeri)) {
    $daftar_galeri[] = $row;
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
                    <span class="icon">📰</span>
                    <span>Kelola Berita</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('galeri')">
                    <span class="icon">🖼️</span>
                    <span>Kelola Galeri</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('messages')">
                    <span class="icon"></span>
                    <span>Pesan Masuk</span>
                </a>
                <a href="#" class="menu-item" onclick="showTab('settings')">
                    <span class="icon"></span>
                    <span>Pengaturan</span>
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
                            <h3 style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1;">1,250</h3>
                        </div>
                        <div style="opacity: 0.8; display: flex; align-items: center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 1.5rem; border-radius: 20px; color: #fff; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: space-between; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div>
                            <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.2rem; font-weight: 500;">Total Guru</p>
                            <h3 style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1;">75</h3>
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
                            <h3 style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1;">150</h3>
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
                        <h3>Profil Sekolah</h3>
                        <p>Edit informasi profil sekolah</p>
                        <button class="btn-primary" onclick="openProfilModal()">Edit</button>
                    </div>
                    <div class="content-card">
                        <h3>Visi & Misi</h3>
                        <p>Edit visi dan misi sekolah</p>
                        <button class="btn-primary" onclick="openVisiMisiModal()">Edit</button>
                    </div>
                    <div class="content-card">
                        <h3>Fasilitas</h3>
                        <p>Kelola fasilitas sekolah</p>
                        <button class="btn-primary" onclick="openFasilitasModal()">Edit</button>
                    </div>
                    <div class="content-card">
                        <h3>Ekstrakulikuler</h3>
                        <p>Kelola ekstrakurikuler</p>
                        <button class="btn-primary" onclick="openEkskulModal()">Edit</button>
                    </div>
                    <div class="content-card">
                        <h3>Lokasi</h3>
                        <p>Edit alamat dan peta</p>
                        <button class="btn-primary" onclick="openLokasiModal()">Edit</button>
                    </div>
                    <div class="content-card">
                        <h3>Kontak</h3>
                        <p>Edit informasi kontak</p>
                        <button class="btn-primary" onclick="openKontakModal()">Edit</button>
                    </div>
                    <div class="content-card">
                        <h3>Berita Sekolah</h3>
                        <p>Kelola berita dan update artikel</p>
                        <button class="btn-primary" onclick="openBeritaModal()">Edit Berita</button>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content">
                <div class="page-header">
                    <h1>Pengaturan</h1>
                    <p>Konfigurasi sistem</p>
                </div>

                <div class="section-card">
                    <h2>Pengaturan Website</h2>
                    <div class="form-group">
                        <label>Nama Sekolah</label>
                        <input type="text" class="form-control" value="SMA NEGERI PRESTASI">
                    </div>
                    <div class="form-group">
                        <label>Email Sekolah</label>
                        <input type="email" class="form-control" value="info@sekolahkita.sch.id">
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" class="form-control" value="021-1234-5678">
                    </div>
                    <button class="btn-primary">Simpan Perubahan</button>
                </div>

                <div class="section-card" style="margin-top: 2rem;">
                    <h2>Pengaturan Keamanan</h2>
                    <div class="settings-item">
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                        <div>
                            <strong>Two-Factor Authentication</strong>
                            <p>Aktifkan autentikasi dua faktor</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ===================== KELOLA BERITA TAB ===================== -->
            <div id="berita" class="tab-content">
                <div class="page-header">
                    <h1>📰 Kelola Berita</h1>
                    <button class="btn-primary" onclick="bukaFormBerita('tambah')" id="btnTambahBerita">+ Tambah
                        Berita</button>
                </div>

                <!-- Notifikasi -->
                <div id="beritaNotif"
                    style="display:none;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;border:1px solid;display:flex;align-items:center;gap:.75rem;">
                </div>

                <!-- Form Tambah/Edit Berita -->
                <div id="formBeritaWrap" style="display:none;">
                    <div class="section-card" style="border-left:4px solid #10b981;">
                        <h2 id="formBeritaTitle" style="margin-bottom:1.5rem;">Tambah Berita Baru</h2>
                        <form id="formBerita" enctype="multipart/form-data">
                            <input type="hidden" id="beritaId" value="">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Judul Berita <span
                                            style="color:red;">*</span></label>
                                    <input type="text" id="beritaJudul" class="form-control"
                                        placeholder="Judul berita..." required
                                        style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                </div>
                                <div class="form-group">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Kategori <span
                                            style="color:red;">*</span></label>
                                    <input type="text" id="beritaKategori" class="form-control" required list="listKategoriBerita"
                                        placeholder="Ketik atau pilih kategori..."
                                        style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                    <datalist id="listKategoriBerita">
                                        <option value="pengumuman">
                                        <option value="prestasi">
                                        <option value="pilihan utama">
                                    </datalist>
                                </div>
                                <div class="form-group">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Tanggal <span
                                            style="color:red;">*</span></label>
                                    <input type="date" id="beritaTanggal" class="form-control" required
                                        style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Foto
                                        Berita</label>
                                    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                                        <input type="file" id="beritaFotoFile" accept="image/*"
                                            style="flex:1;padding:.6rem;border:1.5px dashed #d1d5db;border-radius:8px;"
                                            onchange="previewFoto(this)">
                                        <span style="color:#6b7280;font-size:.85rem;">atau</span>
                                        <input type="text" id="beritaFotoUrl" class="form-control"
                                            placeholder="Tempel URL gambar..."
                                            style="flex:2;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;">
                                    </div>
                                    <div id="fotoPreviewWrap" style="margin-top:.75rem;display:none;">
                                        <img id="fotoPreview" src="" alt="Preview"
                                            style="max-height:160px;border-radius:10px;object-fit:cover;border:2px solid #e5e7eb;">
                                        <button type="button" onclick="hapusFotoPreview()"
                                            style="margin-left:.75rem;background:#ef4444;color:#fff;border:none;padding:.35rem .8rem;border-radius:6px;cursor:pointer;font-size:.85rem;">✕
                                            Hapus</button>
                                    </div>
                                    <small style="color:#6b7280;margin-top:.4rem;display:block;">Format: JPG, PNG, WebP.
                                        Maks 3 MB. Atau tempel URL eksternal.</small>
                                </div>
                                <div class="form-group" style="grid-column:1/-1;">
                                    <label style="font-weight:600;display:block;margin-bottom:.5rem;">Deskripsi Singkat
                                        <span style="color:red;">*</span></label>
                                    <textarea id="beritaDeskripsi" class="form-control" rows="4"
                                        placeholder="Deskripsi singkat berita (maks 500 karakter)..." maxlength="500"
                                        required
                                        style="width:100%;padding:.75rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:1rem;resize:vertical;"></textarea>
                                    <small id="charCount" style="color:#6b7280;">0 / 500 karakter</small>
                                </div>
                            </div>
                            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                                <button type="submit" class="btn-primary" id="btnSimpanBerita">💾 Simpan Berita</button>
                                <button type="button" class="btn-secondary" onclick="tutupFormBerita()">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Berita -->
                <div class="section-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                        <h2>Daftar Berita</h2>
                        <div style="display:flex;gap:.5rem;">
                            <select id="filterKategori" onchange="muatBerita()"
                                style="padding:.5rem .75rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:.9rem;">
                                <option value="">Semua Kategori</option>
                                <option value="pengumuman">Pengumuman</option>
                                <option value="prestasi">Prestasi</option>
                                <option value="pilihan utama">Pilihan Utama</option>
                            </select>
                            <button onclick="muatBerita()"
                                style="padding:.5rem 1rem;background:#f3f4f6;border:1.5px solid #d1d5db;border-radius:8px;cursor:pointer;">🔄
                                Refresh</button>
                        </div>
                    </div>
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
                            <tbody id="tabelBerita">
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:2rem;color:#9ca3af;">⏳ Memuat
                                        data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="beritaPagination" style="margin-top:1rem;display:flex;gap:.5rem;justify-content:flex-end;">
                    </div>
                </div>
            </div>

            <!-- Modal Detail/Preview Berita -->
            <div id="modalDetailBerita"
                style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);">
                <div
                    style="background:#fff;margin:5% auto;border-radius:20px;width:90%;max-width:680px;box-shadow:0 25px 60px rgba(0,0,0,0.35);overflow:hidden;animation:fadeInModal .25s ease;">
                    <div style="position:relative;">
                        <img id="modalBeritaFoto" src="" alt="Foto Berita"
                            style="width:100%;height:260px;object-fit:cover;display:block;">
                        <span onclick="document.getElementById('modalDetailBerita').style.display='none'"
                            style="position:absolute;top:1rem;right:1rem;background:rgba(0,0,0,.5);color:#fff;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;cursor:pointer;">&times;</span>
                        <div id="modalBeritaBadge"
                            style="position:absolute;bottom:1rem;left:1rem;padding:.3rem .9rem;border-radius:50px;font-size:.8rem;font-weight:700;">
                        </div>
                    </div>
                    <div style="padding:2rem;">
                        <div style="font-size:.85rem;color:#10b981;font-weight:600;margin-bottom:.5rem;">📅 <span
                                id="modalBeritaTanggal"></span></div>
                        <h2 id="modalBeritaJudul"
                            style="font-size:1.4rem;color:#1f2937;margin-bottom:1rem;line-height:1.4;"></h2>
                        <p id="modalBeritaDeskripsi" style="color:#4b5563;line-height:1.7;"></p>
                        <div style="margin-top:1.5rem;text-align:right;">
                            <button onclick="document.getElementById('modalDetailBerita').style.display='none'"
                                style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;padding:.7rem 1.8rem;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== KELOLA GALERI TAB ===================== -->
            <div id="galeri" class="tab-content">
                <div class="page-header">
                    <h1>🖼️ Kelola Galeri</h1>
                    <button class="btn-primary" onclick="bukaFormGaleriNative('tambah')" id="btnTambahGaleri">+ Tambah Foto</button>
                </div>

                <?php if (isset($_GET['notif_galeri'])): ?>
                    <div style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.4rem;">✅</span> Operasi galeri berhasil.
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
                                <button type="submit" class="btn-primary">💾 Simpan Foto</button>
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
                                    <tr><td colspan="6" style="text-align:center;padding:2rem;color:#9ca3af;">📭 Belum ada foto di galeri.</td></tr>
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
                                                    <button class="btn-small btn-view" onclick="previewModalGaleriNative(<?php echo htmlspecialchars(json_encode($g)); ?>)">👁️</button>
                                                    <button class="btn-small btn-edit" onclick="editGaleriNative(<?php echo htmlspecialchars(json_encode($g)); ?>)">✏️</button>
                                                    <a href="dashboard-superadmin.php?hapus_galeri=<?php echo $g['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Hapus foto \'<?php echo addslashes($g['judul']); ?>\'?')">🗑️</a>
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

            <!-- Messages Tab --><!-- Messages Tab -->
            <div id="messages" class="tab-content">
                <div class="page-header">
                    <h1>Pesan Masuk</h1>
                    <p>Total <strong><?php echo $total_pesan; ?></strong> pesan dari pengunjung website</p>
                </div>

                <?php if (isset($_GET['notif']) && $_GET['notif'] === 'hapus_berhasil'): ?>
                    <div
                        style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;border:1px solid #6ee7b7;display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.4rem;">✅</span> Pesan berhasil dihapus.
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
                                            📭 Belum ada pesan masuk.
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
                                                    👁️ Lihat Pesan
                                                </button>
                                                <a href="dashboard-superadmin.php?hapus_pesan=<?php echo $pesan['id']; ?>&tab=messages"
                                                    onclick="return confirm('Yakin ingin menghapus pesan dari <?php echo addslashes(htmlspecialchars($pesan['username'])); ?>?')"
                                                    class="btn-small btn-delete"
                                                    style="text-decoration:none;display:inline-flex;align-items:center;">
                                                    🗑️ Hapus
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
    <!-- Modal Profil Sekolah -->
    <div id="profilModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 5% auto; padding: 30px; border-radius: 12px; width: 80%; max-width: 700px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #1f2937;">Edit Profil Sekolah</h2>
                <span style="font-size: 28px; font-weight: bold; cursor: pointer; color: #9ca3af;"
                    onclick="closeModal('profilModal')">&times;</span>
            </div>
            <form onsubmit="event.preventDefault(); alert('Profil berhasil diperbarui!'); closeModal('profilModal');">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Nama Sekolah</label>
                        <input type="text" value="SMP IBNU AQIL"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">NPSN</label>
                        <input type="text" value="20231052"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Tahun Berdiri</label>
                        <input type="text" value="1992"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Akreditasi</label>
                        <input type="text" value="A (Amat Baik)"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Kepala Sekolah</label>
                        <input type="text" value="Ade Irawan"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Status Sekolah</label>
                        <input type="text" value="Swasta"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Tentang Kami</label>
                    <textarea
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 150px; line-height: 1.6;"
                        required>Sekolah Ibnu Aqil Bogor adalah lembaga pendidikan yang mengintegrasikan pembelajaran akademik dengan nilai-nilai keislaman. Kami berkomitmen membentuk peserta didik yang berakhlak mulia, berilmu, dan berkarakter.&#10;&#10;Dengan dukungan tenaga pendidik profesional serta lingkungan belajar yang kondusif, Sekolah Ibnu Aqil menghadirkan pendidikan yang inspiratif untuk menyiapkan generasi yang siap menghadapi tantangan masa depan.</textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-primary" style="flex: 1;">Simpan Perubahan</button>
                    <button type="button" class="btn-secondary" style="flex: 1;"
                        onclick="closeModal('profilModal')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Visi & Misi -->
    <div id="visimisiModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 5% auto; padding: 30px; border-radius: 12px; width: 80%; max-width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #1f2937;">Edit Visi & Misi</h2>
                <span style="font-size: 28px; font-weight: bold; cursor: pointer; color: #9ca3af;"
                    onclick="closeModal('visimisiModal')">&times;</span>
            </div>
            <form
                onsubmit="event.preventDefault(); alert('Visi & Misi berhasil diperbarui!'); closeModal('visimisiModal');">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Visi</label>
                    <textarea
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 80px;"
                        required>Mewujudkan generasi yang unggul dalam IPTEK dan kokoh dalam IMTAK.</textarea>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Misi</label>
                    <textarea
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 120px;"
                        required>1. Menyelenggarakan pendidikan berkualitas...&#10;2. Membentuk karakter islami...&#10;3. Mengembangkan potensi bakat dan minat siswa...</textarea>
                </div>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <!-- Modal Fasilitas -->
    <div id="fasilitasModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 5% auto; padding: 30px; border-radius: 12px; width: 80%; max-width: 700px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #1f2937;">Kelola Fasilitas</h2>
                <span style="font-size: 28px; font-weight: bold; cursor: pointer; color: #9ca3af;"
                    onclick="closeModal('fasilitasModal')">&times;</span>
            </div>
            <button class="btn-primary" style="margin-bottom: 15px;" onclick="showAddFasilitasForm()">+ Tambah Fasilitas
                Baru</button>
            <div id="fasilitasForm"
                style="display: none; background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
                <h4 style="margin-bottom: 15px;">Tambah Fasilitas Baru</h4>
                <form
                    onsubmit="event.preventDefault(); alert('Fasilitas baru berhasil ditambahkan!'); hideFasilitasForm();">
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; font-size: 14px; margin-bottom: 5px;">Nama Fasilitas</label>
                        <input type="text" placeholder="Contoh: Perpustakaan Modern"
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 14px; margin-bottom: 5px;">Deskripsi</label>
                        <textarea
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 60px;"
                            required></textarea>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-primary" style="padding: 8px 15px;">Simpan</button>
                        <button type="button" class="btn-secondary" style="padding: 8px 15px;"
                            onclick="hideFasilitasForm()">Batal</button>
                    </div>
                </form>
            </div>
            <div style="border-top: 1px solid #eee; padding-top: 15px;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 6px;">
                    <span>Ruang Kelas AC</span>
                    <button class="btn-small btn-edit"
                        style="background: #3b82f6; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Edit</button>
                </div>
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 6px;">
                    <span>Lab Komputer</span>
                    <button class="btn-small btn-edit"
                        style="background: #3b82f6; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ekstrakurikuler -->
    <div id="ekskulModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 5% auto; padding: 30px; border-radius: 12px; width: 80%; max-width: 700px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #1f2937;">Kelola Ekstrakurikuler</h2>
                <span style="font-size: 28px; font-weight: bold; cursor: pointer; color: #9ca3af;"
                    onclick="closeModal('ekskulModal')">&times;</span>
            </div>
            <button class="btn-primary" style="margin-bottom: 15px;" onclick="showAddEkskulForm()">+ Tambah Ekskul
                Baru</button>
            <div id="ekskulForm"
                style="display: none; background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
                <h4 style="margin-bottom: 15px;">Tambah Ekskul Baru</h4>
                <form
                    onsubmit="event.preventDefault(); alert('Ekstrakurikuler baru berhasil ditambahkan!'); hideEkskulForm();">
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; font-size: 14px; margin-bottom: 5px;">Nama Ekskul</label>
                        <input type="text" placeholder="Contoh: Robotik"
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; font-size: 14px; margin-bottom: 5px;">Jadwal</label>
                        <input type="text" placeholder="Contoh: Sabtu, 09:00 - 11:00"
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 14px; margin-bottom: 5px;">Deskripsi</label>
                        <textarea
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 60px;"
                            required></textarea>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-primary" style="padding: 8px 15px;">Simpan</button>
                        <button type="button" class="btn-secondary" style="padding: 8px 15px;"
                            onclick="hideEkskulForm()">Batal</button>
                    </div>
                </form>
            </div>
            <div style="border-top: 1px solid #eee; padding-top: 15px;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 6px;">
                    <span>Sepak Bola</span>
                    <button class="btn-small btn-edit"
                        style="background: #3b82f6; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Edit</button>
                </div>
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 6px;">
                    <span>Memanah</span>
                    <button class="btn-small btn-edit"
                        style="background: #3b82f6; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lokasi -->
    <div id="lokasiModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 5% auto; padding: 30px; border-radius: 12px; width: 80%; max-width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #1f2937;">Edit Lokasi</h2>
                <span style="font-size: 28px; font-weight: bold; cursor: pointer; color: #9ca3af;"
                    onclick="closeModal('lokasiModal')">&times;</span>
            </div>
            <form onsubmit="event.preventDefault(); alert('Lokasi berhasil diperbarui!'); closeModal('lokasiModal');">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Alamat Lengkap</label>
                    <textarea
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 80px;"
                        required>Jl. Pendidikan No. 45, Bogor, Jawa Barat</textarea>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Google Maps Embed URL</label>
                    <input type="text" value="https://www.google.com/maps/embed?..."
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                </div>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <!-- Modal Kontak -->
    <div id="kontakModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 5% auto; padding: 30px; border-radius: 12px; width: 80%; max-width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #1f2937;">Edit Kontak</h2>
                <span style="font-size: 28px; font-weight: bold; cursor: pointer; color: #9ca3af;"
                    onclick="closeModal('kontakModal')">&times;</span>
            </div>
            <form onsubmit="event.preventDefault(); alert('Kontak berhasil diperbarui!'); closeModal('kontakModal');">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Nomor Telepon</label>
                    <input type="text" value="021-1234-5678"
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Email Sekolah</label>
                    <input type="email" value="info@sekolahkita.sch.id"
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Instagram</label>
                    <input type="text" value="@smp_ibnu_aqil"
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <!-- (Berita modal removed - now handled in full tab) -->

    <!-- Modal Lihat Pesan -->
    <div id="modalLihatPesan"
        style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);">
        <div
            style="background:#fff;margin:6% auto;padding:2rem 2.5rem;border-radius:16px;width:90%;max-width:600px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;animation:fadeInModal .25s ease;">
            <div
                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;border-bottom:2px solid #f3f4f6;padding-bottom:1rem;">
                <h2 style="color:#1f2937;margin:0;font-size:1.3rem;">📩 Detail Pesan</h2>
                <span onclick="tutupModalPesan()"
                    style="font-size:2rem;font-weight:bold;cursor:pointer;color:#9ca3af;line-height:1;">&times;</span>
            </div>
            <div style="display:grid;gap:1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div style="background:#f9fafb;padding:1rem;border-radius:10px;border:1px solid #e5e7eb;">
                        <p style="font-size:0.75rem;color:#6b7280;margin:0 0 4px;">👤 Nama Pengirim</p>
                        <p id="modalNama" style="font-weight:700;color:#1f2937;margin:0;font-size:1rem;"></p>
                    </div>
                    <div style="background:#f9fafb;padding:1rem;border-radius:10px;border:1px solid #e5e7eb;">
                        <p style="font-size:0.75rem;color:#6b7280;margin:0 0 4px;">📧 Email</p>
                        <p id="modalEmail"
                            style="font-weight:600;color:#2563eb;margin:0;font-size:0.9rem;word-break:break-all;"></p>
                    </div>
                </div>
                <div style="background:#f9fafb;padding:1rem;border-radius:10px;border:1px solid #e5e7eb;">
                    <p style="font-size:0.75rem;color:#6b7280;margin:0 0 4px;">📌 Subjek / Judul</p>
                    <p id="modalJudul" style="font-weight:700;color:#1f2937;margin:0;font-size:1rem;"></p>
                </div>
                <div style="background:#fffbeb;padding:1.25rem;border-radius:10px;border:1px solid #fcd34d;">
                    <p style="font-size:0.75rem;color:#92400e;margin:0 0 8px;font-weight:600;">💬 Isi Pesan</p>
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
        function openProfilModal() { openModal('profilModal'); }
        function openVisiMisiModal() { openModal('visimisiModal'); }
        function openFasilitasModal() { openModal('fasilitasModal'); }
        function openEkskulModal() { openModal('ekskulModal'); }
        function openLokasiModal() { openModal('lokasiModal'); }
        function openKontakModal() { openModal('kontakModal'); }
        function openBeritaModal() { showTab('berita'); }

        function showAddFasilitasForm() { document.getElementById('fasilitasForm').style.display = 'block'; }
        function hideFasilitasForm() { document.getElementById('fasilitasForm').style.display = 'none'; }
        function showAddEkskulForm() { document.getElementById('ekskulForm').style.display = 'block'; }
        function hideEkskulForm() { document.getElementById('ekskulForm').style.display = 'none'; }

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
            if (event.target.id === 'modalDetailBerita') event.target.style.display = 'none';
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                if (event.target.id === 'fasilitasModal') hideFasilitasForm();
                if (event.target.id === 'ekskulModal') hideEkskulForm();
            }
        };

        // ======== BERITA CRUD ========
        let semuaBerita = [];
        let halamanBerita = 1;
        const perHalaman = 8;
        let uploadedFotoPath = '';

        async function muatBerita() {
            const tbody = document.getElementById('tabelBerita');
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:#9ca3af;">⏳ Memuat...</td></tr>';
            try {
                const res = await fetch('berita-api.php?action=list');
                const json = await res.json();
                if (!json.success) throw new Error(json.message);
                const filter = document.getElementById('filterKategori').value;
                semuaBerita = filter ? json.data.filter(b => b.kategori === filter) : json.data;
                halamanBerita = 1;
                renderTabelBerita();
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:#ef4444;">❌ Gagal memuat: ' + e.message + '</td></tr>';
            }
        }

        function renderTabelBerita() {
            const tbody = document.getElementById('tabelBerita');
            const start = (halamanBerita - 1) * perHalaman;
            const slice = semuaBerita.slice(start, start + perHalaman);

            if (semuaBerita.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:#9ca3af;">📭 Belum ada berita.</td></tr>';
                document.getElementById('beritaPagination').innerHTML = '';
                return;
            }

            const badgeColor = { pengumuman: '#3b82f6', prestasi: '#f59e0b', 'pilihan utama': '#10b981' };

            tbody.innerHTML = slice.map((b, i) => {
                const no = start + i + 1;
                const foto = b.foto ? `<img src="${escHtml(b.foto)}" onerror="this.src='https://via.placeholder.com/60x40?text=No+Img'" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">` : '<span style="color:#d1d5db;">—</span>';
                const kat = b.kategori;
                const bg = badgeColor[kat.toLowerCase()] || '#6b7280';
                const badge = `<span style="background:${bg};color:#fff;padding:.2rem .6rem;border-radius:50px;font-size:.75rem;font-weight:700;">${escHtml(kat)}</span>`;
                const tgl = b.tanggal ? new Date(b.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
                const desk = b.deskripsi.length > 60 ? escHtml(b.deskripsi.substring(0, 60)) + '...' : escHtml(b.deskripsi);
                return `<tr>
                    <td>${no}</td>
                    <td>${foto}</td>
                    <td><strong>${escHtml(b.judul)}</strong></td>
                    <td>${badge}</td>
                    <td>${tgl}</td>
                    <td style="max-width:200px;">${desk}</td>
                    <td style="display:flex;gap:6px;flex-wrap:wrap;">
                        <button class="btn-small btn-view" onclick="previewBerita(${b.id})">👁️ Lihat</button>
                        <button class="btn-small btn-edit" onclick="editBerita(${b.id})">✏️ Edit</button>
                        <button class="btn-small btn-delete" onclick="hapusBerita(${b.id}, '${escJs(b.judul)}')">🗑️ Hapus</button>
                    </td>
                </tr>`;
            }).join('');

            // Pagination
            const totalHal = Math.ceil(semuaBerita.length / perHalaman);
            const pag = document.getElementById('beritaPagination');
            if (totalHal <= 1) { pag.innerHTML = ''; return; }
            let html = '';
            for (let p = 1; p <= totalHal; p++) {
                const active = p === halamanBerita ? 'background:#10b981;color:#fff;' : 'background:#f3f4f6;color:#374151;';
                html += `<button onclick="halamanBerita=${p};renderTabelBerita()" style="${active}border:none;padding:.4rem .8rem;border-radius:6px;cursor:pointer;font-weight:600;">${p}</button>`;
            }
            pag.innerHTML = html;
        }

        function escHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        function escJs(str) { return String(str).replace(/'/g, "\\'"); }

        function tampilNotif(pesan, tipe) {
            const div = document.getElementById('beritaNotif');
            const isOk = tipe === 'ok';
            div.style.cssText = `display:flex;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;align-items:center;gap:.75rem;background:${isOk ? '#d1fae5' : '#fee2e2'};color:${isOk ? '#065f46' : '#991b1b'};border:1px solid ${isOk ? '#6ee7b7' : '#fca5a5'};`;
            div.innerHTML = `<span style="font-size:1.4rem;">${isOk ? '✅' : '❌'}</span> ${pesan}`;
            setTimeout(() => { div.style.display = 'none'; }, 4000);
        }

        function bukaFormBerita(mode) {
            document.getElementById('formBeritaWrap').style.display = 'block';
            document.getElementById('beritaNotif').style.display = 'none';
            if (mode === 'tambah') {
                document.getElementById('formBeritaTitle').textContent = 'Tambah Berita Baru';
                document.getElementById('beritaId').value = '';
                document.getElementById('beritaJudul').value = '';
                document.getElementById('beritaKategori').value = '';
                document.getElementById('beritaTanggal').value = new Date().toISOString().split('T')[0];
                document.getElementById('beritaDeskripsi').value = '';
                document.getElementById('beritaFotoUrl').value = '';
                document.getElementById('beritaFotoFile').value = '';
                document.getElementById('charCount').textContent = '0 / 500 karakter';
                hapusFotoPreview();
                uploadedFotoPath = '';
            }
            document.getElementById('formBeritaWrap').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function tutupFormBerita() {
            document.getElementById('formBeritaWrap').style.display = 'none';
            uploadedFotoPath = '';
        }

        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('fotoPreview').src = e.target.result;
                    document.getElementById('fotoPreviewWrap').style.display = 'block';
                    document.getElementById('beritaFotoUrl').value = '';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function hapusFotoPreview() {
            document.getElementById('fotoPreview').src = '';
            document.getElementById('fotoPreviewWrap').style.display = 'none';
            document.getElementById('beritaFotoFile').value = '';
        }

        document.getElementById('beritaDeskripsi')?.addEventListener('input', function () {
            document.getElementById('charCount').textContent = this.value.length + ' / 500 karakter';
        });

        document.getElementById('formBerita')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            const id = document.getElementById('beritaId').value;
            const judul = document.getElementById('beritaJudul').value.trim();
            const kategori = document.getElementById('beritaKategori').value;
            const tanggal = document.getElementById('beritaTanggal').value;
            const deskripsi = document.getElementById('beritaDeskripsi').value.trim();
            const fotoFile = document.getElementById('beritaFotoFile').files[0];
            let fotoVal = document.getElementById('beritaFotoUrl').value.trim();

            const btn = document.getElementById('btnSimpanBerita');
            btn.disabled = true;
            btn.textContent = '⏳ Menyimpan...';

            try {
                // Upload file jika ada
                if (fotoFile) {
                    const fd = new FormData();
                    fd.append('foto', fotoFile);
                    const up = await fetch('berita-api.php?action=upload', { method: 'POST', body: fd });
                    const upJ = await up.json();
                    if (!upJ.success) throw new Error('Upload gagal: ' + upJ.message);
                    fotoVal = upJ.filename;
                } else if (uploadedFotoPath) {
                    fotoVal = uploadedFotoPath;
                }

                const action = id ? 'update' : 'create';
                const payload = { judul, foto: fotoVal, kategori, tanggal, deskripsi };
                if (id) payload.id = parseInt(id);

                const res = await fetch('berita-api.php?action=' + action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!json.success) throw new Error(json.message);

                tampilNotif(json.message, 'ok');
                tutupFormBerita();
                muatBerita();
            } catch (err) {
                tampilNotif('Gagal: ' + err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '💾 Simpan Berita';
            }
        });

        async function editBerita(id) {
            try {
                const res = await fetch('berita-api.php?action=get&id=' + id);
                const json = await res.json();
                if (!json.success) throw new Error(json.message);
                const b = json.data;
                bukaFormBerita('edit');
                document.getElementById('formBeritaTitle').textContent = 'Edit Berita';
                document.getElementById('beritaId').value = b.id;
                document.getElementById('beritaJudul').value = b.judul;
                document.getElementById('beritaKategori').value = b.kategori;
                document.getElementById('beritaTanggal').value = b.tanggal;
                document.getElementById('beritaDeskripsi').value = b.deskripsi;
                document.getElementById('charCount').textContent = b.deskripsi.length + ' / 500 karakter';
                document.getElementById('beritaFotoUrl').value = b.foto || '';
                if (b.foto) {
                    document.getElementById('fotoPreview').src = b.foto;
                    document.getElementById('fotoPreviewWrap').style.display = 'block';
                } else {
                    hapusFotoPreview();
                }
                uploadedFotoPath = '';
            } catch (err) {
                tampilNotif('Gagal memuat data: ' + err.message, 'error');
            }
        }

        async function hapusBerita(id, judul) {
            if (!confirm('Yakin ingin menghapus berita:\n"' + judul + '"?')) return;
            try {
                const res = await fetch('berita-api.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const json = await res.json();
                if (!json.success) throw new Error(json.message);
                tampilNotif(json.message, 'ok');
                muatBerita();
            } catch (err) {
                tampilNotif('Gagal menghapus: ' + err.message, 'error');
            }
        }

        function previewBerita(id) {
            const b = semuaBerita.find(x => x.id == id);
            if (!b) return;
            const badgeColor = { pengumuman: '#3b82f6', prestasi: '#f59e0b', 'pilihan utama': '#10b981' };
            const bg = badgeColor[b.kategori] || '#6b7280';
            const tgl = b.tanggal ? new Date(b.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '—';
            document.getElementById('modalBeritaFoto').src = b.foto || 'https://via.placeholder.com/680x260?text=No+Image';
            document.getElementById('modalBeritaJudul').textContent = b.judul;
            document.getElementById('modalBeritaTanggal').textContent = tgl;
            document.getElementById('modalBeritaDeskripsi').textContent = b.deskripsi;
            const badge = document.getElementById('modalBeritaBadge');
            badge.textContent = b.kategori.toUpperCase();
            badge.style.cssText = `position:absolute;bottom:1rem;left:1rem;background:${bg};color:#fff;padding:.3rem .9rem;border-radius:50px;font-size:.8rem;font-weight:700;`;
            document.getElementById('modalDetailBerita').style.display = 'block';
        }

        // Muat berita saat tab berita pertama kali dibuka
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

        // JS Click Listener 
        // Note: Removed the old "fetch" listeners for galeri
        // Attach click listener for Galeri sidebar
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[onclick*="showTab(\'galeri\')"]').forEach(el => {
                // Native Galeri uses PHP array rendering automatically
            });
            // Also keep Berita listener
            document.querySelectorAll('[onclick*="showTab(\'berita\')"]').forEach(el => {
                el.addEventListener('click', () => setTimeout(muatBerita, 100));
            });
        });

        // Pastikan close modal ditambahkan event targetnya
        const __oldOnClick = window.onclick;
        window.onclick = function (event) {
            if (__oldOnClick) __oldOnClick(event);
            if (event.target.id === 'modalDetailGaleriNative') event.target.style.display = 'none';
        }
    </script>
</body>

</html>