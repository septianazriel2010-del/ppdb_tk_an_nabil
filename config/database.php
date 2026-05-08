<?php

$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'ppdb_tk_an_nabil';

$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    // Simpan error asli ke log server
    error_log('Database connection failed: ' . mysqli_connect_error());

    // Pesan aman untuk user
    die('Koneksi database gagal.');
}

// Set charset agar lebih aman dari encoding issue
mysqli_set_charset($conn, 'utf8mb4');
?>