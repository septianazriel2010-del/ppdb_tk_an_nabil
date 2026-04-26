<?php
session_start();
require_once '../functions/functions.php';
$active_page = 'status';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Pendaftaran — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .status-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }
  .status-hero .nomor { font-size: 0.8rem; opacity: 0.8; margin-bottom: 4px; }
  .status-hero h3 { font-size: 1.2rem; font-weight: 800; }
  .status-hero .pill {
    background: rgba(255,255,255,0.2);
    padding: 0.45rem 1.1rem;
    border-radius: 99px;
    font-size: 0.82rem;
    font-weight: 700;
    border: 1.5px solid rgba(255,255,255,0.4);
  }

  /* Timeline */
  .timeline { position: relative; padding-left: 2rem; }
  .timeline::before {
    content: '';
    position: absolute;
    left: 10px; top: 0; bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, var(--primary), var(--border));
  }
  .tl-item { position: relative; padding-bottom: 1.75rem; }
  .tl-item:last-child { padding-bottom: 0; }
  .tl-dot {
    position: absolute;
    left: -2rem;
    top: 0;
    width: 22px; height: 22px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem;
    border: 3px solid var(--bg-white);
    box-shadow: 0 0 0 2px var(--border);
  }
  .tl-dot.done    { background: var(--success);  color: #fff; box-shadow: 0 0 0 2px var(--success-bg); }
  .tl-dot.current { background: var(--primary);  color: #fff; box-shadow: 0 0 0 3px var(--primary-mid); }
  .tl-dot.pending { background: #f1f5f9;          color: var(--text-muted); }
  .tl-body {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1rem 1.25rem;
    margin-left: 0.5rem;
  }
  .tl-item.current .tl-body { border-color: var(--primary-mid); }
  .tl-body h4 { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
  .tl-body .tl-date { font-size: 0.78rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.3rem; margin-bottom: 6px; }
  .tl-body p { font-size: 0.82rem; color: var(--text-secondary); line-height: 1.5; }

  /* Dokumen tabel */
  .doc-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
  .doc-table th { text-align: left; padding: 0.6rem 1rem; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border); }
  .doc-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--bg-surface); color: var(--text-secondary); }
  .doc-table tr:last-child td { border-bottom: none; }
  .doc-table td:first-child { color: var(--text-primary); font-weight: 500; }

  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }
  @media(max-width:700px){ .grid-2{ grid-template-columns:1fr; } }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>

  <div class="content-isi">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Status Pendaftaran</h1>
        <p>Pantau perkembangan pendaftaran kamu</p>
      </div>
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <!-- Hero Status -->
    <div class="status-hero">
      <div>
        <div class="nomor">No. Pendaftaran: <strong>PPDB-2025-00123</strong></div>
        <h3><?= htmlspecialchars($nama) ?></h3>
        <div style="font-size:0.82rem; opacity:0.85; margin-top:4px;">Calon Peserta Didik: <strong>Nama Anak</strong></div>
      </div>
      <div class="pill"><i class="fas fa-spinner fa-spin"></i> Sedang Diverifikasi</div>
    </div>

    <!-- Metrik -->
    <div class="metrics-grid" style="margin-bottom:1.5rem;">
      <div class="metric-card">
        <div class="metric-icon green"><i class="fas fa-file-check"></i></div>
        <div>
          <div class="metric-label">Dokumen Diterima</div>
          <div class="metric-value">4</div>
          <div class="metric-sub">dari 5 dokumen</div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon yellow"><i class="fas fa-hourglass-half"></i></div>
        <div>
          <div class="metric-label">Tahap</div>
          <div class="metric-value" style="font-size:0.9rem; margin-top:4px;">Verifikasi</div>
          <div class="metric-sub">Tahap 3 dari 5</div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon blue"><i class="fas fa-calendar-check"></i></div>
        <div>
          <div class="metric-label">Terdaftar Sejak</div>
          <div class="metric-value" style="font-size:0.9rem; margin-top:4px;">15 Jan 2025</div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon red"><i class="fas fa-clock"></i></div>
        <div>
          <div class="metric-label">Sisa Waktu</div>
          <div class="metric-value" id="sisaHari" style="font-size:1.2rem;">--</div>
          <div class="metric-sub">hari lagi</div>
        </div>
      </div>
    </div>

    <div class="grid-2">
      <!-- Timeline -->
      <div class="card">
        <div class="card-title">Alur Proses Pendaftaran</div>
        <div class="card-subtitle">Progres saat ini</div>
        <div class="timeline">
          <div class="tl-item">
            <div class="tl-dot done"><i class="fas fa-check"></i></div>
            <div class="tl-body">
              <span class="badge badge-green" style="margin-bottom:6px;">Selesai</span>
              <h4>Pendaftaran Online</h4>
              <div class="tl-date"><i class="fas fa-calendar"></i> 15 Januari 2025</div>
              <p>Formulir pendaftaran berhasil diisi dan dikirimkan.</p>
            </div>
          </div>
          <div class="tl-item">
            <div class="tl-dot done"><i class="fas fa-check"></i></div>
            <div class="tl-body">
              <span class="badge badge-green" style="margin-bottom:6px;">Selesai</span>
              <h4>Upload Dokumen</h4>
              <div class="tl-date"><i class="fas fa-calendar"></i> 17 Januari 2025</div>
              <p>4 dari 5 dokumen berhasil diunggah.</p>
            </div>
          </div>
          <div class="tl-item current">
            <div class="tl-dot current"><i class="fas fa-spinner fa-spin"></i></div>
            <div class="tl-body">
              <span class="badge badge-yellow" style="margin-bottom:6px;">Proses</span>
              <h4>Verifikasi Berkas</h4>
              <div class="tl-date"><i class="fas fa-calendar"></i> Estimasi: Maret – Juni 2025</div>
              <p>Panitia sedang memverifikasi kelengkapan dokumen Anda.</p>
            </div>
          </div>
          <div class="tl-item">
            <div class="tl-dot pending"><i class="fas fa-bullhorn"></i></div>
            <div class="tl-body">
              <span class="badge badge-gray" style="margin-bottom:6px;">Menunggu</span>
              <h4>Pengumuman Hasil</h4>
              <div class="tl-date"><i class="fas fa-calendar"></i> 15 Juli 2025</div>
              <p>Hasil seleksi akan diumumkan secara online.</p>
            </div>
          </div>
          <div class="tl-item">
            <div class="tl-dot pending"><i class="fas fa-school"></i></div>
            <div class="tl-body">
              <span class="badge badge-gray" style="margin-bottom:6px;">Menunggu</span>
              <h4>Daftar Ulang</h4>
              <div class="tl-date"><i class="fas fa-calendar"></i> 16–31 Juli 2025</div>
              <p>Peserta yang diterima melakukan daftar ulang.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Dokumen status -->
      <div class="card">
        <div class="card-title">Status Dokumen</div>
        <div class="card-subtitle">Kelengkapan berkas</div>
        <table class="doc-table">
          <thead>
            <tr>
              <th>Dokumen</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Akta Kelahiran</td>
              <td><span class="badge badge-green"><i class="fas fa-check"></i> Diterima</span></td>
            </tr>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Kartu Keluarga</td>
              <td><span class="badge badge-green"><i class="fas fa-check"></i> Diterima</span></td>
            </tr>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>KTP Orang Tua</td>
              <td><span class="badge badge-green"><i class="fas fa-check"></i> Diterima</span></td>
            </tr>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Pas Foto</td>
              <td><span class="badge badge-yellow"><i class="fas fa-clock"></i> Diverifikasi</span></td>
            </tr>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Ijazah TK</td>
              <td><span class="badge badge-red"><i class="fas fa-times"></i> Belum Upload</span></td>
            </tr>
          </tbody>
        </table>
        <div style="margin-top:1.25rem;">
          <a href="pendaftaran.php" class="btn-back" style="margin-bottom:0;display:inline-flex;">
            <i class="fas fa-upload"></i> Upload Dokumen Kurang
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const batas = new Date('2025-06-30');
  const hari = Math.max(0, Math.ceil((batas - new Date()) / 86400000));
  document.getElementById('sisaHari').textContent = hari;
</script>
</body>
</html>