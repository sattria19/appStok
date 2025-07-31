<?php
require_once __DIR__ . '/../vendor/autoload.php';
include '../koneksi.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Ambil filter
$tanggal_filter = $_GET['tanggal'] ?? '';
$varian_filter = $_GET['varian'] ?? '';

$kondisi = "WHERE 1";
if ($tanggal_filter != '') $kondisi .= " AND DATE(updated_at) = '$tanggal_filter'";
if ($varian_filter != '') $kondisi .= " AND nama_barang = '$varian_filter'";

// Ambil data barcode
$query = mysqli_query($conn, "SELECT * FROM barcode_produk $kondisi ORDER BY updated_at DESC");
$barcodes = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Siapkan QR options
$options = new QROptions([
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel' => QRCode::ECC_L,
    'scale' => 2, // lebih kecil dari sebelumnya
]);

// Pastikan folder temp_qr ada
$temp_dir = __DIR__ . '/temp_qr/';
if (!file_exists($temp_dir)) {
    mkdir($temp_dir, 0777, true);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode Batch</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .filter {
            margin-bottom: 20px;
        }

        .barcode-list {
            display: flex;
            flex-wrap: wrap;
        }

        .barcode {
            border: 1px dashed #000;
            padding: 6px;
            width: 110px;
            height: 150px;
            text-align: center;
            margin: 8px;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .barcode img {
            width: 80px;
            height: 80px;
        }

        .nama-barang {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .kode {
            font-size: 8px;
            margin-top: 4px;
            word-break: break-word;
        }

        .no-print {
            margin-bottom: 15px;
        }

        @media print {
            .no-print, .filter, h2 {
                display: none !important;
            }

            body {
                margin: 0;
            }

            .barcode {
                margin: 5px;
            }
        }
    </style>
</head>
<body>

<h2 class="no-print">Daftar Barcode Siap Cetak</h2>

<!-- Filter -->
<div class="filter no-print">
    <form method="GET">
        Tanggal:
        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>">
        Varian:
        <select name="varian">
            <option value="">-- Semua --</option>
            <?php
            $varian_query = mysqli_query($conn, "SELECT DISTINCT nama_barang FROM barcode_produk ORDER BY nama_barang");
            while ($v = mysqli_fetch_assoc($varian_query)) {
                $selected = ($v['nama_barang'] == $varian_filter) ? 'selected' : '';
                echo "<option $selected>" . htmlspecialchars($v['nama_barang']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Tampilkan</button>
    </form>
</div>

<!-- Tombol Cetak -->
<div class="no-print">
    <button onclick="window.print()">🖨️ Cetak Semua Barcode</button>
</div>

<!-- Daftar barcode -->
<div class="barcode-list">
<?php
foreach ($barcodes as $item) {
    $filename = $temp_dir . $item['kode_barcode'] . '.png';
    $relativePath = 'temp_qr/' . $item['kode_barcode'] . '.png';

    // Buat file QR jika belum ada
    if (!file_exists($filename)) {
        (new QRCode($options))->render($item['kode_barcode'], $filename);
    }

    echo "<div class='barcode'>";
    echo "<div class='nama-barang'>" . htmlspecialchars($item['nama_barang']) . "</div>";
    echo "<img src='$relativePath' alt='QR'>";
    echo "<div class='kode'>" . $item['kode_barcode'] . "</div>";
    echo "</div>";
}
?>
</div>

</body>
</html>
