<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password_input = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);

    if (password_verify($password_input, $data['password'])) {
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        if ($data['role'] == "admin") {
            header("Location: admin/dashboard-admin.php");
        } else if ($data['role'] == "spg") {
            header("Location: spg/dashboard-spg.php");
        } else if ($data['role'] == "collecting") {
            header("Location: collecting/dashboard-collecting.php");
        }
        exit;
    }
}

echo "<script>alert('Login gagal. Cek username atau password!'); window.location='index.php';</script>";
?>
