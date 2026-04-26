<?php
require_once '../functions/db.php';
require_once '../functions/auth_orangtua.php';

$active_page = 'dashboard';
$user_id  = getUserId($conn);
$nama     = $_SESSION['nama'] ?? 'Orang Tua';
$inisial  = strtoupper(substr($nama, 0, 2));

// Ambil data siswa milik user ini
$siswa   = null;
$dokumen = null;

$stmt = $conn->prepare("SELECT * FROM siswa WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $siswa = $result->fetch_assoc();
    $stmt2 = $conn->prepare("SELECT * FROM dokumen WHERE siswa_id = ? LIMIT 1");
    $stmt2->bind_param("i", $siswa['id']);
    $stmt2->execute();
    $dokumen = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();
}
$stmt->close();

// Hitung kelengkapan dokumen
$dok_ada   = 0;
$total_dok = 3; // akta_kelahiran, kartu_keluarga, foto
if ($dokumen) {
    if (!empty($dokumen['akta_kelahiran'])) $dok_ada++;
    if (!empty($dokumen['kartu_keluarga'])) $dok_ada++;
    if (!empty($dokumen['foto']))           $dok_ada++;
}

// Pengumuman terbaru dari database
$pengumuman_list = [];
$res_peng = $conn->query("SELECT id, judul, isi, gambar, tanggal FROM pengumuman ORDER BY tanggal DESC LIMIT 3");
if ($res_peng) while ($row = $res_peng->fetch_assoc()) $pengumuman_list[] = $row;

