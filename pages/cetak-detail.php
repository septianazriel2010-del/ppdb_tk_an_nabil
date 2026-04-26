<?php
require_once '../functions/db.php';
require_once '../functions/auth_admin.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { echo "ID tidak valid."; exit; }

$stmt = $conn->prepare("
    SELECT s.*, u.email
    FROM siswa s
    LEFT JOIN users u ON s.user_id = u.id
    WHERE s.id = ? LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$s = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$s) { echo "Data tidak ditemukan."; exit; }

$tglLahir  = $s['tanggal_lahir']  ? date('d F Y', strtotime($s['tanggal_lahir']))  : '-';
$tglDaftar = $s['tanggal_daftar'] ? date('d F Y', strtotime($s['tanggal_daftar'])) : '-';
$jk        = $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan';
$stLabel   = match($s['status'] ?? '') { 'lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', default => 'Pending' };
$stClass   = match($s['status'] ?? '') { 'lulus' => 'badge-green', 'tidak_lulus' => 'badge-red', default => 'badge-yellow' };
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formulir — <?= htmlspecialchars($s['nama_siswa']) ?></title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
/* ── Layout halaman cetak detail ── */
body { background: var(--bg); }

.detail-wrap {
  max-width: 800px;
  margin: 2rem auto;
  padding: 0 1rem;
}

/* Tombol aksi atas */
.action-bar {
  display: flex; gap: 0.6rem; margin-bottom: 1.2rem;
  align-items: center;
}

/* Sheet dokumen */
.sheet {
  background: #fff;
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: 0 2px 20px rgba(0,0,0,.08);
  border: 1px solid var(--border);
}

/* KOP */
.kop {
  background: linear-gradient(135deg, #0f172a 0%, #1a3a8f 100%);
  color: #fff; display: flex; align-items: center;
  gap: 1.2rem; padding: 1.6rem 2rem;
}
.kop-logo { font-size: 2.8rem; opacity: .9; }
.kop h1   { font-size: 1.2rem; font-weight: 800; }
.kop p    { font-size: 0.78rem; opacity: .7; margin-top: 3px; }
.kop-right { margin-left: auto; text-align: right; flex-shrink: 0; }
.kop-right .no-reg { font-size: 0.7rem; opacity: .6; }
.kop-right .reg-val { font-size: 1rem; font-weight: 800; letter-spacing: 1px; margin-top: 2px; }

/* Judul dokumen */
.doc-title {
  text-align: center; padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--border);
}
.doc-title h2 {
  font-size: 0.85rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: 1.5px; color: var(--primary);
}
.doc-title p { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }

/* Status bar */
.status-row {
  display: flex; align-items: center; gap: 0.6rem;
  padding: 0.8rem 1.5rem; border-bottom: 1px solid var(--border);
  font-size: 0.82rem; font-weight: 700;
}

/* Section dalam sheet */
.doc-section { padding: 1.2rem 1.5rem; }
.doc-section + .doc-section { border-top: 1px solid var(--border); }
.section-title {
  font-size: 0.72rem; font-weight: 800; color: var(--primary);
  text-transform: uppercase; letter-spacing: 1px;
  border-left: 3px solid var(--accent, #f59e0b);
  padding-left: 8px; margin-bottom: 1rem;
}
.fields-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem 2rem; }
.field { display: flex; flex-direction: column; gap: 3px; }
.field label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }
.field span  { font-size: 0.85rem; font-weight: 600; border-bottom: 1px dashed var(--border); padding-bottom: 6px; min-height: 26px; }
.full { grid-column: 1 / -1; }

