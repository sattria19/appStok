<?php
include '../koneksi.php';
session_start();

if ($_SESSION['role'] != 'collecting') {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];

$stok_query = mysqli_query($conn, "
    SELECT toko.id, toko.nama_toko, 
           IFNULL(SUM(stok_toko.jumlah), 0) as total_stok
    FROM toko
    LEFT JOIN stok_toko ON toko.id = stok_toko.id_toko
    GROUP BY toko.id
    ORDER BY toko.nama_toko ASC
");

$data_toko = [];
while ($row = mysqli_fetch_assoc($stok_query)) {
    $data_toko[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Collecting</title>
  <style>
    body {
      font-family: sans-serif;
      background-color: #f9f9f9;
      padding: 20px;
      margin: 0;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .search-container {
      text-align: center;
      margin-bottom: 20px;
    }

    .search-container input[type="text"] {
      padding: 10px;
      width: 300px;
      max-width: 90%;
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      min-width: 600px;
      background: white;
      border-radius: 5px;
      overflow: hidden;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 12px 10px;
      text-align: center;
    }

    th {
      background-color: #f0f0f0;
    }

    .pagination {
      text-align: center;
      margin-top: 30px;
      margin-bottom: 40px;
    }

    .pagination button {
      background-color: #007bff;
      color: white;
      padding: 10px 16px;
      margin: 5px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }

    .pagination button:hover {
      background-color: #0056b3;
    }

    #pageInfo {
      font-size: 16px;
      margin: 0 15px;
    }

    i {
      font-style: normal;
      font-size: 20px;
      cursor: pointer;
    }

    @media (max-width: 600px) {
      th, td {
        font-size: 14px;
        padding: 8px;
      }

      .pagination button {
        width: 40%;
        font-size: 16px;
      }
    }
  </style>
</head>
<body>

<h2>Daftar Stok Toko</h2>

<div class="search-container">
  <input type="text" id="searchInput" placeholder="Cari Toko...">
</div>

<div class="table-wrapper">
  <table id="tokoTable">
    <thead>
      <tr>
        <th>Nama Toko</th>
        <th>Total Stok</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data_toko as $toko): ?>
        <tr>
          <td><?= htmlspecialchars($toko['nama_toko']) ?></td>
          <td><?= $toko['total_stok'] ?> pcs</td>
          <td>
            <a href="tambah-stok-toko.php?id_toko=<?= $toko['id'] ?>" title="Tambah Stok">
              <i style="color:blue;">➕</i>
            </a>
            &nbsp;
            <a href="kurang-stok-toko.php?id_toko=<?= $toko['id'] ?>" title="Kurang Stok">
              <i style="color:orange;">➖</i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="pagination">
  <button id="prevBtn">⬅️ Prev</button>
  <span id="pageInfo"></span>
  <button id="nextBtn">Next ➡️</button>
</div>

<script>
let currentPage = 1;
let rowsPerPage = 10;

function getVisibleRows() {
  return Array.from(document.querySelectorAll("#tokoTable tbody tr"))
              .filter(row => row.style.display !== "none");
}

function showPage(page) {
  let visibleRows = getVisibleRows();
  let totalPages = Math.ceil(visibleRows.length / rowsPerPage);

  if (page < 1) page = 1;
  if (page > totalPages) page = totalPages;

  currentPage = page;

  visibleRows.forEach((row, index) => {
    row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? "" : "none";
  });

  document.getElementById("pageInfo").innerText = `Halaman ${page} dari ${totalPages}`;
}

function changePage(delta) {
  showPage(currentPage + delta);
}

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchInput");

  searchInput.addEventListener("keyup", () => {
    const filter = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll("#tokoTable tbody tr");

    rows.forEach(row => {
      const namaToko = row.cells[0].innerText.toLowerCase();
      row.style.display = namaToko.includes(filter) ? "" : "none";
    });

    currentPage = 1;
    showPage(currentPage);
  });

  document.getElementById("prevBtn").addEventListener("click", () => changePage(-1));
  document.getElementById("nextBtn").addEventListener("click", () => changePage(1));

  showPage(currentPage);
});
</script>


</body>
</html>
