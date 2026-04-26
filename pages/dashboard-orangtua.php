<?php
session_start();
require_once '../functions/functions.php';
$active_page = 'dashboard';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));
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
    border-radius: var(--radius-lg);
    padding: 2rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
  }
  .welcome-banner h2 { font-size: 1.4rem; font-weight: 800; margin-bottom: 0.35rem; }
  .welcome-banner p  { font-size: 0.875rem; opacity: 0.88; max-width: 480px; line-height: 1.6; }
  .welcome-banner .banner-icon {
    width: 72px; height: 72px;
    background: rgba(255,255,255,0.15);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
  }

  .quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 1rem;
    margin-bottom: 1.75rem;
  }
  .quick-link-card {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1.25rem 1rem;
    text-align: center;
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.18s;
    display: flex; flex-direction: column; align-items: center; gap: 0.6rem;
  }
  .quick-link-card:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-blue);
    transform: translateY(-2px);
    color: var(--primary);
  }
  .quick-link-card .ql-icon {
    width: 44px; height: 44px;
    background: var(--primary-light);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    color: var(--primary);
  }
  .quick-link-card span { font-size: 0.8rem; font-weight: 600; }

  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
  .info-list { list-style: none; display: flex; flex-direction: column; gap: 0.85rem; }
  .info-list li {
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 0.85rem; color: var(--text-secondary);
  }
  .info-list li i {
    width: 32px; height: 32px;
    background: var(--primary-light);
    color: var(--primary);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; flex-shrink: 0;
  }
  .info-list li strong { display: block; color: var(--text-primary); font-size: 0.88rem; }

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
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <!-- Banner -->
    <div class="welcome-banner">
      <div>
        <h2>Halo, <?= htmlspecialchars($nama) ?>!</h2>
        <p>Selamat datang di portal PPDB RA An-Nabil. Gunakan menu navigasi untuk mengakses layanan pendaftaran, memantau status, dan mendapatkan informasi terbaru.</p>
      </div>
      <div class="banner-icon"><i class="fas fa-graduation-cap"></i></div>
    </div>

    <!-- Metrik -->
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-icon blue"><i class="fas fa-file-alt"></i></div>
        <div>
          <div class="metric-label">Status Berkas</div>
          <div class="metric-value" style="font-size:1rem; margin-top:4px;">
            <span class="badge badge-yellow">Diverifikasi</span>
          </div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="metric-label">Dokumen Lengkap</div>
          <div class="metric-value">4 / 5</div>
          <div class="metric-sub">1 dokumen kurang</div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon yellow"><i class="fas fa-calendar-alt"></i></div>
        <div>
          <div class="metric-label">Tahap Saat Ini</div>
          <div class="metric-value" style="font-size:0.95rem; margin-top:4px;">Verifikasi Berkas</div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon red"><i class="fas fa-clock"></i></div>
        <div>
          <div class="metric-label">Batas Pendaftaran</div>
          <div class="metric-value" style="font-size:0.95rem; margin-top:4px;">30 Jun 2025</div>
        </div>
      </div>
    </div>

    <!-- Quick links -->
    <p class="section-label">Akses Cepat</p>
    <div class="quick-links">
      <a href="info-pendaftaran.php" class="quick-link-card">
        <div class="ql-icon"><i class="fas fa-info-circle"></i></div>
        <span>Info Pendaftaran</span>
      </a>
      <a href="pendaftaran.php" class="quick-link-card">
        <div class="ql-icon"><i class="fas fa-file-alt"></i></div>
        <span>Pendaftaran</span>
      </a>
      <a href="status.php" class="quick-link-card">
        <div class="ql-icon"><i class="fas fa-chart-line"></i></div>
        <span>Status</span>
      </a>
      <a href="pengumuman.php" class="quick-link-card">
        <div class="ql-icon"><i class="fas fa-bell"></i></div>
        <span>Pengumuman</span>
      </a>
      <a href="panduan.php" class="quick-link-card">
        <div class="ql-icon"><i class="fas fa-book"></i></div>
        <span>Panduan</span>
      </a>
      <a href="profil.php" class="quick-link-card">
        <div class="ql-icon"><i class="fas fa-user"></i></div>
        <span>Profil Akun</span>
      </a>
    </div>

    <!-- Info & Pengumuman -->
    <div class="grid-2">
      <div class="card">
        <div class="card-title">Informasi Sekolah</div>
        <div class="card-subtitle">Data RA An-Nabil</div>
        <ul class="info-list">
          <li><i class="fas fa-map-marker-alt"></i><div><strong>Alamat</strong>Jl. Contoh No.1, Kota Anda</div></li>
          <li><i class="fas fa-phone"></i><div><strong>Telepon</strong>(021) 000-0000</div></li>
          <li><i class="fas fa-envelope"></i><div><strong>Email</strong>ppdb@ra-annabil.sch.id</div></li>
          <li><i class="fas fa-clock"></i><div><strong>Jam Operasional</strong>Senin–Jumat, 08.00–15.00</div></li>
        </ul>
      </div>

      <div class="card">
        <div class="card-title">Pengumuman Terbaru</div>
        <div class="card-subtitle">Update terakhir</div>
        <ul class="info-list">
          <li><i class="fas fa-bell"></i><div><strong>Verifikasi Berkas Diperpanjang</strong>Batas akhir diperpanjang hingga 30 Juni 2025</div></li>
          <li><i class="fas fa-bell"></i><div><strong>Jadwal Wawancara</strong>Dijadwalkan pada minggu ke-3 Juli 2025</div></li>
          <li><i class="fas fa-bell"></i><div><strong>Pengumuman Hasil</strong>Akan diumumkan 15 Juli 2025</div></li>
        </ul>
      </div>
    </div>
  </div>
</div>
</body>
</html>