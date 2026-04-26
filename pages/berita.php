<?php
require_once '../functions/db.php';
require_once '../functions/auth_orangtua.php';

$active_page = 'berita';
$nama    = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));

// Berita diambil dari tabel pengumuman juga (karena tidak ada tabel berita terpisah di database)
// Ambil semua entri, tampilkan sebagai berita
$berita_list = [];
$res = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal DESC");
if ($res) while ($row = $res->fetch_assoc()) $berita_list[] = $row;
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
    background: var(--bg-white); border: 1px solid var(--border);
    border-radius: var(--radius-lg); overflow: hidden;
    transition: box-shadow 0.18s, transform 0.18s;
    text-decoration: none; color: inherit; display: block;
  }
  .berita-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
  .berita-img {
    width: 100%; height: 160px; overflow: hidden;
    background: var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary); font-size: 2.5rem;
  }
  .berita-img img { width:100%; height:100%; object-fit:cover; }
  .berita-body { padding: 1.1rem; }
  .berita-body .meta { font-size: 0.72rem; color: var(--text-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 0.4rem; }
  .berita-body h4 { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; line-height: 1.4; }
  .berita-body p  { font-size: 0.8rem; color: var(--text-secondary); line-height: 1.5; }
  .berita-footer { padding: 0.75rem 1.1rem; border-top: 1px solid var(--border); font-size: 0.78rem; color: var(--primary); font-weight: 600; display: flex; align-items: center; gap: 0.4rem; }
  .empty-state { text-align:center; padding:3rem; color:var(--text-muted); }
  .empty-state i { font-size:2.5rem; display:block; margin-bottom:1rem; }
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
      <div class="topbar-right"><div class="avatar-circle"><?= $inisial ?></div></div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <?php if (count($berita_list) > 0): ?>
    <div class="berita-grid">
      <?php foreach ($berita_list as $b): ?>
      <div class="berita-card">
        <div class="berita-img">
          <?php if (!empty($b['gambar'])): ?>
            <img src="../uploads/pengumuman/<?= htmlspecialchars($b['gambar']) ?>" alt="<?= htmlspecialchars($b['judul']) ?>">
          <?php else: ?>
            <i class="fas fa-newspaper"></i>
          <?php endif; ?>
        </div>
        <div class="berita-body">
          <div class="meta">
            <i class="fas fa-calendar"></i>
            <?= date('d M Y', strtotime($b['tanggal'])) ?>
          </div>
          <h4><?= htmlspecialchars($b['judul']) ?></h4>
          <p><?= htmlspecialchars(mb_substr(strip_tags($b['isi']), 0, 120)) ?><?= mb_strlen($b['isi']) > 120 ? '...' : '' ?></p>
        </div>
        <div class="berita-footer"><i class="fas fa-arrow-right"></i> Baca selengkapnya</div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card">
      <div class="empty-state">
        <i class="fas fa-newspaper"></i>
        <strong>Belum Ada Berita</strong>
        <p style="margin-top:0.5rem;font-size:0.85rem;">Berita dari sekolah akan tampil di sini.</p>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>
</body>
</html>