// Kontak sekolah dari database
$kontak = $conn->query("SELECT * FROM kontak LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beranda — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .welcome-banner {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border-radius: var(--radius-lg); padding: 2rem; color: #fff;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1.5rem; margin-bottom: 1.75rem; flex-wrap: wrap;
  }
  .welcome-banner h2 { font-size: 1.4rem; font-weight: 800; margin-bottom: 0.35rem; }
  .welcome-banner p  { font-size: 0.875rem; opacity: 0.88; max-width: 480px; line-height: 1.6; }
  .banner-icon {
    width: 72px; height: 72px; background: rgba(255,255,255,0.15);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; flex-shrink: 0;
  }
  .quick-links { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
  .quick-link-card {
    background: var(--bg-white); border: 1px solid var(--border);
    border-radius: var(--radius-md); padding: 1.25rem 1rem;
    text-align: center; text-decoration: none; color: var(--text-primary);
    transition: all 0.18s; display: flex; flex-direction: column; align-items: center; gap: 0.6rem;
  }
  .quick-link-card:hover { border-color: var(--primary); box-shadow: var(--shadow-blue); transform: translateY(-2px); color: var(--primary); }
  .quick-link-card .ql-icon { width: 44px; height: 44px; background: var(--primary-light); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--primary); }
  .quick-link-card span { font-size: 0.8rem; font-weight: 600; }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
  .info-list { list-style: none; display: flex; flex-direction: column; gap: 0.85rem; }
  .info-list li { display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.85rem; color: var(--text-secondary); }
  .info-list li .il-icon { width: 32px; height: 32px; background: var(--primary-light); color: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; margin-top: 2px; }
  .info-list li strong { display: block; color: var(--text-primary); font-size: 0.88rem; }
  .empty-state { text-align:center; padding:1.5rem 1rem; color:var(--text-muted); font-size:0.85rem; }
  @media(max-width:768px){ .grid-2{ grid-template-columns:1fr; } }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>
  <div class="content-isi">

    <div class="topbar">
      <div class="topbar-left">
        <h1>Selamat Datang 👋</h1>
        <p><?= date('l, d F Y') ?></p>
      </div>
      <div class="topbar-right"><div class="avatar-circle"><?= $inisial ?></div></div>
    </div>

    <div class="welcome-banner">
      <div>
        <h2>Halo, <?= htmlspecialchars($nama) ?>!</h2>
        <p>Selamat datang di portal PPDB RA An-Nabil. Gunakan menu navigasi untuk mengakses layanan pendaftaran, memantau status, dan mendapatkan informasi terbaru.</p>
      </div>
      <div class="banner-icon"><i class="fas fa-graduation-cap"></i></div>
    </div>

    <!-- Metrik dari database -->
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-icon blue"><i class="fas fa-file-alt"></i></div>
        <div>
          <div class="metric-label">Status Berkas</div>
          <div class="metric-value" style="font-size:1rem;margin-top:4px;">
            <?php if (!$siswa): ?>
              <span class="badge badge-gray">Belum Daftar</span>
            <?php elseif ($siswa['status'] === 'pending'): ?>
              <span class="badge badge-yellow">Sedang Diverifikasi</span>
            <?php elseif ($siswa['status'] === 'lulus'): ?>
              <span class="badge badge-green">Diterima</span>
            <?php else: ?>
              <span class="badge badge-red">Tidak Diterima</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="metric-label">Dokumen Lengkap</div>
          <?php if (!$siswa): ?>
            <div class="metric-value" style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">—</div>
            <div class="metric-sub">Belum ada data</div>
          <?php else: ?>
            <div class="metric-value"><?= $dok_ada ?> / <?= $total_dok ?></div>
            <div class="metric-sub"><?= ($total_dok - $dok_ada) > 0 ? ($total_dok - $dok_ada).' dokumen kurang' : 'Semua lengkap ✓' ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon yellow"><i class="fas fa-calendar-alt"></i></div>
        <div>
          <div class="metric-label">Tahap Saat Ini</div>
          <div class="metric-value" style="font-size:0.95rem;margin-top:4px;">
            <?php if (!$siswa): ?>
              <span style="color:var(--text-muted);font-size:0.85rem;">Belum mendaftar</span>
            <?php elseif ($siswa['status'] === 'pending'): ?>
              Verifikasi Berkas
            <?php elseif ($siswa['status'] === 'lulus'): ?>
              Diterima
            <?php else: ?>
              Proses Selesai
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon red"><i class="fas fa-clock"></i></div>
        <div>
          <div class="metric-label">Batas Pendaftaran</div>
          <div class="metric-value" style="font-size:0.95rem;margin-top:4px;">30 Jun 2025</div>
        </div>
      </div>
    </div>

    <p class="section-label">Akses Cepat</p>
    <div class="quick-links">
      <a href="info-pendaftaran.php" class="quick-link-card"><div class="ql-icon"><i class="fas fa-info-circle"></i></div><span>Info Pendaftaran</span></a>
      <a href="pendaftaran.php" class="quick-link-card"><div class="ql-icon"><i class="fas fa-file-alt"></i></div><span>Pendaftaran</span></a>
      <a href="status.php" class="quick-link-card"><div class="ql-icon"><i class="fas fa-chart-line"></i></div><span>Status</span></a>
      <a href="pengumuman.php" class="quick-link-card"><div class="ql-icon"><i class="fas fa-bell"></i></div><span>Pengumuman</span></a>
      <a href="panduan.php" class="quick-link-card"><div class="ql-icon"><i class="fas fa-book"></i></div><span>Panduan</span></a>
      <a href="profil.php" class="quick-link-card"><div class="ql-icon"><i class="fas fa-user"></i></div><span>Profil Akun</span></a>
    </div>

    <div class="grid-2">
      <!-- Kontak dari database -->
      <div class="card">
        <div class="card-title">Informasi Sekolah</div>
        <div class="card-subtitle">Data RA An-Nabil</div>
        <?php if ($kontak): ?>
        <ul class="info-list">
          <li><div class="il-icon"><i class="fas fa-map-marker-alt"></i></div><div><strong>Alamat</strong><?= nl2br(htmlspecialchars($kontak['alamat'])) ?></div></li>
          <li><div class="il-icon"><i class="fas fa-phone"></i></div><div><strong>WhatsApp</strong><?= htmlspecialchars($kontak['whatsapp']) ?></div></li>
          <li><div class="il-icon"><i class="fas fa-envelope"></i></div><div><strong>Email</strong><?= htmlspecialchars($kontak['email']) ?></div></li>
          <?php if (!empty($kontak['tiktok'])): ?>
          <li><div class="il-icon"><i class="fab fa-tiktok"></i></div><div><strong>TikTok</strong><?= htmlspecialchars($kontak['tiktok']) ?></div></li>
          <?php endif; ?>
        </ul>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-info-circle" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>Informasi kontak belum diisi admin.</div>
        <?php endif; ?>
      </div>

      <!-- Pengumuman dari database -->
      <div class="card">
        <div class="card-title">Pengumuman Terbaru</div>
        <div class="card-subtitle">Update terakhir</div>
        <?php if (count($pengumuman_list) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <?php 
          $supported_images = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
          foreach ($pengumuman_list as $p): 
              $is_valid_image = false;
              if (!empty($p['gambar'])) {
                  $file_ext = strtolower(pathinfo($p['gambar'], PATHINFO_EXTENSION));
                  $is_valid_image = in_array($file_ext, $supported_images);
              }
          ?>
          <div style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; transition: all 0.2s;" class="peng-card">
            <?php if ($is_valid_image): ?>
            <img src="../uploads/pengumuman/<?= htmlspecialchars($p['gambar']) ?>" style="width: 100%; height: 120px; object-fit: cover;" alt="<?= htmlspecialchars($p['judul']) ?>">
            <?php endif; ?>
            <div style="padding: 1rem;">
              <strong style="display: block; margin-bottom: 0.3rem;"><?= htmlspecialchars($p['judul']) ?></strong>
              <span style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.5rem;"><?= date('d M Y', strtotime($p['tanggal'])) ?></span>
              <p style="font-size: 0.85rem; line-height: 1.4; color: var(--text-secondary); margin: 0;">
                <?= htmlspecialchars(mb_substr(strip_tags($p['isi']), 0, 100)) . (mb_strlen($p['isi']) > 100 ? '...' : '') ?>
              </p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:1rem;">
          <a href="pengumuman.php" style="font-size:0.82rem;color:var(--primary);font-weight:600;text-decoration:none;">Lihat semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-bell" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>Belum ada pengumuman.</div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
</body>
</html>