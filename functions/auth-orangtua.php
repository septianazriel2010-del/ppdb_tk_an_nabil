<?php
// auth_orangtua.php
// Sesuai dengan session yang disimpan di login.php:
// $_SESSION['login'], $_SESSION['role'], $_SESSION['nama'], $_SESSION['email']

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Belum login → redirect ke login.php (ada di folder pages/)
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

// Sudah login tapi bukan orangtua → tolak akses
if ($_SESSION['role'] !== 'orangtua') {
    header('Location: dashboard-admin.php');
    exit;
}