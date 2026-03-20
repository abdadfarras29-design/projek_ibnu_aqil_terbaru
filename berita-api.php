<?php
session_start();

// Hanya superadmin/admin yang boleh akses
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'koneksi.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ============================================================
// UPLOAD FOTO (POST multipart)
// ============================================================
if ($method === 'POST' && $action === 'upload') {
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Gagal upload file']);
        exit();
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['foto']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Format file tidak didukung']);
        exit();
    }

    if ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar (maks 3 MB)']);
        exit();
    }

    $uploadDir = __DIR__ . '/uploads/berita/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $filename = 'berita_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $dest = $uploadDir . $filename;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
        exit();
    }

    echo json_encode(['success' => true, 'filename' => 'uploads/berita/' . $filename]);
    exit();
}

// ============================================================
// READ ALL
// ============================================================
if ($method === 'GET' && $action === 'list') {
    $q = mysqli_query($KONEKSI, "SELECT * FROM berita ORDER BY tanggal DESC, id DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($q)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}

// ============================================================
// READ ONE
// ============================================================
if ($method === 'GET' && $action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = mysqli_prepare($KONEKSI, "SELECT * FROM berita WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if ($row) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Berita tidak ditemukan']);
    }
    exit();
}

// ============================================================
// CREATE
// ============================================================
if ($method === 'POST' && $action === 'create') {
    $input = json_decode(file_get_contents('php://input'), true);
    $judul = trim($input['judul'] ?? '');
    $foto = trim($input['foto'] ?? '');
    $kategori = trim($input['kategori'] ?? '');
    $tanggal = trim($input['tanggal'] ?? '');
    $deskripsi = trim($input['deskripsi'] ?? '');

    if (!$judul || !$kategori || !$tanggal || !$deskripsi) {
        echo json_encode(['success' => false, 'message' => 'Field wajib tidak boleh kosong']);
        exit();
    }

    // Hapus validasi array agar bisa varchar bebas (bukan enum)

    $stmt = mysqli_prepare($KONEKSI, "INSERT INTO berita (judul, foto, kategori, tanggal, deskripsi) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $judul, $foto, $kategori, $tanggal, $deskripsi);

    if (mysqli_stmt_execute($stmt)) {
        $new_id = mysqli_insert_id($KONEKSI);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'id' => $new_id, 'message' => 'Berita berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan berita: ' . mysqli_error($KONEKSI)]);
    }
    exit();
}

// ============================================================
// UPDATE
// ============================================================
if ($method === 'POST' && $action === 'update') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
    $judul = trim($input['judul'] ?? '');
    $foto = trim($input['foto'] ?? '');
    $kategori = trim($input['kategori'] ?? '');
    $tanggal = trim($input['tanggal'] ?? '');
    $deskripsi = trim($input['deskripsi'] ?? '');

    if (!$id || !$judul || !$kategori || !$tanggal || !$deskripsi) {
        echo json_encode(['success' => false, 'message' => 'Field wajib tidak boleh kosong']);
        exit();
    }

    // Hapus validasi kategori agar bebas varchar

    $stmt = mysqli_prepare($KONEKSI, "UPDATE berita SET judul=?, foto=?, kategori=?, tanggal=?, deskripsi=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssssi", $judul, $foto, $kategori, $tanggal, $deskripsi, $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'message' => 'Berita berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui: ' . mysqli_error($KONEKSI)]);
    }
    exit();
}

// ============================================================
// DELETE
// ============================================================
if ($method === 'POST' && $action === 'delete') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit();
    }

    // Ambil foto dulu untuk dihapus dari server
    $q = mysqli_prepare($KONEKSI, "SELECT foto FROM berita WHERE id = ?");
    mysqli_stmt_bind_param($q, "i", $id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($q);

    $stmt = mysqli_prepare($KONEKSI, "DELETE FROM berita WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Hapus file foto jika ada dan dimulai dengan uploads/
        if ($row && !empty($row['foto']) && strpos($row['foto'], 'uploads/') === 0) {
            $path = __DIR__ . '/' . $row['foto'];
            if (file_exists($path))
                @unlink($path);
        }
        echo json_encode(['success' => true, 'message' => 'Berita berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($KONEKSI)]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal']);
