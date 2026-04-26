<?php
require_once '../functions/db.php';
require_once '../functions/auth_admin.php';
requireAdmin();

$active_page = 'cetak';

// Filter status
$filter  = $_GET['status'] ?? 'semua';
$allowed = ['semua', 'pending', 'lulus', 'tidak_lulus'];
if (!in_array($filter, $allowed)) $filter = 'semua';

// Query utama
if ($filter === 'semua') {
    $sql    = "SELECT s.*, u.email FROM siswa s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.tanggal_daftar DESC";
    $result = $conn->query($sql);
} else {
    $stmt = $conn->prepare("SELECT s.*, u.email FROM siswa s LEFT JOIN users u ON s.user_id = u.id WHERE s.status = ? ORDER BY s.tanggal_daftar DESC");
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $result = $stmt->get_result();
}

$siswa = [];
if ($result) while ($row = $result->fetch_assoc()) $siswa[] = $row;

// Statistik
$counts = ['semua' => 0, 'pending' => 0, 'lulus' => 0, 'tidak_lulus' => 0];
$rc = $conn->query("SELECT status, COUNT(*) AS jml FROM siswa GROUP BY status");
if ($rc) while ($r = $rc->fetch_assoc()) {
    $counts[$r['status']] = (int)$r['jml'];
    $counts['semua'] += (int)$r['jml'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Data — Admin PPDB RA An-Nabil</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
/* ── Halaman Cetak Data ── */
.filter-bar {
  display: flex; align-items: center; gap: 0.5rem;
  flex-wrap: wrap; margin-bottom: 1.2rem;
}
.filter-bar label { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-right: 4px; }
.filter-btn {
  padding: 5px 14px; border-radius: 20px;
  border: 1px solid var(--border); background: transparent;
  font-family: inherit; font-size: 0.78rem; font-weight: 600;
  color: var(--text-muted); cursor: pointer; text-decoration: none;
  transition: all .15s;
}
.filter-btn:hover { border-color: var(--primary); color: var(--primary); }
.filter-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }

.search-box {
  display: flex; align-items: center; gap: 8px;
  border: 1px solid var(--border); border-radius: var(--radius-sm);
  padding: 6px 12px; background: var(--bg);
}
.search-box input {
  border: none; background: transparent; outline: none;
  font-family: inherit; font-size: 0.83rem; width: 200px; color: var(--text);
}
.search-box i { color: var(--text-muted); font-size: 0.8rem; }

.table-scroll { overflow-x: auto; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table thead th {
  padding: 10px 14px; text-align: left;
  font-size: 0.72rem; font-weight: 700; color: var(--text-muted);
  text-transform: uppercase; letter-spacing: .6px;
  background: var(--bg); border-bottom: 1px solid var(--border);
  white-space: nowrap;
}
.data-table tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
.data-table tbody tr:hover { background: var(--bg); }
.data-table tbody tr:last-child { border-bottom: none; }
.data-table td { padding: 11px 14px; font-size: 0.83rem; vertical-align: middle; }

.td-nama { font-weight: 700; color: var(--text); }
.td-sub  { font-size: 0.72rem; color: var(--text-muted); margin-top: 2px; }
.td-addr { max-width: 160px; white-space: normal; font-size: 0.8rem; }

/* Print styles */
@media print {
  .admin-sidebar, .admin-topbar, .filter-bar,
  .metrics-grid, .page-actions, .no-print { display: none !important; }
  body { background: #fff !important; }
  .admin-wrap { display: block !important; }
  .admin-main { margin: 0 !important; padding: 0 !important; }
  .admin-page { padding: 0 !important; }
  .a-card     { box-shadow: none !important; border: none !important; padding: 0 !important; }
  .print-header { display: block !important; }
  .data-table thead th { background: #eaf0fb !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
  .data-table td { font-size: 10px !important; padding: 5px 8px !important; }
  .btn { display: none !important; }
}
.print-header {
  display: none; text-align: center;
  padding-bottom: 14px; margin-bottom: 16px;
  border-bottom: 2px solid var(--primary);
}
.print-header .ph-school { font-size: 18px; font-weight: 800; color: var(--primary); }
.print-header .ph-title  { font-size: 13px; font-weight: 600; color: #555; margin-top: 4px; }
.print-header .ph-date   { font-size: 11px; color: #888; margin-top: 2px; }
</style>
</head>
<body>
<div class="admin-wrap">
  <?php include 'sidebar-admin.php'; ?>
  <div class="admin-main">

    <div class="admin-topbar">
      <div class="topbar-left">
        <h1>Cetak Data Siswa</h1>
        <div class="tb-sub">Data lengkap peserta PPDB RA An-Nabil <?= date('Y') ?></div>
      </div>
      <div class="topbar-right no-print">
        <button class="btn btn-outline" onclick="window.print()">
          <i class="fas fa-print"></i> Cetak Halaman
        </button>
        <button class="btn btn-primary" onclick="exportCSV()">
          <i class="fas fa-file-csv"></i> Export CSV
        </button>
      </div>
    </div>

    <div class="admin-page">

      <!-- Print header (hanya muncul saat cetak) -->
      <div class="print-header">
        <div class="ph-school">RA An-Nabil</div>
        <div class="ph-title">Rekap Data Peserta Didik Baru — PPDB <?= date('Y') ?></div>
        <div class="ph-date">Dicetak pada: <?= date('d F Y, H:i') ?> WIB</div>
      </div>

      <!-- Statistik -->
      <div class="metrics-grid no-print">
        <div class="metric-card">
          <div class="m-icon blue"><i class="fas fa-users"></i></div>
          <div>
            <div class="m-label">Total Pendaftar</div>
            <div class="m-value"><?= $counts['semua'] ?></div>
            <div class="m-sub">Semua status</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon green"><i class="fas fa-check-double"></i></div>
          <div>
            <div class="m-label">Lulus</div>
            <div class="m-value"><?= $counts['lulus'] ?? 0 ?></div>
            <div class="m-sub">Status lulus</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon yellow"><i class="fas fa-hourglass-half"></i></div>
          <div>
            <div class="m-label">Pending</div>
            <div class="m-value"><?= $counts['pending'] ?? 0 ?></div>
            <div class="m-sub">Menunggu verifikasi</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon red"><i class="fas fa-times-circle"></i></div>
          <div>
            <div class="m-label">Tidak Lulus</div>
            <div class="m-value"><?= $counts['tidak_lulus'] ?? 0 ?></div>
            <div class="m-sub">Tidak diterima</div>
          </div>
        </div>
      </div>

      <!-- Tabel data -->
      <div class="a-card">
        <!-- Filter & Search -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:1rem" class="no-print">
          <div class="filter-bar" style="margin:0">
            <label><i class="fas fa-filter"></i> Filter:</label>
            <a href="?status=semua"       class="filter-btn <?= $filter==='semua'       ?'active':'' ?>">Semua (<?= $counts['semua'] ?>)</a>
            <a href="?status=lulus"       class="filter-btn <?= $filter==='lulus'       ?'active':'' ?>">Lulus (<?= $counts['lulus'] ?? 0 ?>)</a>
            <a href="?status=pending"     class="filter-btn <?= $filter==='pending'     ?'active':'' ?>">Pending (<?= $counts['pending'] ?? 0 ?>)</a>
            <a href="?status=tidak_lulus" class="filter-btn <?= $filter==='tidak_lulus' ?'active':'' ?>">Tidak Lulus (<?= $counts['tidak_lulus'] ?? 0 ?>)</a>
          </div>
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama, orang tua…" oninput="filterTable()">
          </div>
        </div>

        <?php if (empty($siswa)): ?>
          <div class="empty-state">
            <i class="fas fa-inbox"></i>
            Tidak ada data untuk ditampilkan.
          </div>
        <?php else: ?>
        <div class="table-scroll">
          <table class="data-table" id="mainTable">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>JK</th>
                <th>Tgl Lahir</th>
                <th>Nama Ayah</th>
                <th>Nama Ibu</th>
                <th>No. HP</th>
                <th>Alamat</th>
                <th>Status</th>
                <th>Tgl Daftar</th>
                <th class="no-print">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($siswa as $i => $s):
                $tglLahir  = $s['tanggal_lahir']  ? date('d/m/Y', strtotime($s['tanggal_lahir']))  : '-';
                $tglDaftar = $s['tanggal_daftar'] ? date('d/m/Y', strtotime($s['tanggal_daftar'])) : '-';
                $jk  = $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan';
                $st  = $s['status'] ?? 'pending';
                $badgeClass = match($st) { 'lulus' => 'badge-green', 'tidak_lulus' => 'badge-red', default => 'badge-yellow' };
                $badgeLabel = match($st) { 'lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', default => 'Pending' };
              ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <div class="td-nama"><?= htmlspecialchars($s['nama_siswa']) ?></div>
                  <div class="td-sub"><?= htmlspecialchars($s['email'] ?? '-') ?></div>
                </td>
                <td><?= $jk ?></td>
                <td><?= $tglLahir ?></td>
                <td><?= htmlspecialchars($s['nama_ayah'] ?? '-') ?></td>
                <td><?= htmlspecialchars($s['nama_ibu']  ?? '-') ?></td>
                <td><?= htmlspecialchars($s['no_hp']     ?? '-') ?></td>
                <td class="td-addr"><?= htmlspecialchars($s['alamat'] ?? '-') ?></td>
                <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                <td><?= $tglDaftar ?></td>
                <td class="no-print">
                  <a href="cetak-detail.php?id=<?= $s['id'] ?>" target="_blank"
                     class="btn btn-outline btn-sm" title="Cetak formulir siswa ini">
                    <i class="fas fa-print"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

    </div><!-- /.admin-page -->
  </div><!-- /.admin-main -->
</div><!-- /.admin-wrap -->

<script>
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#mainTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function exportCSV() {
    const headers = ['No','Nama Siswa','JK','Tgl Lahir','Nama Ayah','Nama Ibu','No HP','Alamat','Status','Tgl Daftar'];
    const rows = [headers];
    document.querySelectorAll('#mainTable tbody tr').forEach(tr => {
        if (tr.style.display === 'none') return;
        const cells = [...tr.querySelectorAll('td:not(.no-print)')];
        rows.push(cells.map(td => '"' + td.innerText.replace(/\n/g,' ').trim() + '"'));
    });
    const csv  = rows.map(r => r.join(',')).join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = 'data-siswa-<?= date('Ymd') ?>.csv'; a.click();
    URL.revokeObjectURL(url);
}
</script>

</body>
</html>