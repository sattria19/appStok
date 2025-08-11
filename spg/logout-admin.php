<?php
session_start();

// Destroy semua session
session_destroy();

// Redirect ke halaman login
header("Location: ../index.php");
exit();
