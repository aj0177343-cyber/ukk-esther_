<?php
// Konfigurasi database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gudang-ukk';

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Base URL
$base_url = '/UKK-Esther_/';
?>