<?php
require_once '../vendor/autoload.php';
include '../koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ====== CETAK PDF ======
if (isset($_POST['cetak_pdf'])) {
    $tanggal_awal = $_POST['tanggal_awal'] ?? '';
    $tanggal_akhir = $_POST['tanggal_akhir'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    $kondisi = "WHERE 1";
    if ($tanggal_awal && $tanggal_akhir) {
        $kondisi .= " AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
    } elseif ($tanggal_awal) {
        $kondisi .= " AND tanggal >= '$tanggal_awal'";
    } elseif ($tanggal_akhir) {
        $kondisi .= " AND tanggal <= '$tanggal_akhir'";
    }
    if ($lokasi) $kondisi .= " AND lokasi = '$lokasi'";
    if ($keterangan) $kondisi .= " AND keterangan = '$keterangan'";

    $result = mysqli_query($conn, "SELECT * FROM laporan_stok $kondisi ORDER BY tanggal DESC");

    ob_start();
    ?>
    <html>
    <head>
        <style>
            body { font-family: sans-serif; font-size: 11px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #000; padding: 5px; text-align: center; }
            th { background-color: #333; color: white; }
        </style>
    </head>
    <body>
        <h2 style="text-align: center;">Laporan Perubahan Stok Parfum</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th><th>Varian</th><th>Lokasi</th><th>Stok Awal</th><th>Masuk</th>
                    <th>Keluar</th><th>Stok Akhir</th><th>Keterangan</th><th>Oleh</th><th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($data = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $data['tanggal'] ?></td>
                        <td><?= $data['nama_barang'] ?></td>
                        <td><?= $data['lokasi'] ?></td>
                        <td><?= $data['stok_awal'] ?></td>
                        <td><?= $data['masuk'] ?? 0 ?></td>
                        <td><?= $data['keluar'] ?? 0 ?></td>
                        <td><?= $data['stok_akhir'] ?></td>
                        <td><?= $data['keterangan'] ?></td>
                        <td><?= $data['dibuat_oleh'] ?></td>
                        <td><?= $data['dibuat_pada'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    $dompdf = new Dompdf(new Options(['isRemoteEnabled' => true]));
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Laporan_Stok_Parfum.pdf', ['Attachment' => true]);
    exit;
}

// ====== EKSPOR EXCEL ======
if (isset($_POST['cetak_excel'])) {
    $tanggal_awal = $_POST['tanggal_awal'] ?? '';
    $tanggal_akhir = $_POST['tanggal_akhir'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    $kondisi = "WHERE 1";
    if ($tanggal_awal && $tanggal_akhir) {
        $kondisi .= " AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
    } elseif ($tanggal_awal) {
        $kondisi .= " AND tanggal >= '$tanggal_awal'";
    } elseif ($tanggal_akhir) {
        $kondisi .= " AND tanggal <= '$tanggal_akhir'";
    }
    if ($lokasi) $kondisi .= " AND lokasi = '$lokasi'";
    if ($keterangan) $kondisi .= " AND keterangan = '$keterangan'";

    $result = mysqli_query($conn, "
        SELECT tanggal, lokasi, nama_barang, masuk, keluar, keterangan 
        FROM laporan_stok 
        $kondisi 
        ORDER BY tanggal DESC
    ");

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'Tanggal');
    $sheet->setCellValue('B1', 'Lokasi');
    $sheet->setCellValue('C1', 'Varian');
    $sheet->setCellValue('D1', 'Masuk');
    $sheet->setCellValue('E1', 'Keluar');
    $sheet->setCellValue('F1', 'Keterangan');

    // Data
    $row = 2;
    $total_masuk = 0;
    $total_keluar = 0;

    while ($data = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue("A$row", $data['tanggal']);
        $sheet->setCellValue("B$row", $data['lokasi']);
        $sheet->setCellValue("C$row", $data['nama_barang']);
        $sheet->setCellValue("D$row", $data['masuk']);
        $sheet->setCellValue("E$row", $data['keluar']);
        $sheet->setCellValue("F$row", $data['keterangan']);

        $total_masuk += (int) $data['masuk'];
        $total_keluar += (int) $data['keluar'];
        $row++;
    }

    // Tambahkan total
    $sheet->setCellValue("C$row", 'Total');
    $sheet->setCellValue("D$row", $total_masuk);
    $sheet->setCellValue("E$row", $total_keluar);

    // Output
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Laporan_Stok_Parfum.xlsx"');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}



// ====== TAMPILAN HTML BIASA ======
include 'header.php';

$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$lokasi = $_GET['lokasi'] ?? '';
$keterangan = $_GET['keterangan'] ?? '';

$kondisi = "WHERE 1";
if ($tanggal_awal && $tanggal_akhir) {
    $kondisi .= " AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
} elseif ($tanggal_awal) {
    $kondisi .= " AND tanggal >= '$tanggal_awal'";
} elseif ($tanggal_akhir) {
    $kondisi .= " AND tanggal <= '$tanggal_akhir'";
}
if ($lokasi) $kondisi .= " AND lokasi = '$lokasi'";
if ($keterangan) $kondisi .= " AND keterangan = '$keterangan'";

$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_stok $kondisi"))['total'];
$total_pages = ceil($total / $limit);
$query = mysqli_query($conn, "SELECT * FROM laporan_stok $kondisi ORDER BY id DESC LIMIT $offset, $limit");
?>

<h2 style="text-align: center;">Laporan Perubahan Stok Parfum</h2>

<!-- Filter -->
<form method="GET" style="text-align:center; margin-bottom:10px;">
    <input type="date" name="tanggal_awal" value="<?= $tanggal_awal ?>">
    <input type="date" name="tanggal_akhir" value="<?= $tanggal_akhir ?>">
    <select name="lokasi">
        <option value="">-- Lokasi --</option>
        <?php
        $lokasi_list = mysqli_query($conn, "SELECT DISTINCT lokasi FROM laporan_stok ORDER BY lokasi ASC");
        while ($row = mysqli_fetch_assoc($lokasi_list)) {
            $sel = ($row['lokasi'] == $lokasi) ? 'selected' : '';
            echo "<option value='{$row['lokasi']}' $sel>{$row['lokasi']}</option>";
        }
        ?>
    </select>
    <select name="keterangan">
        <option value="">-- Keterangan --</option>
        <?php
        $ket_list = mysqli_query($conn, "SELECT DISTINCT keterangan FROM laporan_stok ORDER BY keterangan ASC");
        while ($row = mysqli_fetch_assoc($ket_list)) {
            $sel = ($row['keterangan'] == $keterangan) ? 'selected' : '';
            echo "<option value='{$row['keterangan']}' $sel>{$row['keterangan']}</option>";
        }
        ?>
    </select>
    <button type="submit">Filter</button>
    <a href="laporan.php" style="margin-left:10px; color:red;">Reset Filter</a>
</form>

<!-- Tombol Export -->
<form method="POST" style="text-align: center; margin-bottom: 20px;">
    <input type="hidden" name="tanggal_awal" value="<?= $tanggal_awal ?>">
    <input type="hidden" name="tanggal_akhir" value="<?= $tanggal_akhir ?>">
    <input type="hidden" name="lokasi" value="<?= $lokasi ?>">
    <input type="hidden" name="keterangan" value="<?= $keterangan ?>">

    <button type="submit" name="cetak_pdf" style="padding: 8px 16px; background:black; color:white; border-radius:5px;">🖨️ Unduh PDF</button>
    <button type="submit" name="cetak_excel" style="padding: 8px 16px; background:green; color:white; border-radius:5px;">📊 Unduh Excel</button>
</form>

<!-- Tabel -->
<div style="overflow-x:auto; width:95%; margin:0 auto;">
    <table border="1" cellpadding="8" cellspacing="0" style="min-width:1000px;">
        <thead style="background:black; color:white;">
            <tr>
                <th>Tanggal</th><th>Varian</th><th>Lokasi</th><th>Stok Awal</th><th>Masuk</th>
                <th>Keluar</th><th>Stok Akhir</th><th>Keterangan</th><th>Oleh</th><th>Waktu</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($data = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= $data['tanggal'] ?></td>
                <td><?= $data['nama_barang'] ?></td>
                <td><?= $data['lokasi'] ?></td>
                <td><?= $data['stok_awal'] ?></td>
                <td><?= $data['masuk'] ?? 0 ?></td>
                <td><?= $data['keluar'] ?? 0 ?></td>
                <td><?= $data['stok_akhir'] ?></td>
                <td><?= $data['keterangan'] ?: '-' ?></td>
                <td><?= $data['dibuat_oleh'] ?></td>
                <td><?= $data['dibuat_pada'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div style="text-align: center; margin: 20px;">
    <?php
    $params = $_GET;
    if ($page > 1) {
        $params['page'] = $page - 1;
        echo "<a href='?" . http_build_query($params) . "' style='margin-right:10px;'>Prev</a>";
    } else {
        echo "<span style='color: grey; margin-right:10px;'>Prev</span>";
    }

    echo "Halaman $page dari $total_pages";

    if ($page < $total_pages) {
        $params['page'] = $page + 1;
        echo "<a href='?" . http_build_query($params) . "' style='margin-left:10px;'>Next</a>";
    } else {
        echo "<span style='color: grey; margin-left:10px;'>Next</span>";
    }
    ?>
</div>

<?php include 'footer.php'; ?>
