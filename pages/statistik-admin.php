<?php
require_once '../functions/db.php';
require_once '../functions/auth_admin.php';
requireAdmin();

$active_page = 'statistik';
$nama    = getAdminName();
$inisial = strtoupper(substr($nama, 0, 2));

// Data agregasi
$total      = (int)$conn->query("SELECT COUNT(*) FROM siswa")->fetch_row()[0];
$pending    = (int)$conn->query("SELECT COUNT(*) FROM siswa WHERE status='pending'")->fetch_row()[0];
$lulus      = (int)$conn->query("SELECT COUNT(*) FROM siswa WHERE status='lulus'")->fetch_row()[0];
$tdk_lulus  = (int)$conn->query("SELECT COUNT(*) FROM siswa WHERE status='tidak_lulus'")->fetch_row()[0];
$laki       = (int)$conn->query("SELECT COUNT(*) FROM siswa WHERE jenis_kelamin='L'")->fetch_row()[0];
$perempuan  = $total - $laki;

// Period filter untuk chart
$period = $_GET['period'] ?? '12month';
$period_label = '12 Bulan Terakhir';
$date_format = '%Y-%m';
$group_by = 'bln';

if ($period === '7day') {
    $period_label = '7 Hari Terakhir';
    $period_sql = "DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $date_format = '%Y-%m-%d';
    $group_by = 'hari';
} elseif ($period === '1month') {
    $period_label = '1 Bulan Terakhir';
    $period_sql = "DATE_SUB(NOW(), INTERVAL 1 MONTH)";
    $date_format = '%Y-%m-%d';
    $group_by = 'hari';
} else {
    $period_label = '12 Bulan Terakhir';
    $period_sql = "DATE_SUB(NOW(), INTERVAL 12 MONTH)";
    $date_format = '%Y-%m';
    $group_by = 'bln';
}

// Pendaftaran per periode
$per_bulan = [];
$res = $conn->query(
    "SELECT DATE_FORMAT(tanggal_daftar,'$date_format') AS $group_by, COUNT(*) AS jml
     FROM siswa WHERE tanggal_daftar >= $period_sql
     GROUP BY $group_by ORDER BY $group_by ASC"
);
while ($r = $res->fetch_assoc()) $per_bulan[] = $r;

// Kelengkapan dokumen
$dok_lengkap = (int)$conn->query(
    "SELECT COUNT(*) FROM dokumen WHERE akta_kelahiran!='' AND kartu_keluarga!='' AND foto!=''"
)->fetch_row()[0];
$dok_kurang  = $total - $dok_lengkap;

