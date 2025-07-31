<?php
include '../koneksi.php';

$id_toko = $_GET['id_toko'];
$toko = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM toko WHERE id = $id_toko"));
$stok = mysqli_query($conn, "SELECT * FROM stok_toko WHERE id_toko = $id_toko ORDER BY id ASC");
$parfum = mysqli_query($conn, "SELECT * FROM stok_gudang ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Stok ke Toko</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: white;
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
      padding: 12px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background-color: #f0f0f0;
    }

    input[type="number"],
    input[type="text"] {
      width: 100%;
      padding: 6px;
      box-sizing: border-box;
    }

    button {
      display: block;
      margin: 20px auto 0;
      background-color: #28a745;
      color: white;
      border: none;
      padding: 12px 24px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background-color: #218838;
    }

    @media (max-width: 600px) {
      .container {
        padding: 20px;
      }

      th, td {
        font-size: 14px;
        padding: 8px;
      }

      button {
        width: 100%;
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Tambah Stok untuk <strong><?= htmlspecialchars($toko['nama_toko']); ?></strong></h2>

  <form action="proses-tambah-stok-toko.php" method="post">
    <input type="hidden" name="id_toko" value="<?= $id_toko; ?>">

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Varian Parfum</th>
            <th>Stok Saat Ini</th>
            <th>Tambah (pcs)</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($p = mysqli_fetch_assoc($parfum)) {
            $id_barang = $p['id'];
            $nama_barang = $p['nama_barang'];
            $stok_saat_ini = 0;

            $nama_barang_safe = mysqli_real_escape_string($conn, $nama_barang);
            $q = mysqli_query($conn, "SELECT * FROM stok_toko WHERE id_toko=$id_toko AND nama_barang='$nama_barang_safe'");
            if (mysqli_num_rows($q) > 0) {
                $row = mysqli_fetch_assoc($q);
                $stok_saat_ini = $row['jumlah'];
            }
        ?>
          <tr>
            <td>
              <?= htmlspecialchars($nama_barang); ?>
              <input type="hidden" name="id_barang[]" value="<?= $id_barang; ?>">
            </td>
            <td><?= $stok_saat_ini; ?> pcs</td>
            <td><input type="number" name="tambah[]" value="0" min="0"></td>
            <td><input type="text" name="keterangan[]" value="Dari Gudang"></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>

    <button type="submit">Simpan Perubahan</button>
  </form>
</div>
</body>
</html>
