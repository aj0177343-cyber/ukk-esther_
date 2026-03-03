<?php
session_start();

// Path absolut menggunakan __DIR__
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/function.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id_user = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Jika user tidak ditemukan
if (!$user) {
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}

// Base URL
$base_url = '/UKK-Esther_/';
?>