function pct($val, $total) {
    return $total ? round($val / $total * 100) : 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Statistik — Admin PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrap">
  <?php include 'sidebar-admin.php'; ?>
  <div class="admin-main">

    <div class="admin-topbar">
      <div class="topbar-left">
        <h1>Statistik Pendaftaran</h1>
        <div class="tb-sub">Ringkasan dan analitik data PPDB</div>
      </div>
      <div class="topbar-right">
        <span class="tb-badge"><i class="fas fa-shield-alt"></i> Administrator</span>
        <div class="admin-avatar"><?= $inisial ?></div>
      </div>
    </div>

    <div class="admin-page">

      <a href="dashboard-admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

      <!-- Metrik utama -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="m-icon blue"><i class="fas fa-users"></i></div>
          <div><div class="m-label">Total Pendaftar</div><div class="m-value"><?= $total ?></div></div>
        </div>
        <div class="metric-card">
          <div class="m-icon yellow"><i class="fas fa-hourglass-half"></i></div>
          <div>
            <div class="m-label">Menunggu</div>
            <div class="m-value"><?= $pending ?></div>
            <div class="m-sub"><?= pct($pending,$total) ?>% dari total</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon green"><i class="fas fa-check-double"></i></div>
          <div>
            <div class="m-label">Diterima</div>
            <div class="m-value"><?= $lulus ?></div>
            <div class="m-sub"><?= pct($lulus,$total) ?>% dari total</div>
          </div>
        </div>
        <div class="metric-card">
          <div class="m-icon red"><i class="fas fa-times-circle"></i></div>
          <div>
            <div class="m-label">Tidak Diterima</div>
            <div class="m-value"><?= $tdk_lulus ?></div>
            <div class="m-sub"><?= pct($tdk_lulus,$total) ?>% dari total</div>
          </div>
        </div>
      </div>

      <div class="grid-3" style="margin-bottom:1.2rem">
        <!-- Distribusi Status -->
        <div class="a-card">
          <div class="a-card-title">Distribusi Status</div>
          <div class="a-card-sub">Hasil verifikasi pendaftar</div>
          <?php
          $bars = [
            ['Menunggu',     $pending,   '#d97706'],
            ['Diterima',     $lulus,     '#0e9f6e'],
            ['Tdk Diterima', $tdk_lulus, '#e02424'],
          ];
          foreach ($bars as [$label, $val, $color]):
            $p = pct($val, $total);
          ?>
          <div class="pb-row">
            <div class="pb-label"><?= $label ?></div>
            <div class="pb-track"><div class="pb-fill" style="width:<?= $p ?>%;background:<?= $color ?>"></div></div>
            <div class="pb-val"><?= $val ?> (<?= $p ?>%)</div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Jenis Kelamin -->
        <div class="a-card">
          <div class="a-card-title">Jenis Kelamin</div>
          <div class="a-card-sub">Komposisi calon siswa</div>
          <?php
          $jk = [
            ['Laki-laki', $laki,      '#1a56db'],
            ['Perempuan',  $perempuan, '#e02424'],
          ];
          foreach ($jk as [$label, $val, $color]):
            $p = pct($val, $total);
          ?>
          <div class="pb-row">
            <div class="pb-label"><?= $label ?></div>
            <div class="pb-track"><div class="pb-fill" style="width:<?= $p ?>%;background:<?= $color ?>"></div></div>
            <div class="pb-val"><?= $val ?> (<?= $p ?>%)</div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Kelengkapan Dokumen -->
        <div class="a-card">
          <div class="a-card-title">Kelengkapan Dokumen</div>
          <div class="a-card-sub">Dari total pendaftar yang terdaftar</div>
          <?php
          $dok = [
            ['Lengkap',      $dok_lengkap, '#0e9f6e'],
            ['Tidak Lengkap',$dok_kurang,  '#e02424'],
          ];
          foreach ($dok as [$label, $val, $color]):
            $p = pct($val, $total);
          ?>
          <div class="pb-row">
            <div class="pb-label"><?= $label ?></div>
            <div class="pb-track"><div class="pb-fill" style="width:<?= $p ?>%;background:<?= $color ?>"></div></div>
            <div class="pb-val"><?= $val ?> (<?= $p ?>%)</div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Grafik per periode -->
      <div class="a-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
          <div>
            <div class="a-card-title">Pendaftaran Periode Waktu</div>
            <div class="a-card-sub"><?= $period_label ?></div>
          </div>
          <div class="period-filter" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="?period=7day" class="filter-btn <?= $period === '7day' ? 'active' : '' ?>" style="padding:0.4rem 0.8rem; border-radius:6px; font-size:0.8rem; text-decoration:none; border:1px solid #e2e8f0; cursor:pointer; transition:all 0.15s; <?= $period === '7day' ? 'background:#3b82f6; color:#fff; border-color:#3b82f6;' : 'background:#fff; color:#475569;' ?>">7 Hari</a>
            <a href="?period=1month" class="filter-btn <?= $period === '1month' ? 'active' : '' ?>" style="padding:0.4rem 0.8rem; border-radius:6px; font-size:0.8rem; text-decoration:none; border:1px solid #e2e8f0; cursor:pointer; transition:all 0.15s; <?= $period === '1month' ? 'background:#3b82f6; color:#fff; border-color:#3b82f6;' : 'background:#fff; color:#475569;' ?>">1 Bulan</a>
            <a href="?period=12month" class="filter-btn <?= $period === '12month' ? 'active' : '' ?>" style="padding:0.4rem 0.8rem; border-radius:6px; font-size:0.8rem; text-decoration:none; border:1px solid #e2e8f0; cursor:pointer; transition:all 0.15s; <?= $period === '12month' ? 'background:#3b82f6; color:#fff; border-color:#3b82f6;' : 'background:#fff; color:#475569;' ?>">12 Bulan</a>
          </div>
        </div>
        <?php if ($per_bulan):
          $max_val = max(array_column($per_bulan, 'jml'));
          $bulan_id = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        ?>
        <div class="chart-wrap">
          <div class="chart-bars">
            <?php foreach ($per_bulan as $b):
              $h = $max_val ? round($b['jml'] / $max_val * 100) : 0;
            ?>
            <div class="c-bar" style="height:<?= $h ?>%">
              <div class="c-tip"><?= $b[$group_by] ?>: <?= $b['jml'] ?> orang</div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="chart-xlabels">
            <?php foreach ($per_bulan as $b):
              if ($period === '7day' || $period === '1month') {
                echo '<span>' . date('d M', strtotime($b[$group_by])) . '</span>';
              } else {
                [$y, $m] = explode('-', $b[$group_by]);
                echo '<span>' . $bulan_id[(int)$m - 1] . ' ' . substr($y, 2) . '</span>';
              }
            endforeach; ?>
          </div>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-chart-bar"></i>Belum ada data pendaftaran untuk periode ini.</div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
</body>
</html>