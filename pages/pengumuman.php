<?php
session_start();
require_once '../functions/functions.php';
$active_page = 'pengumuman';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));

$pengumuman = [
  ['judul'=>'Perpanjangan Batas Pendaftaran', 'tanggal'=>'20 Mei 2025', 'tipe'=>'penting', 'isi'=>'Batas akhir pengumpulan berkas diperpanjang hingga 30 Juni 2025. Segera lengkapi dokumen Anda.'],
  ['judul'=>'Jadwal Wawancara Orang Tua', 'tanggal'=>'15 Mei 2025', 'tipe'=>'info', 'isi'=>'Wawancara orang tua dijadwalkan pada minggu ke-3 Juli 2025. Detail jadwal akan dikirim via WhatsApp.'],
  ['judul'=>'Pengumuman Hasil Seleksi', 'tanggal'=>'10 Mei 2025', 'tipe'=>'info', 'isi'=>'Hasil seleksi PPDB 2025/2026 akan diumumkan pada 15 Juli 2025 pukul 09.00 WIB melalui portal ini.'],
  ['judul'=>'Libur Operasional Kantor', 'tanggal'=>'5 Mei 2025', 'tipe'=>'umum', 'isi'=>'Kantor PPDB libur pada tanggal 29 Mei 2025 dalam rangka Hari Raya Waisak. Layanan online tetap aktif.'],
  ['judul'=>'Sosialisasi PPDB 2025', 'tanggal'=>'1 Januari 2025', 'tipe'=>'umum', 'isi'=>'Kegiatan sosialisasi PPDB telah resmi dibuka. Informasi lengkap dapat diakses melalui halaman Info Pendaftaran.'],
];
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
  .peng-list { display: flex; flex-direction: column; gap: 1rem; }
  .peng-card {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1.1rem 1.25rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    transition: box-shadow 0.18s, border-color 0.18s;
  }
  .peng-card:hover { box-shadow: var(--shadow-md); border-color: var(--primary-mid); }
  .peng-card.penting { border-left: 4px solid var(--danger); }
  .peng-card.info    { border-left: 4px solid var(--primary); }
  .peng-card.umum    { border-left: 4px solid var(--text-muted); }
  .peng-icon {
    width: 40px; height: 40px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
  }
  .peng-icon.penting { background: var(--danger-bg); color: var(--danger); }
  .peng-icon.info    { background: var(--primary-light); color: var(--primary); }
  .peng-icon.umum    { background: #f1f5f9; color: var(--text-muted); }
  .peng-body h4 { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
  .peng-body .meta { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 0.4rem; }
  .peng-body p { font-size: 0.83rem; color: var(--text-secondary); line-height: 1.5; }

  .filter-bar { display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
  .filter-btn {
    padding: 0.4rem 1rem;
    border-radius: 99px;
    border: 1.5px solid var(--border);
    background: var(--bg-white);
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    font-family: inherit;
    transition: all 0.18s;
  }
  .filter-btn.active, .filter-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>

  <div class="content-isi">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Pengumuman</h1>
        <p>Informasi dan pemberitahuan resmi dari sekolah</p>
      </div>
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <div class="filter-bar">
      <button class="filter-btn active" onclick="filterPeng('semua',this)">Semua</button>
      <button class="filter-btn" onclick="filterPeng('penting',this)">Penting</button>
      <button class="filter-btn" onclick="filterPeng('info',this)">Informasi</button>
      <button class="filter-btn" onclick="filterPeng('umum',this)">Umum</button>
    </div>

    <div class="peng-list" id="pengList">
      <?php foreach($pengumuman as $p): ?>
      <div class="peng-card <?= $p['tipe'] ?>" data-tipe="<?= $p['tipe'] ?>">
        <div class="peng-icon <?= $p['tipe'] ?>">
          <i class="fas <?= $p['tipe']==='penting' ? 'fa-exclamation-triangle' : ($p['tipe']==='info' ? 'fa-info-circle' : 'fa-bell') ?>"></i>
        </div>
        <div class="peng-body">
          <div class="meta">
            <i class="fas fa-calendar"></i> <?= $p['tanggal'] ?>
            <?php if($p['tipe']==='penting'): ?>
            <span class="badge badge-red">Penting</span>
            <?php elseif($p['tipe']==='info'): ?>
            <span class="badge badge-blue">Info</span>
            <?php else: ?>
            <span class="badge badge-gray">Umum</span>
            <?php endif; ?>
          </div>
          <h4><?= htmlspecialchars($p['judul']) ?></h4>
          <p><?= htmlspecialchars($p['isi']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function filterPeng(tipe, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.peng-card').forEach(card => {
    card.style.display = (tipe === 'semua' || card.dataset.tipe === tipe) ? 'flex' : 'none';
  });
}
</script>
</body>
</html>