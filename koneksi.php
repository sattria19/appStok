<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_parfum"; // nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

// Atur zona waktu ke WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
