<?php
session_start();
require_once '../functions/functions.php';
$active_page = 'info';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Info Pendaftaran — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .dokumen-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
  .dokumen-card {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1.1rem 1.25rem;
    display: flex; align-items: flex-start; gap: 1rem;
    transition: border-color 0.18s, box-shadow 0.18s;
  }
  .dokumen-card:hover { border-color: var(--primary-mid); box-shadow: var(--shadow-md); }
  .dokumen-icon {
    width: 42px; height: 42px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
  }
  .di-blue   { background: var(--primary-light); color: var(--primary); }
  .di-green  { background: #f0fdf4; color: #16a34a; }
  .di-yellow { background: #fef3c7; color: #d97706; }
  .di-red    { background: #fff1f2; color: #f43f5e; }
  .di-purple { background: #faf5ff; color: #a855f7; }
  .di-orange { background: #fff7ed; color: #f97316; }
  .dokumen-info h4 { font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
  .dokumen-info p  { font-size: 0.78rem; color: var(--text-secondary); line-height: 1.45; }

  /* Timeline jadwal */
  .jadwal-wrap { position: relative; padding-left: 2rem; }
  .jadwal-wrap::before {
    content: ''; position: absolute;
    left: 10px; top: 0; bottom: 0; width: 2px;
    background: linear-gradient(to bottom, var(--primary), var(--border));
  }
  .jd-item { position: relative; padding-bottom: 1.75rem; }
  .jd-item:last-child { padding-bottom: 0; }
  .jd-dot {
    position: absolute; left: -2rem; top: 2px;
    width: 22px; height: 22px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem;
    border: 3px solid var(--bg-white);
  }
  .jd-dot.done    { background: var(--success); color: #fff; box-shadow: 0 0 0 2px var(--success-bg); }
  .jd-dot.current { background: var(--primary); color: #fff; box-shadow: 0 0 0 3px var(--primary-mid); }
  .jd-dot.pending { background: #f1f5f9; color: var(--text-muted); box-shadow: 0 0 0 2px var(--border); }
  .jd-body {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 0.9rem 1.1rem;
    margin-left: 0.5rem;
  }
  .jd-item.current .jd-body { border-color: var(--primary-mid); }
  .jd-body h4 { font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 3px; }
  .jd-body .jd-date { font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.3rem; margin-bottom: 5px; }
  .jd-body p { font-size: 0.8rem; color: var(--text-secondary); line-height: 1.5; }

  /* Countdown */
  .countdown-box {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    color: #fff;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
    margin-bottom: 1.5rem;
  }
  .cd-nums { display: flex; gap: 0.75rem; align-items: center; }
  .cd-num { text-align: center; }
  .cd-num span { display: block; font-size: 1.75rem; font-weight: 800; line-height: 1; }
  .cd-num small { font-size: 0.65rem; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.05em; }
  .cd-sep { font-size: 1.5rem; font-weight: 800; opacity: 0.5; padding-bottom: 6px; }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>

  <div class="content-isi">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Info Pendaftaran</h1>
        <p>Syarat, dokumen, dan jadwal PPDB RA An-Nabil</p>
      </div>
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <div class="tab-nav">
      <button class="tab-btn active" onclick="switchTab('syarat', this)">
        <i class="fas fa-file-check"></i> Syarat & Dokumen
      </button>
      <button class="tab-btn" onclick="switchTab('jadwal', this)">
        <i class="fas fa-calendar-alt"></i> Jadwal Pendaftaran
      </button>
    </div>

    <!-- TAB SYARAT -->
    <div class="tab-panel active" id="tab-syarat">
      <div class="section-label">Dokumen yang diperlukan</div>
      <div class="dokumen-grid">

        <div class="dokumen-card">
          <div class="dokumen-icon di-blue"><i class="fas fa-id-card"></i></div>
          <div class="dokumen-info">
            <h4>Akta Kelahiran</h4>
            <p>Fotokopi akta kelahiran calon peserta didik yang telah dilegalisir</p>
            <span class="badge badge-red" style="margin-top:6px;">Wajib</span>
          </div>
        </div>

        <div class="dokumen-card">
          <div class="dokumen-icon di-green"><i class="fas fa-home"></i></div>
          <div class="dokumen-info">
            <h4>Kartu Keluarga (KK)</h4>
            <p>Fotokopi KK yang masih berlaku dan sesuai dengan data diri</p>
            <span class="badge badge-red" style="margin-top:6px;">Wajib</span>
          </div>
        </div>

        <div class="dokumen-card">
          <div class="dokumen-icon di-yellow"><i class="fas fa-id-badge"></i></div>
          <div class="dokumen-info">
            <h4>KTP Orang Tua/Wali</h4>
            <p>Fotokopi KTP ayah dan ibu atau wali yang sah</p>
            <span class="badge badge-red" style="margin-top:6px;">Wajib</span>
          </div>
        </div>

        <div class="dokumen-card">
          <div class="dokumen-icon di-red"><i class="fas fa-camera"></i></div>
          <div class="dokumen-info">
            <h4>Pas Foto Terbaru</h4>
            <p>Ukuran 3×4 cm sebanyak 3 lembar, latar belakang merah</p>
            <span class="badge badge-red" style="margin-top:6px;">Wajib</span>
          </div>
        </div>

        <div class="dokumen-card">
          <div class="dokumen-icon di-orange"><i class="fas fa-graduation-cap"></i></div>
          <div class="dokumen-info">
            <h4>Ijazah / Surat Keterangan TK</h4>
            <p>Bagi yang sebelumnya bersekolah di TK/PAUD (jika ada)</p>
            <span class="badge badge-green" style="margin-top:6px;">Opsional</span>
          </div>
        </div>

      </div>

      <div class="info-box">
        <i class="fas fa-circle-info"></i>
        <div>
          <strong>Catatan penting:</strong> Semua dokumen wajib diserahkan dalam bentuk fotokopi <strong>dan</strong> dokumen asli untuk diverifikasi saat pendaftaran langsung. Pastikan semua dokumen dalam kondisi jelas dan tidak rusak.
        </div>
      </div>
    </div>

    <!-- TAB JADWAL -->
    <div class="tab-panel" id="tab-jadwal">

      <div class="countdown-box">
        <div>
          <div style="font-size:0.8rem; opacity:0.8; margin-bottom:3px;"><i class="fas fa-clock"></i> Batas Akhir Pendaftaran</div>
          <div style="font-size:1rem; font-weight:700;">30 Juni 2025</div>
        </div>
        <div class="cd-nums">
          <div class="cd-num"><span id="cd-h">--</span><small>Hari</small></div>
          <div class="cd-sep">:</div>
          <div class="cd-num"><span id="cd-j">--</span><small>Jam</small></div>
          <div class="cd-sep">:</div>
          <div class="cd-num"><span id="cd-m">--</span><small>Menit</small></div>
          <div class="cd-sep">:</div>
          <div class="cd-num"><span id="cd-d">--</span><small>Detik</small></div>
        </div>
      </div>

      <div class="section-label">Tahapan pendaftaran</div>
      <div class="jadwal-wrap">

        <div class="jd-item">
          <div class="jd-dot done"><i class="fas fa-check"></i></div>
          <div class="jd-body">
            <span class="badge badge-green" style="margin-bottom:6px;">Selesai</span>
            <h4>Sosialisasi & Pengumuman PPDB</h4>
            <div class="jd-date"><i class="fas fa-calendar"></i> 1 – 10 Januari 2025</div>
            <p>Penyebaran informasi PPDB melalui website, media sosial, dan pengumuman di sekolah.</p>
          </div>
        </div>

        <div class="jd-item">
          <div class="jd-dot done"><i class="fas fa-check"></i></div>
          <div class="jd-body">
            <span class="badge badge-green" style="margin-bottom:6px;">Selesai</span>
            <h4>Pendaftaran Online & Pengumpulan Berkas</h4>
            <div class="jd-date"><i class="fas fa-calendar"></i> 15 Januari – 28 Februari 2025</div>
            <p>Calon peserta didik mendaftarkan diri secara online dan mengumpulkan dokumen.</p>
          </div>
        </div>

        <div class="jd-item current">
          <div class="jd-dot current"><i class="fas fa-spinner fa-spin"></i></div>
          <div class="jd-body">
            <span class="badge badge-yellow" style="margin-bottom:6px;">Berlangsung</span>
            <h4>Verifikasi & Seleksi Berkas</h4>
            <div class="jd-date"><i class="fas fa-calendar"></i> 1 Maret – 30 Juni 2025</div>
            <p>Panitia memverifikasi kelengkapan dan keabsahan berkas pendaftaran.</p>
          </div>
        </div>

        <div class="jd-item">
          <div class="jd-dot pending"><i class="fas fa-bullhorn"></i></div>
          <div class="jd-body">
            <span class="badge badge-gray" style="margin-bottom:6px;">Akan Datang</span>
            <h4>Pengumuman Hasil Seleksi</h4>
            <div class="jd-date"><i class="fas fa-calendar"></i> 15 Juli 2025</div>
            <p>Hasil seleksi diumumkan melalui website dan papan pengumuman sekolah.</p>
          </div>
        </div>

        <div class="jd-item">
          <div class="jd-dot pending"><i class="fas fa-file-signature"></i></div>
          <div class="jd-body">
            <span class="badge badge-gray" style="margin-bottom:6px;">Akan Datang</span>
            <h4>Daftar Ulang & Orientasi</h4>
            <div class="jd-date"><i class="fas fa-calendar"></i> 16 – 31 Juli 2025</div>
            <p>Peserta yang diterima melakukan daftar ulang dan mengikuti orientasi siswa baru.</p>
          </div>
        </div>

        <div class="jd-item">
          <div class="jd-dot pending"><i class="fas fa-school"></i></div>
          <div class="jd-body">
            <span class="badge badge-gray" style="margin-bottom:6px;">Akan Datang</span>
            <h4>Tahun Ajaran Baru Dimulai</h4>
            <div class="jd-date"><i class="fas fa-calendar"></i> 1 Agustus 2025</div>
            <p>Kegiatan belajar mengajar tahun ajaran 2025/2026 resmi dimulai.</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
function switchTab(id, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + id).classList.add('active');
  btn.classList.add('active');
}

function updateCountdown() {
  const diff = new Date('2025-06-30T23:59:59') - new Date();
  if (diff <= 0) { ['cd-h','cd-j','cd-m','cd-d'].forEach(id => document.getElementById(id).textContent='00'); return; }
  const pad = n => String(n).padStart(2,'0');
  document.getElementById('cd-h').textContent = pad(Math.floor(diff/86400000));
  document.getElementById('cd-j').textContent = pad(Math.floor((diff%86400000)/3600000));
  document.getElementById('cd-m').textContent = pad(Math.floor((diff%3600000)/60000));
  document.getElementById('cd-d').textContent = pad(Math.floor((diff%60000)/1000));
}
updateCountdown(); setInterval(updateCountdown, 1000);
</script>
</body>
</html>