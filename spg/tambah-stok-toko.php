<?php
include '../koneksi.php';
session_start();

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
  <title>Tambah Stok - <?= htmlspecialchars($toko['nama_toko']); ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f3f3f3;
      margin: 0;
      padding: 20px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .container {
      max-width: 960px;
      margin: auto;
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      overflow-x: auto;
      display: block;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #eee;
    }

    input[type="number"], input[type="text"] {
      padding: 6px;
      width: 100%;
      box-sizing: border-box;
    }

    button {
      background-color: green;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: darkgreen;
    }

    .submit-btn {
      text-align: center;
      margin-top: 20px;
    }

    @media (max-width: 600px) {
      th, td {
        font-size: 14px;
        padding: 8px;
      }

      button {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<h2>Tambah Stok untuk <?= htmlspecialchars($toko['nama_toko']); ?></h2>

<div class="container">
  <?php if (isset($_SESSION['error'])): ?>
    <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <form action="proses-tambah-stok.php" method="post">
    <input type="hidden" name="id_toko" value="<?= $id_toko; ?>">

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

    <div class="submit-btn">
      <button type="submit">Simpan Perubahan</button>
    </div>
  </form>
</div>

</body>
</html>
