<?php
session_start();
require_once '../functions/functions.php';
$active_page = 'pendaftaran';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$inisial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
  .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
  .form-group.full { grid-column: 1 / -1; }
  .form-group label { font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); }
  .form-group label span.req { color: var(--danger); margin-left: 2px; }
  .form-group input,
  .form-group select,
  .form-group textarea {
    padding: 0.6rem 0.85rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-family: inherit;
    color: var(--text-primary);
    background: var(--bg-white);
    transition: border-color 0.18s, box-shadow 0.18s;
    outline: none;
  }
  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
  }
  .form-group textarea { resize: vertical; min-height: 90px; }

  .form-section-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 1.5rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-light);
    grid-column: 1 / -1;
  }

  .btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.75rem;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.18s, box-shadow 0.18s, transform 0.15s;
    box-shadow: var(--shadow-blue);
  }
  .btn-submit:hover { background: var(--primary-dark); transform: translateY(-1px); }
  .btn-submit:active { transform: translateY(0); }

  .btn-draft {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    background: var(--bg-white);
    color: var(--text-secondary);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.18s;
  }
  .btn-draft:hover { border-color: var(--primary); color: var(--primary); }

  .form-actions { display: flex; gap: 0.75rem; align-items: center; margin-top: 1.5rem; grid-column: 1 / -1; }

  .progress-bar-wrap { background: var(--border); border-radius: 99px; height: 6px; margin-bottom: 1.5rem; }
  .progress-bar-fill { background: var(--primary); height: 6px; border-radius: 99px; width: 40%; transition: width 0.4s; }
  .progress-label { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.4rem; }

  @media(max-width:600px){ .form-grid{ grid-template-columns:1fr; } .form-group.full{ grid-column:1; } }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>

  <div class="content-isi">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Formulir Pendaftaran</h1>
        <p>Isi data dengan lengkap dan benar</p>
      </div>
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <div class="info-box">
      <i class="fas fa-circle-info"></i>
      <div>Pastikan semua data yang diisi sesuai dengan dokumen resmi. Data yang tidak sesuai dapat menghambat proses verifikasi.</div>
    </div>

    <!-- Progress -->
    <div class="card" style="margin-bottom:1.25rem;">
      <div class="progress-label">Kelengkapan formulir — 40%</div>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" id="progressFill"></div></div>
      <div style="display:flex; gap:1rem; flex-wrap:wrap;">
        <span class="badge badge-blue"><i class="fas fa-check"></i> Data Anak</span>
        <span class="badge badge-blue"><i class="fas fa-check"></i> Data Orang Tua</span>
        <span class="badge badge-gray">Dokumen</span>
        <span class="badge badge-gray">Konfirmasi</span>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Data Pendaftaran</div>
      <div class="card-subtitle">Tahun Ajaran 2025/2026</div>

      <form method="POST" action="proses-pendaftaran.php" enctype="multipart/form-data">
        <div class="form-grid">

          <div class="form-section-title"><i class="fas fa-child"></i> Data Calon Peserta Didik</div>

          <div class="form-group">
            <label>Nama Lengkap Anak <span class="req">*</span></label>
            <input type="text" name="nama_anak" placeholder="Sesuai akta kelahiran" required>
          </div>
          <div class="form-group">
            <label>Nama Panggilan</label>
            <input type="text" name="nama_panggilan" placeholder="Nama yang biasa dipakai">
          </div>
          <div class="form-group">
            <label>Tempat Lahir <span class="req">*</span></label>
            <input type="text" name="tempat_lahir" placeholder="Kota/Kabupaten" required>
          </div>
          <div class="form-group">
            <label>Tanggal Lahir <span class="req">*</span></label>
            <input type="date" name="tanggal_lahir" required>
          </div>
          <div class="form-group">
            <label>Jenis Kelamin <span class="req">*</span></label>
            <select name="jenis_kelamin" required>
              <option value="">-- Pilih --</option>
              <option>Laki-laki</option>
              <option>Perempuan</option>
            </select>
          </div>
          <div class="form-group">
            <label>Agama <span class="req">*</span></label>
            <select name="agama" required>
              <option value="">-- Pilih --</option>
              <option>Islam</option>
              <option>Kristen</option>
              <option>Katolik</option>
              <option>Hindu</option>
              <option>Buddha</option>
            </select>
          </div>
          <div class="form-group full">
            <label>Alamat Tempat Tinggal <span class="req">*</span></label>
            <textarea name="alamat" placeholder="Jl. Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota" required></textarea>
          </div>

          <div class="form-section-title"><i class="fas fa-users"></i> Data Orang Tua / Wali</div>

          <div class="form-group">
            <label>Nama Ayah <span class="req">*</span></label>
            <input type="text" name="nama_ayah" placeholder="Nama lengkap ayah" required>
          </div>
          <div class="form-group">
            <label>Nama Ibu <span class="req">*</span></label>
            <input type="text" name="nama_ibu" placeholder="Nama lengkap ibu" required>
          </div>
          <div class="form-group">
            <label>No. HP / WhatsApp <span class="req">*</span></label>
            <input type="tel" name="no_hp" placeholder="08xxxxxxxxxx" required>
          </div>
          <div class="form-group">
            <label>Email Orang Tua</label>
            <input type="email" name="email" placeholder="email@contoh.com">
          </div>
          <div class="form-group">
            <label>Pekerjaan Ayah</label>
            <input type="text" name="pekerjaan_ayah" placeholder="Contoh: Wiraswasta">
          </div>
          <div class="form-group">
            <label>Pekerjaan Ibu</label>
            <input type="text" name="pekerjaan_ibu" placeholder="Contoh: Ibu Rumah Tangga">
          </div>

          <div class="form-section-title"><i class="fas fa-paperclip"></i> Upload Dokumen</div>

          <div class="form-group">
            <label>Akta Kelahiran <span class="req">*</span></label>
            <input type="file" name="akta" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>
          <div class="form-group">
            <label>Kartu Keluarga <span class="req">*</span></label>
            <input type="file" name="kk" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>
          <div class="form-group">
            <label>KTP Orang Tua <span class="req">*</span></label>
            <input type="file" name="ktp" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>
          <div class="form-group">
            <label>Pas Foto <span class="req">*</span></label>
            <input type="file" name="pas_foto" accept=".jpg,.jpeg,.png" required>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Kirim Pendaftaran</button>
            <button type="button" class="btn-draft"><i class="fas fa-save"></i> Simpan Draft</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>