/* TTD */
.doc-footer {
  display: flex; justify-content: space-between; align-items: flex-end;
  padding: 1rem 1.5rem 1.5rem; border-top: 1px solid var(--border); margin-top: 0.5rem;
}
.footer-note { font-size: 0.72rem; color: var(--text-muted); line-height: 1.7; max-width: 280px; }
.ttd { text-align: center; width: 190px; }
.ttd .lbl   { font-size: 0.75rem; color: #555; }
.ttd .space { height: 60px; border-bottom: 1px solid #333; margin: 8px 0; }
.ttd .name  { font-size: 0.75rem; font-weight: 700; }

/* PRINT */
@media print {
  .action-bar, .no-print { display: none !important; }
  body { background: #fff !important; }
  .detail-wrap { margin: 0; padding: 0; max-width: 100%; }
  .sheet { box-shadow: none; border: none; border-radius: 0; }
  .kop { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
}
</style>
</head>
<body>
<div class="detail-wrap">

  <!-- Tombol aksi (hilang saat print) -->
  <div class="action-bar no-print">
    <a href="cetak-data.php" class="btn btn-outline">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <button class="btn btn-primary" onclick="window.print()">
      <i class="fas fa-print"></i> Cetak Formulir
    </button>
  </div>

  <div class="sheet">

    <!-- KOP -->
    <div class="kop">
      <div class="kop-logo"><i class="fas fa-graduation-cap"></i></div>
      <div>
        <h1>RA An-Nabil</h1>
        <p>Penerimaan Peserta Didik Baru (PPDB) — Tahun Ajaran <?= date('Y') ?>/<?= date('Y')+1 ?></p>
      </div>
      <div class="kop-right">
        <div class="no-reg">No. Pendaftaran</div>
        <div class="reg-val">#<?= str_pad($s['id'], 5, '0', STR_PAD_LEFT) ?></div>
      </div>
    </div>

    <!-- JUDUL -->
    <div class="doc-title">
      <h2>Formulir Data Calon Peserta Didik</h2>
      <p>Tanggal Pendaftaran: <?= $tglDaftar ?> &nbsp;|&nbsp; Dicetak: <?= date('d F Y, H:i') ?> WIB</p>
    </div>

    <!-- STATUS -->
    <div class="status-row">
      <span style="color:var(--text-muted);font-weight:600;font-size:0.8rem">Status Penerimaan:</span>
      <span class="badge <?= $stClass ?>"><?= $stLabel ?></span>
    </div>

    <!-- DATA SISWA -->
    <div class="doc-section">
      <div class="section-title">Data Calon Siswa</div>
      <div class="fields-grid">
        <div class="field full">
          <label>Nama Lengkap Siswa</label>
          <span><?= htmlspecialchars($s['nama_siswa']) ?></span>
        </div>
        <div class="field">
          <label>Jenis Kelamin</label>
          <span><?= $jk ?></span>
        </div>
        <div class="field">
          <label>Tanggal Lahir</label>
          <span><?= $tglLahir ?></span>
        </div>
        <div class="field full">
          <label>Alamat Lengkap</label>
          <span><?= htmlspecialchars($s['alamat'] ?? '-') ?></span>
        </div>
      </div>
    </div>

    <!-- DATA ORANG TUA -->
    <div class="doc-section">
      <div class="section-title">Data Orang Tua / Wali</div>
      <div class="fields-grid">
        <div class="field">
          <label>Nama Ayah</label>
          <span><?= htmlspecialchars($s['nama_ayah'] ?? '-') ?></span>
        </div>
        <div class="field">
          <label>Nama Ibu</label>
          <span><?= htmlspecialchars($s['nama_ibu'] ?? '-') ?></span>
        </div>
        <div class="field">
          <label>No. HP / WhatsApp</label>
          <span><?= htmlspecialchars($s['no_hp'] ?? '-') ?></span>
        </div>
        <div class="field">
          <label>Email Akun</label>
          <span><?= htmlspecialchars($s['email'] ?? '-') ?></span>
        </div>
      </div>
    </div>

    <!-- FOOTER TTD -->
    <div class="doc-footer">
      <div class="footer-note">
        Dokumen ini merupakan bukti pendaftaran resmi PPDB RA An-Nabil.<br>
        Harap disimpan sebagai arsip.
      </div>
      <div class="ttd">
        <div class="lbl">Bogor, <?= date('d F Y') ?></div>
        <div class="lbl">Kepala RA An-Nabil</div>
        <div class="space"></div>
        <div class="name">( ________________________ )</div>
      </div>
    </div>

  </div><!-- /.sheet -->
</div><!-- /.detail-wrap -->

<script>
  // Auto print jika dibuka dari tombol di tabel
  if (window.opener) window.print();
</script>
</body>
</html>