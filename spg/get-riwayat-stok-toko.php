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

$id_toko = $_GET['id_toko'] ?? 0;
$username = $_SESSION['username'];

if ($id_toko == 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID Toko tidak valid']);
    exit;
}

try {
    // Ambil nama toko terlebih dahulu
    $toko_query = "SELECT nama_toko FROM toko WHERE id = ?";
    $toko_stmt = $conn->prepare($toko_query);
    $toko_stmt->bind_param("i", $id_toko);
    $toko_stmt->execute();
    $toko_result = $toko_stmt->get_result();
    $toko_data = $toko_result->fetch_assoc();

    if (!$toko_data) {
        echo json_encode(['status' => 'error', 'message' => 'Toko tidak ditemukan']);
        exit;
    }

    $nama_toko = $toko_data['nama_toko'];

    // Ambil riwayat penambahan stok hari ini untuk toko tertentu
    // Menggunakan log_aktivitas untuk mendapatkan riwayat
    $query = "SELECT 
                username,
                aksi,
                waktu
              FROM log_aktivitas 
              WHERE username = ? 
              AND aksi LIKE CONCAT('%Menambah stok ', ?, '%')
              AND DATE(waktu) = CURDATE()
              ORDER BY waktu DESC 
              LIMIT 20";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $nama_toko);
    $stmt->execute();
    $result = $stmt->get_result();

    $riwayat = [];
    while ($row = $result->fetch_assoc()) {
        // Parse informasi dari aksi
        $aksi = $row['aksi'];

        // Extract nama toko, nama barang dan barcode dari aksi
        // Format: "Menambah stok $nama_toko: {$nama_barang} (Barcode: {$kode_barcode}) sebanyak {$jumlah} pcs"
        preg_match('/Menambah stok (.+?): (.+?) \(Barcode: (.+?)\) sebanyak (\d+) pcs/', $aksi, $matches);

        if (count($matches) >= 5) {
            $riwayat[] = [
                'nama_toko' => $matches[1],
                'nama_barang' => $matches[2],
                'kode_barcode' => $matches[3],
                'jumlah' => $matches[4],
                'waktu' => date('H:i:s', strtotime($row['waktu']))
            ];
        }
    }

    // Jika tidak ada data dari log, coba ambil dari stok_toko yang diupdate hari ini
    if (empty($riwayat)) {
        $alt_query = "SELECT 
                        nama_barang,
                        jumlah,
                        updated_at
                      FROM stok_toko 
                      WHERE id_toko = ? 
                      AND DATE(updated_at) = CURDATE()
                      ORDER BY updated_at DESC 
                      LIMIT 10";

        $alt_stmt = $conn->prepare($alt_query);
        $alt_stmt->bind_param("i", $id_toko);
        $alt_stmt->execute();
        $alt_result = $alt_stmt->get_result();

        while ($row = $alt_result->fetch_assoc()) {
            $riwayat[] = [
                'nama_toko' => '-',
                'nama_barang' => $row['nama_barang'],
                'kode_barcode' => '-',
                'jumlah' => $row['jumlah'],
                'waktu' => date('H:i:s', strtotime($row['updated_at']))
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $riwayat
    ]);
} catch (Exception $e) {
    error_log("Error dalam get-riwayat-stok-toko.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat mengambil riwayat'
    ]);
}

$conn->close();
