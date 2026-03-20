<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');
require_once 'koneksi.php';

$action = $_GET['action'] ?? '';

// ===================== UPLOAD FOTO =====================
if ($action === 'upload') {
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada file atau terjadi error upload.']);
        exit();
    }

    $uploadDir = 'uploads/galery/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['foto'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Format file tidak didukung.']);
        exit();
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5 MB.']);
        exit();
    }

    $filename = uniqid('galery_', true) . '.' . $ext;
    $dest = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['success' => false, 'message' => 'Gagal memindahkan file.']);
        exit();
    }

    echo json_encode(['success' => true, 'filename' => $dest]);
    exit();
}

// ===================== LIST =====================
if ($action === 'list') {
    $sql = "SELECT * FROM galery ORDER BY id DESC";
    $result = mysqli_query($KONEKSI, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}

// ===================== GET SINGLE =====================
if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = mysqli_prepare($KONEKSI, "SELECT * FROM galery WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if ($row) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan.']);
    }
    exit();
}

// ===================== CREATE =====================
if ($action === 'create') {
    $body = json_decode(file_get_contents('php://input'), true);
    $judul = trim($body['judul'] ?? '');
    $keterangan = trim($body['keterangan'] ?? '');
    $deskripsi = trim($body['deskripsi'] ?? '');
    $foto = trim($body['foto'] ?? '');

    if (!$judul) {
        echo json_encode(['success' => false, 'message' => 'Judul wajib diisi.']);
        exit();
    }

    $stmt = mysqli_prepare($KONEKSI, "INSERT INTO galery (foto, keterangan, judul, deskripsi) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $foto, $keterangan, $judul, $deskripsi);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        echo json_encode(['success' => true, 'message' => 'Foto berhasil ditambahkan ke galeri.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($KONEKSI)]);
    }
    exit();
}

// ===================== UPDATE =====================
if ($action === 'update') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id = intval($body['id'] ?? 0);
    $judul = trim($body['judul'] ?? '');
    $keterangan = trim($body['keterangan'] ?? '');
    $deskripsi = trim($body['deskripsi'] ?? '');
    $foto = trim($body['foto'] ?? '');

    if (!$id || !$judul) {
        echo json_encode(['success' => false, 'message' => 'ID dan Judul wajib diisi.']);
        exit();
    }

    $stmt = mysqli_prepare($KONEKSI, "UPDATE galery SET foto=?, keterangan=?, judul=?, deskripsi=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssssi', $foto, $keterangan, $judul, $deskripsi, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        echo json_encode(['success' => true, 'message' => 'Foto galeri berhasil diperbarui.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui: ' . mysqli_error($KONEKSI)]);
    }
    exit();
}

// ===================== DELETE =====================
if ($action === 'delete') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id = intval($body['id'] ?? 0);

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
        exit();
    }

    // Ambil nama file foto dulu untuk dihapus dari server
    $stmt = mysqli_prepare($KONEKSI, "SELECT foto FROM galery WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    // Hapus foto dari disk jika ada dan bukan URL eksternal
    if ($row && $row['foto'] && strpos($row['foto'], 'uploads/') === 0 && file_exists($row['foto'])) {
        unlink($row['foto']);
    }

    $stmt = mysqli_prepare($KONEKSI, "DELETE FROM galery WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        echo json_encode(['success' => true, 'message' => 'Foto berhasil dihapus dari galeri.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($KONEKSI)]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Action tidak dikenal.']);
