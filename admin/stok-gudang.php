<?php include 'header.php'; ?>

<h2 style="text-align: center; margin-bottom: 20px;">📦 Data Stok Gudang</h2>

<div class="table-wrapper">
  <table>
    <thead>
      <tr style="background-color: black; color: white;">
        <th>Nama Barang</th>
        <th>Jumlah</th>
        <th>Satuan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      include '../koneksi.php';
      $query = mysqli_query($conn, "SELECT * FROM stok_gudang ORDER BY id ASC");
      while ($data = mysqli_fetch_assoc($query)) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($data['nama_barang']) . "</td>";
          echo "<td>" . htmlspecialchars($data['jumlah']) . "</td>";
          echo "<td>" . htmlspecialchars($data['satuan']) . "</td>";
          echo "<td style='text-align:center;'>
              <a href='tambah-stok-gudang.php?id={$data['id']}' title='Tambah'>➕</a> &nbsp;
              <a href='kurang-stok-gudang.php?id={$data['id']}' title='Kurangi'>➖</a>
            </td>";
          echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>

</div> <!-- Tutup .content -->
</body>
</html>
