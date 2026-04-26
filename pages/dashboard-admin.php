<?php
require_once '../functions/db.php';
require_once '../functions/auth_admin.php';
requireAdmin();

$active_page = 'dashboard';
$nama    = getAdminName();
$inisial = strtoupper(substr($nama, 0, 2));

// Statistik ringkasan
$total     = (int)$conn->query("SELECT COUNT(*) FROM siswa")->fetch_row()[0];
$pending   = (int)$conn->query("SELECT COUNT(*) FROM siswa WHERE status='pending'")->fetch_row()[0];
$lulus     = (int)$conn->query("SELECT COUNT(*) FROM siswa WHERE status='lulus'")->fetch_row()[0];
$tdk_lulus = (int)$conn->query("SELECT COUNT(*) FROM siswa WHERE status='tidak_lulus'")->fetch_row()[0];
$total_peng= (int)$conn->query("SELECT COUNT(*) FROM pengumuman")->fetch_row()[0];

// 5 pendaftar terbaru
$pendaftar_baru = [];
$res = $conn->query("SELECT nama_siswa, status, tanggal_daftar FROM siswa ORDER BY tanggal_daftar DESC LIMIT 5");
while ($r = $res->fetch_assoc()) $pendaftar_baru[] = $r;

// 4 pengumuman terbaru
$peng_list = [];
$res2 = $conn->query("SELECT judul, tanggal FROM pengumuman ORDER BY tanggal DESC LIMIT 4");
while ($r = $res2->fetch_assoc()) $peng_list[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beranda — Admin PPDB RA An-Nabil</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
.welcome-banner {
  background: linear-gradient(135deg, #0f172a 0%, #1a3a8f 100%);
  border-radius: var(--radius-lg); padding: 1.75rem 2rem;
  color: #fff; display: flex; align-items: center;
  justify-content: space-between; gap: 1.5rem;
  margin-bottom: 1.4rem; flex-wrap: wrap;
}
.welcome-banner h2 { font-size: 1.3rem; font-weight: 800; margin-bottom: 0.3rem; }
.welcome-banner p  { font-size: 0.83rem; opacity: 0.8; max-width: 460px; line-height: 1.6; }
.wb-icon {
  width: 68px; height: 68px; background: rgba(255,255,255,0.1);
  border-radius: var(--radius-md); display: flex; align-items: center;
  justify-content: center; font-size: 1.9rem; flex-shrink: 0;
}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php include 'sidebar-admin.php'; ?>
  <div class="admin-main">

    <div class="admin-topbar">
      <div class="topbar-left">
        <h1>Dashboard Admin</h1>
        <div class="tb-sub"><?= date('l, d F Y') ?></div>
      </div>
      <div class="topbar-right">
        <span class="tb-badge"><i class="fas fa-shield-alt"></i> Administrator</span>
        <div class="admin-avatar"><?= $inisial ?></div>
      </div>
    </div>

    <div class="admin-page">

      <div class="welcome-banner">
        <div>
          <h2>Halo, <?= htmlspecialchars($nama) ?>! 👋</h2>
          <p>Selamat datang di Panel Admin PPDB RA An-Nabil. Pantau pendaftaran, kelola pengumuman, dan verifikasi data siswa dari sini.</p>
        </div>
        <div class="wb-icon"><i class="fas fa-user-shield"></i></div>
      </div>

      <!-- Statistik -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="m-icon blue"><i class="fas fa-users"></i></div>
          <div>
            <div class="m-label">Total Pendaftar</div>
            <div class="m-value"><?= $total ?></div>
            <div class="m-sub">Semua status</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon yellow"><i class="fas fa-hourglass-half"></i></div>
          <div>
            <div class="m-label">Menunggu Verifikasi</div>
            <div class="m-value"><?= $pending ?></div>
            <div class="m-sub">Perlu diproses</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon green"><i class="fas fa-check-double"></i></div>
          <div>
            <div class="m-label">Diterima</div>
            <div class="m-value"><?= $lulus ?></div>
            <div class="m-sub">Status lulus</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon red"><i class="fas fa-times-circle"></i></div>
          <div>
            <div class="m-label">Tidak Diterima</div>
            <div class="m-value"><?= $tdk_lulus ?></div>
            <div class="m-sub">Status tidak lulus</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon purple"><i class="fas fa-bullhorn"></i></div>
          <div>
            <div class="m-label">Pengumuman</div>
            <div class="m-value"><?= $total_peng ?></div>
            <div class="m-sub">Total diterbitkan</div>
          </div>
        </div>
      </div>

      <div class="grid-2">
        <!-- Pendaftar terbaru -->
        <div class="a-card">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div class="a-card-title">Pendaftar Terbaru</div>
            <a href="verifikasi-admin.php" class="btn btn-outline btn-sm">Lihat Semua</a>
          </div>
          <div class="a-card-sub">5 pendaftaran terakhir</div>
          <?php if ($pendaftar_baru): ?>
            <?php foreach ($pendaftar_baru as $p): ?>
            <div class="r-row">
              <div class="r-dot <?= $p['status']==='pending'?'dot-y':($p['status']==='lulus'?'dot-g':'dot-r') ?>"></div>
              <div>
                <div class="r-name"><?= htmlspecialchars($p['nama_siswa']) ?></div>
                <div class="r-meta">
                  <?= date('d M Y, H:i', strtotime($p['tanggal_daftar'])) ?> ·
                  <span class="badge <?= $p['status']==='pending'?'badge-yellow':($p['status']==='lulus'?'badge-green':'badge-red') ?>">
                    <?= ucfirst(str_replace('_',' ',$p['status'])) ?>
                  </span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state"><i class="fas fa-users"></i>Belum ada pendaftar.</div>
          <?php endif; ?>
        </div>

        <!-- Pengumuman terbaru -->
        <div class="a-card">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div class="a-card-title">Pengumuman Terbaru</div>
            <a href="pengumuman-admin.php" class="btn btn-outline btn-sm">Kelola</a>
          </div>
          <div class="a-card-sub">Terakhir diterbitkan</div>
          <?php if ($peng_list): ?>
            <?php foreach ($peng_list as $pg): ?>
            <div class="r-row">
              <div style="width:28px;height:28px;background:var(--primary-light);color:var(--primary);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:0.72rem;flex-shrink:0">
                <i class="fas fa-bell"></i>
              </div>
              <div>
                <div class="r-name"><?= htmlspecialchars($pg['judul']) ?></div>
                <div class="r-meta"><?= date('d M Y', strtotime($pg['tanggal'])) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state"><i class="fas fa-bell"></i>Belum ada pengumuman.</div>
          <?php endif; ?>
          <div style="margin-top:1rem">
            <a href="pengumuman-admin.php?action=tambah" class="btn btn-primary btn-sm">
              <i class="fas fa-plus"></i> Buat Pengumuman
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>