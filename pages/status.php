<?php
require_once '../functions/db.php';
require_once '../functions/auth_orangtua.php';

$active_page = 'status';
$user_id  = getUserId($conn);
$nama     = $_SESSION['nama'] ?? 'Orang Tua';
$inisial  = strtoupper(substr($nama, 0, 2));

// Ambil data siswa & dokumen
$siswa   = null;
$dokumen = null;

$stmt = $conn->prepare("SELECT * FROM siswa WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $siswa = $result->fetch_assoc();
    $d = $conn->prepare("SELECT * FROM dokumen WHERE siswa_id = ? LIMIT 1");
    $d->bind_param("i", $siswa['id']);
    $d->execute();
    $dokumen = $d->get_result()->fetch_assoc();
    $d->close();
}
$stmt->close();

// Helper status dokumen
function status_badge($nilai) {
    if (!empty($nilai))
        return '<span class="badge badge-green"><i class="fas fa-check"></i> Diterima</span>';
    else
        return '<span class="badge badge-red"><i class="fas fa-times"></i> Belum Upload</span>';
}

// Tentukan tahap saat ini berdasarkan data
function tahap_saat_ini($siswa, $dokumen) {
    if (!$siswa) return 0;
    // Tahap 1: sudah daftar
    // Tahap 2: sudah upload dokumen
    // Tahap 3: sedang verifikasi (status pending)
    // Tahap 4: pengumuman (lulus/tidak_lulus)
    if ($siswa['status'] === 'lulus' || $siswa['status'] === 'tidak_lulus') return 4;
    if ($dokumen && (!empty($dokumen['akta_kelahiran']) || !empty($dokumen['foto']))) return 3;
    if ($siswa) return 2;
    return 1;
}

