<?php
include 'header.php';
include '../koneksi.php';

// Ambil filter
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$sort = ($_GET['sort'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

// Bangun kondisi WHERE
$kondisi = "WHERE 1";
if ($tanggal_awal !== '' && $tanggal_akhir !== '') {
    $kondisi .= " AND DATE(dibuat_pada) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
} elseif ($tanggal_awal !== '') {
    $kondisi .= " AND DATE(dibuat_pada) >= '$tanggal_awal'";
} elseif ($tanggal_akhir !== '') {
    $kondisi .= " AND DATE(dibuat_pada) <= '$tanggal_akhir'";
}

// Ambil data toko
$query = mysqli_query($conn, "SELECT * FROM toko $kondisi ORDER BY dibuat_pada $sort");
$data_toko = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data_toko[] = $row;
}
?>

<?php if (isset($_GET['hapus']) && $_GET['hapus'] == 'success'): ?>
<script>alert("Toko berhasil dihapus!");</script>
<?php endif; ?>

<h2 style="text-align: center;">Daftar Mitra XLPerfume</h2>

<div style="width: 95%; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;">
    <a href="tambah-toko.php" style="background-color: green; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-bottom: 10px;">➕ Tambah Toko</a>

    <!-- Form Ekspor PDF -->
    <form method="POST" action="export-toko-pdf.php" style="margin-bottom: 10px;">
        <input type="hidden" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>">
        <input type="hidden" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>">
        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? 'desc') ?>">
        <button type="submit" style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 5px;">📄 Ekspor ke PDF</button>
    </form>
</div>

<!-- Filter Tanggal & Urutkan -->
<div style="width: 95%; margin: 0 auto; margin-bottom: 10px;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
        <label>Dari:</label>
        <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>">
        <label>Sampai:</label>
        <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>">

        <label>Urutkan:</label>
        <select name="sort" style="padding: 5px;">
            <option value="desc" <?= ($_GET['sort'] ?? '') === 'desc' ? 'selected' : '' ?>>Terbaru</option>
            <option value="asc" <?= ($_GET['sort'] ?? '') === 'asc' ? 'selected' : '' ?>>Terlama</option>
        </select>

        <button type="submit">🔍 Tampilkan</button>
    </form>
</div>

<!-- Search -->
<div style="width: 95%; margin: 0 auto;">
    <input type="text" id="searchInput" placeholder="Cari Nama Toko..." style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;">
</div>

<!-- Tabel Toko -->
<div class="table-wrapper" style="overflow-x:auto; width: 95%; margin: 0 auto;">
    <table id="tokoTable">
        <thead style="background-color: black; color: white;">
            <tr>
                <th>Nama Toko</th>
                <th>Lokasi (Google Maps)</th>
                <th>Alamat Manual</th>
                <th>Dibuat Pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_toko as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_toko']) ?></td>
                <td><a href="<?= htmlspecialchars($row['lokasi_maps']) ?>" target="_blank">Lihat Lokasi</a></td>
                <td><a href="<?= htmlspecialchars($row['alamat_manual']) ?>" target="_blank">Lihat Alamat</a></td>
                <td><?= $row['dibuat_pada'] ? date('d-m-Y H:i', strtotime($row['dibuat_pada'])) : '-' ?></td>
                <td>
                    <a href="edit-toko.php?id=<?= $row['id'] ?>" style="color: blue;">Edit</a> |
                    <a href="hapus-toko.php?id=<?= $row['id'] ?>" style="color: red;" onclick="return confirm('Yakin ingin menghapus toko ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center" style="text-align:center; margin-top: 10px;">
        <button onclick="changePage(-1)" style="padding: 5px 15px;">Prev</button>
        <span id="pageInfo" style="margin: 0 10px;"></span>
        <button onclick="changePage(1)" style="padding: 5px 15px;">Next</button>
    </div>
</div>

<script>
// Search bar
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#tokoTable tbody tr");
    rows.forEach(row => {
        const namaToko = row.cells[0].innerText.toLowerCase();
        row.style.display = namaToko.includes(filter) ? "" : "none";
    });
});

// Pagination
let currentPage = 1;
const rowsPerPage = 10;
const rows = document.querySelectorAll("#tokoTable tbody tr");

function showPage(page) {
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    currentPage = page;

    rows.forEach((row, index) => {
        row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? "" : "none";
    });

    document.getElementById("pageInfo").innerText = `Halaman ${page} dari ${totalPages}`;
}

function changePage(delta) {
    showPage(currentPage + delta);
}

document.addEventListener("DOMContentLoaded", function () {
    showPage(currentPage);
});
</script>

<?php include 'footer.php'; ?>
