<?php
session_start();
require_once '../functions/functions.php';
$active_page = 'panduan';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panduan — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .panduan-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start; }
  .panduan-nav { position: sticky; top: 1.5rem; }
  .panduan-nav ul { list-style: none; }
  .panduan-nav ul li a {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.6rem 0.85rem;
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.18s;
    margin-bottom: 2px;
  }
  .panduan-nav ul li a:hover { background: var(--primary-light); color: var(--primary); }
  .panduan-nav ul li a.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }

  .step-card {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
  }
  .step-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
  .step-num {
    width: 36px; height: 36px;
    background: var(--primary);
    color: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem;
    font-weight: 800;
    flex-shrink: 0;
  }
  .step-header h3 { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
  .step-content p { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.65; margin-bottom: 0.75rem; }
  .step-content ul { padding-left: 1.25rem; }
  .step-content ul li { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 4px; }

  .faq-item { border: 1px solid var(--border); border-radius: var(--radius-md); margin-bottom: 0.75rem; overflow: hidden; }
  .faq-q {
    padding: 0.9rem 1.1rem;
    display: flex; align-items: center; justify-content: space-between;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    background: var(--bg-white);
    transition: background 0.18s;
    gap: 0.5rem;
  }
  .faq-q:hover { background: var(--primary-light); color: var(--primary); }
  .faq-q i.chev { font-size: 0.75rem; flex-shrink: 0; transition: transform 0.2s; }
  .faq-a {
    display: none;
    padding: 0 1.1rem 1rem;
    font-size: 0.84rem;
    color: var(--text-secondary);
    line-height: 1.6;
    background: var(--bg-surface);
  }
  .faq-item.open .faq-a { display: block; }
  .faq-item.open .chev { transform: rotate(180deg); }

  @media(max-width:700px){ .panduan-grid{ grid-template-columns:1fr; } .panduan-nav{ position:static; } }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>

  <div class="content-isi">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Panduan Pendaftaran</h1>
        <p>Langkah-langkah mendaftar di PPDB RA An-Nabil</p>
      </div>
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <div class="panduan-grid">
      <!-- Nav panduan -->
      <div class="panduan-nav card">
        <div class="section-label">Daftar Isi</div>
        <ul>
          <li><a href="#langkah" class="active"><i class="fas fa-list-ol"></i> Langkah Pendaftaran</a></li>
          <li><a href="#faq"><i class="fas fa-question-circle"></i> FAQ</a></li>
          <li><a href="#kontak"><i class="fas fa-headset"></i> Hubungi Kami</a></li>
        </ul>
      </div>

      <div>
        <!-- Langkah -->
        <div id="langkah" style="scroll-margin-top:1rem;">
          <div class="section-label">Langkah Pendaftaran</div>

          <div class="step-card">
            <div class="step-header">
              <div class="step-num">1</div>
              <h3>Buat Akun & Login</h3>
            </div>
            <div class="step-content">
              <p>Daftarkan diri Anda di halaman registrasi portal PPDB menggunakan email aktif yang dapat dihubungi.</p>
              <ul>
                <li>Kunjungi halaman registrasi</li>
                <li>Isi email dan buat kata sandi</li>
                <li>Verifikasi email melalui tautan yang dikirimkan</li>
                <li>Login ke dashboard</li>
              </ul>
            </div>
          </div>

          <div class="step-card">
            <div class="step-header">
              <div class="step-num">2</div>
              <h3>Isi Formulir Pendaftaran</h3>
            </div>
            <div class="step-content">
              <p>Lengkapi formulir data calon peserta didik dan data orang tua dengan teliti dan akurat.</p>
              <ul>
                <li>Data diri anak (nama lengkap, tanggal lahir, jenis kelamin)</li>
                <li>Data orang tua/wali (nama, pekerjaan, kontak)</li>
                <li>Alamat tempat tinggal saat ini</li>
              </ul>
              <div class="info-box" style="margin-top:0.75rem;">
                <i class="fas fa-circle-info"></i>
                <div>Pastikan nama anak sesuai dengan akta kelahiran. Kesalahan nama dapat menghambat proses verifikasi.</div>
              </div>
            </div>
          </div>

          <div class="step-card">
            <div class="step-header">
              <div class="step-num">3</div>
              <h3>Upload Dokumen</h3>
            </div>
            <div class="step-content">
              <p>Unggah semua dokumen yang diperlukan dalam format PDF atau gambar (JPG/PNG) dengan ukuran maksimal 2 MB per file.</p>
              <ul>
                <li>Akta kelahiran anak</li>
                <li>Kartu Keluarga (KK)</li>
                <li>KTP ayah dan ibu</li>
                <li>Pas foto terbaru (latar merah, 3×4)</li>
              </ul>
            </div>
          </div>

          <div class="step-card">
            <div class="step-header">
              <div class="step-num">4</div>
              <h3>Tunggu Verifikasi</h3>
            </div>
            <div class="step-content">
              <p>Panitia akan memverifikasi berkas Anda dalam 3–7 hari kerja. Pantau status di halaman <a href="status.php" style="color:var(--primary);font-weight:600;">Status Pendaftaran</a>.</p>
            </div>
          </div>

          <div class="step-card">
            <div class="step-header">
              <div class="step-num">5</div>
              <h3>Cek Hasil & Daftar Ulang</h3>
            </div>
            <div class="step-content">
              <p>Hasil seleksi diumumkan pada 15 Juli 2025. Jika diterima, segera lakukan daftar ulang sebelum 31 Juli 2025.</p>
            </div>
          </div>
        </div>

        <!-- FAQ -->
        <div id="faq" style="scroll-margin-top:1rem; margin-top:1.5rem;">
          <div class="section-label">FAQ — Pertanyaan Umum</div>

          <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
              Berapa usia minimal anak untuk mendaftar di RA An-Nabil?
              <i class="fas fa-chevron-down chev"></i>
            </div>
            <div class="faq-a">Calon peserta didik minimal berusia 4 tahun lebih</div>
          </div>

          <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
              Apakah pendaftaran bisa dilakukan secara langsung ke sekolah?
              <i class="fas fa-chevron-down chev"></i>
            </div>
            <div class="faq-a">bisa, langsung menghadap guru yang bertugas</div>
          </div>

          <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
              Apakah ada biaya pendaftaran?
              <i class="fas fa-chevron-down chev"></i>
            </div>
            <div class="faq-a">Ada atas kesepakatan sekolah dan orangtua</div>
          </div>

          <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
              Bagaimana jika dokumen saya belum lengkap?
              <i class="fas fa-chevron-down chev"></i>
            </div>
            <div class="faq-a">Anda tetap bisa mengirimkan formulir dengan dokumen yang ada, kemudian melengkapi kekurangan sebelum batas akhir pendaftaran. Panitia akan menginformasikan jika ada dokumen yang perlu dilengkapi.</div>
          </div>
        </div>

        <!-- Kontak -->
        <div id="kontak" style="scroll-margin-top:1rem; margin-top:1.5rem;">
          <div class="section-label">Butuh Bantuan?</div>
          <div class="card" style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
            <div style="flex:1; min-width:200px;">
              <h4 style="font-size:0.95rem; font-weight:700; margin-bottom:0.5rem;">Hubungi Panitia PPDB</h4>
              <p style="font-size:0.83rem; color:var(--text-secondary);">Kami siap membantu Anda pada jam kerja Senin–Jumat, 08.00–15.00 WIB.</p>
            </div>
            <div style="display:flex; flex-direction:column; gap:0.5rem;">
              <a href="https://wa.me/6289614287910" class="btn-back" style="margin-bottom:0;"><i class="fab fa-whatsapp"></i> WhatsApp</a>
              <a href="mailto:alyamuhimah@gmail.com" class="btn-back" style="margin-bottom:0; background:var(--bg-white); color:var(--primary); border:1.5px solid var(--primary); box-shadow:none;"><i class="fas fa-envelope"></i> Email</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function toggleFaq(el) {
  const item = el.closest('.faq-item');
  item.classList.toggle('open');
}
</script>
</body>
</html>