$tahap = tahap_saat_ini($siswa, $dokumen);
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
    border-radius: var(--radius-lg); padding: 1.75rem; color: #fff;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;
  }
  .status-hero .nomor { font-size: 0.8rem; opacity: 0.8; margin-bottom: 4px; }
  .status-hero h3 { font-size: 1.2rem; font-weight: 800; }
  .pill { background: rgba(255,255,255,0.2); padding: 0.45rem 1.1rem; border-radius: 99px; font-size: 0.82rem; font-weight: 700; border: 1.5px solid rgba(255,255,255,0.4); }

  .empty-state-box {
    background: var(--bg-white); border: 2px dashed var(--border);
    border-radius: var(--radius-lg); padding: 3rem 2rem;
    text-align: center;
  }
  .empty-state-box .icon { font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem; }
  .empty-state-box h3 { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; }
  .empty-state-box p  { font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.5rem; }

  /* Timeline */
  .timeline { position: relative; padding-left: 2rem; }
  .timeline::before { content:''; position:absolute; left:10px; top:0; bottom:0; width:2px; background:linear-gradient(to bottom, var(--primary), var(--border)); }
  .tl-item { position: relative; padding-bottom: 1.75rem; }
  .tl-item:last-child { padding-bottom: 0; }
  .tl-dot { position:absolute; left:-2rem; top:0; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.65rem; border:3px solid var(--bg-white); }
  .tl-dot.done    { background:var(--success); color:#fff; box-shadow:0 0 0 2px var(--success-bg); }
  .tl-dot.current { background:var(--primary); color:#fff; box-shadow:0 0 0 3px var(--primary-mid); }
  .tl-dot.pending { background:#f1f5f9; color:var(--text-muted); box-shadow:0 0 0 2px var(--border); }
  .tl-body { background:var(--bg-white); border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem 1.25rem; margin-left:0.5rem; }
  .tl-item.current .tl-body { border-color:var(--primary-mid); }
  .tl-body h4 { font-size:0.9rem; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
  .tl-body .tl-date { font-size:0.78rem; color:var(--text-muted); display:flex; align-items:center; gap:0.3rem; margin-bottom:6px; }
  .tl-body p { font-size:0.82rem; color:var(--text-secondary); line-height:1.5; }

  .doc-table { width:100%; border-collapse:collapse; font-size:0.85rem; }
  .doc-table th { text-align:left; padding:0.6rem 1rem; font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid var(--border); }
  .doc-table td { padding:0.75rem 1rem; border-bottom:1px solid var(--bg-surface); color:var(--text-secondary); }
  .doc-table tr:last-child td { border-bottom:none; }
  .doc-table td:first-child { color:var(--text-primary); font-weight:500; }
  .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
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
        <p>Pantau perkembangan pendaftaran Anda</p>
      </div>
      <div class="topbar-right"><div class="avatar-circle"><?= $inisial ?></div></div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <?php if (!$siswa): ?>
    <!-- Belum daftar -->
    <div class="empty-state-box">
      <div class="icon"><i class="fas fa-clipboard-list"></i></div>
      <h3>Anda Belum Mendaftarkan Calon Peserta Didik</h3>
      <p>Untuk memantau status pendaftaran, Anda perlu mengisi formulir pendaftaran terlebih dahulu. Pastikan data yang diisi sesuai dengan dokumen resmi.</p>
      <a href="pendaftaran.php" class="btn-back" style="margin-bottom:0;display:inline-flex;">
        <i class="fas fa-file-alt"></i> Daftarkan Sekarang
      </a>
    </div>

    <?php else: ?>
    <!-- Hero Status -->
    <div class="status-hero">
      <div>
        <div class="nomor">No. Pendaftaran: <strong>PPDB-2025-<?= str_pad($siswa['id'], 5, '0', STR_PAD_LEFT) ?></strong></div>
        <h3><?= htmlspecialchars($nama) ?></h3>
        <div style="font-size:0.82rem;opacity:0.85;margin-top:4px;">Calon Peserta Didik: <strong><?= htmlspecialchars($siswa['nama_siswa']) ?></strong></div>
      </div>
      <?php if ($siswa['status'] === 'pending'): ?>
        <div class="pill"><i class="fas fa-spinner fa-spin"></i> Sedang Diverifikasi</div>
      <?php elseif ($siswa['status'] === 'lulus'): ?>
        <div class="pill" style="background:rgba(22,163,74,0.25);border-color:rgba(22,163,74,0.5);"><i class="fas fa-check-circle"></i> Diterima</div>
      <?php else: ?>
        <div class="pill" style="background:rgba(220,38,38,0.2);border-color:rgba(220,38,38,0.4);"><i class="fas fa-times-circle"></i> Tidak Diterima</div>
      <?php endif; ?>
    </div>

    <!-- Metrik -->
    <div class="metrics-grid" style="margin-bottom:1.5rem;">
      <div class="metric-card">
        <div class="metric-icon green"><i class="fas fa-file-check"></i></div>
        <div>
          <div class="metric-label">Dokumen Diterima</div>
          <?php
            $dok_ada = 0;
            if ($dokumen) {
                if (!empty($dokumen['akta_kelahiran'])) $dok_ada++;
                if (!empty($dokumen['kartu_keluarga'])) $dok_ada++;
                if (!empty($dokumen['foto']))           $dok_ada++;
            }
          ?>
          <div class="metric-value"><?= $dok_ada ?></div>
          <div class="metric-sub">dari 3 dokumen</div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon yellow"><i class="fas fa-hourglass-half"></i></div>
        <div>
          <div class="metric-label">Tahap</div>
          <div class="metric-value" style="font-size:0.9rem;margin-top:4px;">
            <?= ['—','Terdaftar','Dokumen Masuk','Verifikasi','Pengumuman'][$tahap] ?? '—' ?>
          </div>
          <div class="metric-sub">Tahap <?= $tahap ?> dari 4</div>
        </div>
      </div>
      <div class="metric-card">
        <div class="metric-icon blue"><i class="fas fa-calendar-check"></i></div>
        <div>
          <div class="metric-label">Jenis Kelamin</div>
          <div class="metric-value" style="font-size:0.9rem;margin-top:4px;">
            <?= $siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
          </div>
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
      <!-- Alur proses (berdasarkan data nyata) -->
      <div class="card">
        <div class="card-title">Alur Proses Pendaftaran</div>
        <div class="card-subtitle">Progres berdasarkan data Anda</div>
        <div class="timeline">

          <!-- Tahap 1: Pendaftaran Online -->
          <div class="tl-item <?= $tahap >= 2 ? '' : 'current' ?>">
            <div class="tl-dot <?= $tahap >= 2 ? 'done' : 'current' ?>">
              <?= $tahap >= 2 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-spinner fa-spin"></i>' ?>
            </div>
            <div class="tl-body">
              <span class="badge <?= $tahap >= 2 ? 'badge-green' : 'badge-blue' ?>" style="margin-bottom:6px;"><?= $tahap >= 2 ? 'Selesai' : 'Proses' ?></span>
              <h4>Pendaftaran Online</h4>
              <p>Formulir pendaftaran telah diisi dan dikirimkan ke sistem.</p>
            </div>
          </div>

          <!-- Tahap 2: Upload Dokumen -->
          <div class="tl-item <?= $tahap === 2 ? 'current' : '' ?>">
            <div class="tl-dot <?= $tahap > 2 ? 'done' : ($tahap === 2 ? 'current' : 'pending') ?>">
              <?php if ($tahap > 2) echo '<i class="fas fa-check"></i>';
                    elseif ($tahap === 2) echo '<i class="fas fa-spinner fa-spin"></i>';
                    else echo '<i class="fas fa-paperclip"></i>'; ?>
            </div>
            <div class="tl-body">
              <span class="badge <?= $tahap > 2 ? 'badge-green' : ($tahap === 2 ? 'badge-yellow' : 'badge-gray') ?>" style="margin-bottom:6px;">
                <?= $tahap > 2 ? 'Selesai' : ($tahap === 2 ? 'Proses' : 'Menunggu') ?>
              </span>
              <h4>Upload Dokumen</h4>
              <p><?= $dok_ada ?> dari 3 dokumen berhasil diunggah.</p>
            </div>
          </div>

          <!-- Tahap 3: Verifikasi -->
          <div class="tl-item <?= $tahap === 3 ? 'current' : '' ?>">
            <div class="tl-dot <?= $tahap > 3 ? 'done' : ($tahap === 3 ? 'current' : 'pending') ?>">
              <?php if ($tahap > 3) echo '<i class="fas fa-check"></i>';
                    elseif ($tahap === 3) echo '<i class="fas fa-spinner fa-spin"></i>';
                    else echo '<i class="fas fa-search"></i>'; ?>
            </div>
            <div class="tl-body">
              <span class="badge <?= $tahap > 3 ? 'badge-green' : ($tahap === 3 ? 'badge-yellow' : 'badge-gray') ?>" style="margin-bottom:6px;">
                <?= $tahap > 3 ? 'Selesai' : ($tahap === 3 ? 'Sedang Berlangsung' : 'Menunggu') ?>
              </span>
              <h4>Verifikasi Berkas</h4>
              <p>Panitia sedang memverifikasi kelengkapan dan keabsahan dokumen Anda.</p>
            </div>
          </div>

          <!-- Tahap 4: Pengumuman -->
          <div class="tl-item <?= $tahap === 4 ? 'current' : '' ?>">
            <div class="tl-dot <?= $tahap === 4 ? 'done' : 'pending' ?>">
              <?= $tahap === 4 ? '<i class="fas fa-bullhorn"></i>' : '<i class="fas fa-bullhorn"></i>' ?>
            </div>
            <div class="tl-body">
              <span class="badge <?= $tahap === 4 ? ($siswa['status'] === 'lulus' ? 'badge-green' : 'badge-red') : 'badge-gray' ?>" style="margin-bottom:6px;">
                <?php if ($tahap === 4):
                    echo $siswa['status'] === 'lulus' ? 'Diterima ✓' : 'Tidak Diterima';
                  else: echo 'Menunggu'; endif; ?>
              </span>
              <h4>Pengumuman Hasil</h4>
              <p>
                <?php if ($tahap < 4): ?>
                  Hasil seleksi akan diumumkan setelah proses verifikasi selesai.
                <?php elseif ($siswa['status'] === 'lulus'): ?>
                  Selamat! Calon peserta didik dinyatakan <strong>DITERIMA</strong> di RA An-Nabil.
                <?php else: ?>
                  Mohon maaf, calon peserta didik belum dapat diterima pada tahun ini.
                <?php endif; ?>
              </p>
            </div>
          </div>

        </div>
      </div>

      <!-- Status Dokumen dari database -->
      <div class="card">
        <div class="card-title">Status Dokumen</div>
        <div class="card-subtitle">Kelengkapan berkas</div>
        <table class="doc-table">
          <thead>
            <tr><th>Dokumen</th><th>Status</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Akta Kelahiran</td>
              <td><?= status_badge($dokumen['akta_kelahiran'] ?? '') ?></td>
            </tr>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Kartu Keluarga</td>
              <td><?= status_badge($dokumen['kartu_keluarga'] ?? '') ?></td>
            </tr>
            <tr>
              <td><i class="fas fa-file-alt" style="color:var(--primary);margin-right:6px;"></i>Pas Foto</td>
              <td><?= status_badge($dokumen['foto'] ?? '') ?></td>
            </tr>
          </tbody>
        </table>
        <?php if ($dok_ada < 3): ?>
        <div style="margin-top:1.25rem;">
          <a href="pendaftaran.php" class="btn-back" style="margin-bottom:0;display:inline-flex;">
            <i class="fas fa-upload"></i> Lengkapi Dokumen
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php endif; // end if $siswa ?>

  </div>
</div>
<script>
  const batas = new Date('2025-06-30');
  const hari = Math.max(0, Math.ceil((batas - new Date()) / 86400000));
  const el = document.getElementById('sisaHari');
  if (el) el.textContent = hari;
</script>
</body>
</html>