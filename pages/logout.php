<?php
session_start();
require_once '../functions/functions.php';

// Proses logout jika dikonfirmasi
if (isset($_POST['confirm_logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$nama = $_SESSION['nama'] ?? 'Pengguna';
$inisial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .logout-wrap {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-page);
    padding: 1.5rem;
  }
  .logout-card {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 2.5rem 2rem;
    max-width: 420px;
    width: 100%;
    text-align: center;
    box-shadow: var(--shadow-md);
  }
  .logout-icon {
    width: 72px; height: 72px;
    background: var(--danger-bg);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.75rem;
    color: var(--danger);
  }
  .logout-card h2 { font-size: 1.3rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem; }
  .logout-card p  { font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 2rem; line-height: 1.6; }
  .avatar-sm {
    width: 44px; height: 44px;
    background: var(--primary);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 0.9rem; font-weight: 700;
    margin: 0 auto 0.5rem;
  }
  .user-info { margin-bottom: 2rem; padding: 1rem; background: var(--bg-surface); border-radius: var(--radius-md); border: 1px solid var(--border); }
  .user-info .u-name { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); }
  .user-info .u-role { font-size: 0.78rem; color: var(--text-muted); }

  .btn-logout {
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    width: 100%;
    padding: 0.75rem;
    background: var(--danger);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    margin-bottom: 0.75rem;
    transition: background 0.18s, transform 0.15s;
    box-shadow: 0 4px 14px rgba(220,38,38,0.25);
  }
  .btn-logout:hover { background: #b91c1c; transform: translateY(-1px); }

  .btn-cancel {
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    width: 100%;
    padding: 0.75rem;
    background: var(--bg-white);
    color: var(--text-secondary);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    transition: all 0.18s;
  }
  .btn-cancel:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
</style>
</head>
<body>
<div class="logout-wrap">
  <div class="logout-card">
    <div class="logout-icon"><i class="fas fa-sign-out-alt"></i></div>

    <h2>Keluar dari Akun?</h2>
    <p>Anda akan keluar dari portal PPDB RA An-Nabil. Pastikan semua data telah tersimpan sebelum melanjutkan.</p>

    <div class="user-info">
      <div class="avatar-sm"><?= $inisial ?></div>
      <div class="u-name"><?= htmlspecialchars($nama) ?></div>
      <div class="u-role">Orang Tua / Wali</div>
    </div>

    <form method="POST">
      <button type="submit" name="confirm_logout" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i> Ya, Keluar Sekarang
      </button>
    </form>

    <a href="dashboard-orangtua.php" class="btn-cancel">
      <i class="fas fa-arrow-left"></i> Batal, Kembali ke Dashboard
    </a>
  </div>
</div>
</body>
</html>