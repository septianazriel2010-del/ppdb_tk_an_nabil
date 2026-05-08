<?php
// functions/auth_admin.php
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

// Sudah login tapi bukan admin → tolak akses
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

function requireAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
}

function getAdminName() {
    return $_SESSION['nama'] ?? 'Admin';
}