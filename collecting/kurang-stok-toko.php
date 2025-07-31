<?php
include '../koneksi.php';

if (!isset($_GET['id_toko'])) {
    header("Location: dashboard-collecting.php");
    exit;
}

$id_toko = (int)$_GET['id_toko'];
$toko = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_toko FROM toko WHERE id = $id_toko"));
$varian = mysqli_query($conn, "SELECT DISTINCT nama_barang FROM stok_gudang");
$stok_toko = [];
$result_stok = mysqli_query($conn, "SELECT nama_barang, jumlah FROM stok_toko WHERE id_toko = $id_toko");
while ($row = mysqli_fetch_assoc($result_stok)) {
    $stok_toko[$row['nama_barang']] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kurangi Stok Toko</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .table-responsive {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 12px;
      text-align: center;
    }

    th {
      background: #f0f0f0;
    }

    input[type="number"], select {
      width: 100%;
      padding: 6px;
      box-sizing: border-box;
      font-size: 14px;
    }

    .btn-submit {
      display: block;
      margin: 20px auto 0;
      padding: 12px 24px;
      background-color: #007bff;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .btn-submit:hover {
      background-color: #0056b3;
    }

    @media (max-width: 600px) {
      .container {
        padding: 20px;
      }

      th, td {
        font-size: 14px;
        padding: 8px;
      }

      input[type="number"], select {
        font-size: 13px;
      }

      .btn-submit {
        width: 100%;
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Kurangi Stok <strong><?= htmlspecialchars($toko['nama_toko']) ?></strong></h2>

  <form action="proses-kurang-stok-toko.php" method="POST">
    <input type="hidden" name="id_toko" value="<?= $id_toko; ?>">

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Varian</th>
            <th>Stok Saat Ini</th>
            <th>Jumlah Kurang</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($v = mysqli_fetch_assoc($varian)) : 
            $nama_barang = $v['nama_barang'];
            $stok_saat_ini = isset($stok_toko[$nama_barang]) ? $stok_toko[$nama_barang] : 0;
          ?>
          <tr>
            <td>
              <?= htmlspecialchars($nama_barang) ?>
              <input type="hidden" name="nama_barang[]" value="<?= htmlspecialchars($nama_barang) ?>">
            </td>
            <td><?= $stok_saat_ini ?> pcs</td>
            <td><input type="number" name="kurang[]" min="0" value="0"></td>
            <td>
              <select name="keterangan[]">
                <option value="Terjual">Terjual</option>
                <option value="Balik Stok">Balik Stok</option>
              </select>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <button type="submit" class="btn-submit">Simpan Perubahan</button>
  </form>
</div>
</body>
</html>
