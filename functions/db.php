<?php
// db.php — Koneksi database, include di semua halaman
// Sesuaikan konfigurasi berikut dengan server kamu
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ganti dengan username database kamu
define('DB_PASS', '');           // ganti dengan password database kamu
define('DB_NAME', 'ppdb_tk_an_nabil'); // ganti dengan nama database kamu

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#dc2626;background:#fee2e2;border-radius:8px;margin:2rem;">
        <strong>Koneksi database gagal:</strong> ' . htmlspecialchars($conn->connect_error) . '
    </div>');
}

// Helper: ambil user_id dari tabel users berdasarkan email di session
// Login.php tidak menyimpan user_id ke session, jadi kita ambil dari DB
function getUserId($conn) {
    if (!isset($_SESSION['email'])) return null;
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['id'] : null;
}