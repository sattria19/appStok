<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'spg') {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$kode_barcode = $_POST['kode_barcode'] ?? '';
$id_toko = $_POST['id_toko'] ?? 0;
$jumlah = $_POST['jumlah'] ?? 0;
$username = $_SESSION['username'];

if (empty($kode_barcode)) {
    echo json_encode(['status' => 'error', 'message' => 'Kode barcode tidak boleh kosong']);
    exit;
}

if ($id_toko == 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID Toko tidak valid']);
    exit;
}

if ($jumlah <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Jumlah harus lebih dari 0']);
    exit;
}

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // Cek barcode dan ambil nama barang
    $query = "SELECT bp.id, bp.nama_barang, bp.status 
              FROM barcode_produk bp 
              WHERE bp.kode_barcode = ? FOR UPDATE";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $kode_barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Barcode tidak ditemukan'
        ]);
        exit;
    }

    $data = $result->fetch_assoc();
    $nama_barang = $data['nama_barang'];

    // Ambil nama toko
    $toko_query = "SELECT nama_toko FROM toko WHERE id = ?";
    $toko_stmt = $conn->prepare($toko_query);
    $toko_stmt->bind_param("i", $id_toko);
    $toko_stmt->execute();
    $toko_result = $toko_stmt->get_result();

    if ($toko_result->num_rows === 0) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Toko tidak ditemukan'
        ]);
        exit;
    }

    $toko_data = $toko_result->fetch_assoc();
    $nama_toko = $toko_data['nama_toko'];

    // Cek apakah stok toko sudah ada
    $check_stok = "SELECT id, jumlah FROM stok_toko WHERE id_toko = ? AND nama_barang = ?";
    $check_stmt = $conn->prepare($check_stok);
    $check_stmt->bind_param("is", $id_toko, $nama_barang);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    $stok_baru = 0;
    if ($check_result->num_rows > 0) {
        // Update stok yang sudah ada
        $stok_row = $check_result->fetch_assoc();
        $stok_lama = $stok_row['jumlah'];
        $stok_baru = $stok_lama + $jumlah;

        $update_stok = "UPDATE stok_toko SET jumlah = ?, updated_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_stok);
        $update_stmt->bind_param("ii", $stok_baru, $stok_row['id']);

        if (!$update_stmt->execute()) {
            throw new Exception("Gagal update stok toko: " . $update_stmt->error);
        }
    } else {
        // Insert stok baru
        $stok_baru = $jumlah;
        $insert_stok = "INSERT INTO stok_toko (id_toko, nama_barang, jumlah, updated_at) 
                        VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_stok);
        $insert_stmt->bind_param("isi", $id_toko, $nama_barang, $jumlah);

        if (!$insert_stmt->execute()) {
            throw new Exception("Gagal menambah stok toko: " . $insert_stmt->error);
        }
    }

    // Update status barcode menjadi di_toko - NamaToko
    $new_status = "di_toko - " . $nama_toko;
    $update_status = "UPDATE barcode_produk SET status = ?, updated_at = NOW() WHERE id = ?";
    $status_stmt = $conn->prepare($update_status);
    $status_stmt->bind_param("si", $new_status, $data['id']);

    if (!$status_stmt->execute()) {
        throw new Exception("Gagal update status barcode: " . $status_stmt->error);
    }

    // Log aktivitas
    $check_log = $conn->query("SHOW TABLES LIKE 'log_aktivitas'");
    if ($check_log->num_rows > 0) {
        $log_query = "INSERT INTO log_aktivitas (username, aksi, tabel, waktu) 
                      VALUES (?, ?, 'stok_toko', NOW())";

        $aksi = "Menambah stok $nama_toko: {$nama_barang} (Barcode: {$kode_barcode}) sebanyak {$jumlah} pcs";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("ss", $username, $aksi);
        $log_stmt->execute();
    }

    // Commit transaksi
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Stok berhasil ditambahkan ke toko',
        'data' => [
            'nama_barang' => $nama_barang,
            'kode_barcode' => $kode_barcode,
            'jumlah_tambah' => $jumlah,
            'stok_baru' => $stok_baru
        ]
    ]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error dalam proses-tambah-stok-toko.php: " . $e->getMessage());
    error_log("Kode barcode: " . $kode_barcode);
    error_log("ID Toko: " . $id_toko);
    error_log("Jumlah: " . $jumlah);
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat menambah stok: ' . $e->getMessage()
    ]);
}

$conn->close();
