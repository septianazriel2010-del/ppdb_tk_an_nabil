<?php
session_start();
require_once '../functions/functions.php';
$active_page = 'berita';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Berita — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .berita-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem; }
  .berita-card {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: box-shadow 0.18s, transform 0.18s;
    text-decoration: none;
    color: inherit;
    display: block;
  }
  .berita-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
  .berita-img {
    width: 100%; height: 150px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem;
  }
  .berita-img.biru   { background: var(--primary-light); }
  .berita-img.hijau  { background: #f0fdf4; }
  .berita-img.kuning { background: var(--warning-bg); }
  .berita-body { padding: 1.1rem; }
  .berita-body .meta { font-size: 0.72rem; color: var(--text-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 0.4rem; }
  .berita-body h4 { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; line-height: 1.4; }
  .berita-body p { font-size: 0.8rem; color: var(--text-secondary); line-height: 1.5; }
  .berita-footer { padding: 0.75rem 1.1rem; border-top: 1px solid var(--border); font-size: 0.78rem; color: var(--primary); font-weight: 600; display: flex; align-items: center; gap: 0.4rem; }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>

  <div class="content-isi">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Berita Sekolah</h1>
        <p>Kabar terbaru dari RA An-Nabil</p>
      </div>
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <div class="berita-grid">
      <a href="#" class="berita-card">
        <div class="berita-img biru">🎓</div>
        <div class="berita-body">
          <div class="meta"><i class="fas fa-calendar"></i> 20 Mei 2025 &bull; <span class="badge badge-blue">Akademik</span></div>
          <h4>RA An-Nabil Raih Akreditasi A dari BAN PAUD</h4>
          <p>RA An-Nabil berhasil meraih akreditasi A dari Badan Akreditasi Nasional PAUD dan PNF. Pencapaian ini mencerminkan komitmen sekolah terhadap kualitas pendidikan.</p>
        </div>
        <div class="berita-footer"><i class="fas fa-arrow-right"></i> Baca selengkapnya</div>
      </a>

      <a href="#" class="berita-card">
        <div class="berita-img hijau">🌱</div>
        <div class="berita-body">
          <div class="meta"><i class="fas fa-calendar"></i> 12 Mei 2025 &bull; <span class="badge badge-green">Kegiatan</span></div>
          <h4>Program Pesantren Kilat Ramadan 2025 Sukses Digelar</h4>
          <p>Lebih dari 120 santri mengikuti program pesantren kilat yang berlangsung selama 5 hari penuh dengan berbagai kegiatan edukatif dan spiritual.</p>
        </div>
        <div class="berita-footer"><i class="fas fa-arrow-right"></i> Baca selengkapnya</div>
      </a>

      <a href="#" class="berita-card">
        <div class="berita-img kuning">🏆</div>
        <div class="berita-body">
          <div class="meta"><i class="fas fa-calendar"></i> 5 Mei 2025 &bull; <span class="badge badge-yellow">Prestasi</span></div>
          <h4>Siswa RA An-Nabil Juara 1 Lomba Tahfidz Tingkat Kota</h4>
          <p>Alhamdulillah, dua siswa RA An-Nabil berhasil meraih juara pertama lomba tahfidz Quran tingkat kota yang diselenggarakan oleh Kemenag setempat.</p>
        </div>
        <div class="berita-footer"><i class="fas fa-arrow-right"></i> Baca selengkapnya</div>
      </a>

      <a href="#" class="berita-card">
        <div class="berita-img biru">📚</div>
        <div class="berita-body">
          <div class="meta"><i class="fas fa-calendar"></i> 28 April 2025 &bull; <span class="badge badge-blue">Akademik</span></div>
          <h4>Kurikulum Merdeka Belajar Resmi Diterapkan di RA An-Nabil</h4>
          <p>Mulai tahun ajaran 2025/2026, RA An-Nabil resmi mengimplementasikan Kurikulum Merdeka sesuai arahan Kemendikbud Ristek.</p>
        </div>
        <div class="berita-footer"><i class="fas fa-arrow-right"></i> Baca selengkapnya</div>
      </a>

      <a href="#" class="berita-card">
        <div class="berita-img hijau">🤝</div>
        <div class="berita-body">
          <div class="meta"><i class="fas fa-calendar"></i> 15 April 2025 &bull; <span class="badge badge-green">Kerjasama</span></div>
          <h4>MOU dengan Puskesmas Setempat untuk Pemeriksaan Kesehatan Rutin</h4>
          <p>RA An-Nabil menandatangani MOU dengan puskesmas untuk program pemeriksaan kesehatan rutin bagi seluruh peserta didik setiap semester.</p>
        </div>
        <div class="berita-footer"><i class="fas fa-arrow-right"></i> Baca selengkapnya</div>
      </a>

      <a href="#" class="berita-card">
        <div class="berita-img kuning">🎨</div>
        <div class="berita-body">
          <div class="meta"><i class="fas fa-calendar"></i> 1 April 2025 &bull; <span class="badge badge-yellow">Kegiatan</span></div>
          <h4>Pameran Karya Seni Anak-Anak RA An-Nabil 2025</h4>
          <p>Ratusan karya seni anak-anak dipamerkan dalam acara tahunan Art Exhibition yang dihadiri oleh orang tua dan tamu undangan spesial.</p>
        </div>
        <div class="berita-footer"><i class="fas fa-arrow-right"></i> Baca selengkapnya</div>
      </a>
    </div>
  </div>
</div>
</body>
</html>