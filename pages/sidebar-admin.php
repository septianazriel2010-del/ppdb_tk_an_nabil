<?php
// pages/sidebar-admin.php
$ap = $active_page ?? '';
?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <div class="s-icon"><i class="fas fa-graduation-cap"></i></div>
    <div>
      <div class="s-title">RA An-Nabil</div>
      <div class="s-sub">Panel Administrator</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-label">Menu Utama</div>
    <a href="dashboard-admin.php" class="nav-link <?= $ap==='dashboard'?'active':'' ?>">
      <div class="nl-icon"><i class="fas fa-home"></i></div> Beranda
    </a>
    <a href="statistik-admin.php" class="nav-link <?= $ap==='statistik'?'active':'' ?>">
      <div class="nl-icon"><i class="fas fa-chart-bar"></i></div> Statistik
    </a>

    <div class="nav-label">Pendaftaran</div>
    <a href="verifikasi-admin.php" class="nav-link <?= $ap==='verifikasi'?'active':'' ?>">
      <div class="nl-icon"><i class="fas fa-user-check"></i></div> Verifikasi Pendaftar
    </a>

    <div class="nav-label">Konten</div>
    <a href="pengumuman-admin.php" class="nav-link <?= $ap==='pengumuman'?'active':'' ?>">
      <div class="nl-icon"><i class="fas fa-bullhorn"></i></div> Pengumuman
    </a>
    <a href="kontak-admin.php" class="nav-link <?= $ap==='kontak'?'active':'' ?>">
      <div class="nl-icon"><i class="fas fa-address-card"></i></div> Kontak Sekolah
    </a>

    <div class="nav-label">Laporan</div>
    <a href="cetak-data.php" class="nav-link <?= $ap==='cetak'?'active':'' ?>">
      <div class="nl-icon"><i class="fas fa-print"></i></div> Cetak Data
    </a>
  </nav>

  <div class="sidebar-foot">
    <a href="logout.php" class="logout-link">
      <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
  </div>
</aside>