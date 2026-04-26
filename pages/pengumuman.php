<?php
session_start();
require_once '../functions/auth_orangtua.php';
require_once '../functions/db.php';

$nama = $_SESSION['nama'] ?? 'User';
$inisial = strtoupper(substr($nama, 0, 2));

// Get semua pengumuman
$pengumuman_list = [];
$res = $conn->query("SELECT id, judul, isi, gambar, tanggal FROM pengumuman ORDER BY tanggal DESC");
if ($res) while($r = $res->fetch_assoc()) $pengumuman_list[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengumuman — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .pengumuman-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
  .peng-card { 
    background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; 
    overflow: hidden; transition: all 0.3s; cursor: pointer;
  }
  .peng-card:hover { transform: translateY(-4px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
  .peng-img { width: 100%; height: 180px; object-fit: cover; display: block; }
  .peng-content { padding: 1rem; }
  .peng-title { font-weight: 600; font-size: 1rem; margin-bottom: 0.5rem; color: #0f172a; }
  .peng-date { font-size: 0.75rem; color: #64748b; margin-bottom: 0.8rem; }
  .peng-text { font-size: 0.9rem; line-height: 1.5; color: #475569; }
  .placeholder-img {
    width: 100%; height: 180px; background: linear-gradient(135deg, #06b6d4, #3b82f6);
    display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;
  }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>
  <div class="content-isi">

    <div class="topbar">
      <div class="topbar-left">
        <h1>Pengumuman</h1>
        <p><?= date('l, d F Y') ?></p>
      </div>
      <div class="topbar-right"><div class="avatar-circle"><?= $inisial ?></div></div>
    </div>

    <div class="card">
      <div class="card-title">Semua Pengumuman</div>
      <div class="card-subtitle">Informasi terbaru dari RA An-Nabil</div>

      <?php if (count($pengumuman_list) > 0): ?>
      <div class="pengumuman-grid">
        <?php foreach ($pengumuman_list as $p): ?>
        <div class="peng-card">
          <?php if (!empty($p['gambar'])): ?>
            <img src="../uploads/pengumuman/<?= htmlspecialchars($p['gambar']) ?>" alt="" class="peng-img">
          <?php else: ?>
            <div class="placeholder-img"><i class="fas fa-bell"></i></div>
          <?php endif; ?>
          <div class="peng-content">
            <div class="peng-title"><?= htmlspecialchars($p['judul']) ?></div>
            <div class="peng-date"><i class="fas fa-calendar"></i> <?= date('d F Y', strtotime($p['tanggal'])) ?></div>
            <div class="peng-text">
              <?= htmlspecialchars(mb_substr(strip_tags($p['isi']), 0, 150)) . (mb_strlen($p['isi']) > 150 ? '...' : '') ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div style="text-align: center; padding: 3rem 1rem;">
        <i class="fas fa-bell" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
        <p style="color: #94a3b8; font-size: 1rem;">Belum ada pengumuman dari sekolah</p>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>
</body>
</html>
