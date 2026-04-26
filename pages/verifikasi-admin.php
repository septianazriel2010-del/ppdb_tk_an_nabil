<?php
require_once '../functions/db.php';
require_once '../functions/auth_admin.php';
requireAdmin();

$active_page = 'verifikasi';
$nama    = getAdminName();
$inisial = strtoupper(substr($nama, 0, 2));
$msg     = '';

// ── Update status ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $sid    = (int)$_POST['siswa_id'];
    $status = in_array($_POST['status'], ['pending','lulus','tidak_lulus']) ? $_POST['status'] : 'pending';
    $stmt   = $conn->prepare("UPDATE siswa SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $sid);
    $stmt->execute(); $stmt->close();
    header("Location: verifikasi-admin.php?detail=$sid&msg=update_ok");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'update_ok') $msg = 'Status berhasil diperbarui!';

// ── Filter & Search ──
$filter = $_GET['status'] ?? 'semua';
$q      = trim($_GET['q'] ?? '');

$where  = []; $params = []; $types = '';
if ($filter !== 'semua') {
    $where[] = "s.status = ?";
    $params[] = $filter; $types .= 's';
}
if ($q) {
    $like = "%$q%";
    $where[] = "(s.nama_siswa LIKE ? OR s.nama_ayah LIKE ? OR s.no_hp LIKE ?)";
    $params[] = $like; $params[] = $like; $params[] = $like; $types .= 'sss';
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $conn->prepare(
    "SELECT s.*, d.akta_kelahiran, d.kartu_keluarga, d.foto
     FROM siswa s LEFT JOIN dokumen d ON d.siswa_id = s.id
     $where_sql ORDER BY s.tanggal_daftar DESC"
);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Detail satu siswa ──
$detail = null;
if (isset($_GET['detail'])) {
    $did    = (int)$_GET['detail'];
    $detail = $conn->query(
        "SELECT s.*, d.akta_kelahiran, d.kartu_keluarga, d.foto
         FROM siswa s LEFT JOIN dokumen d ON d.siswa_id = s.id
         WHERE s.id = $did"
    )->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Pendaftar — Admin PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
.filter-bar { display:flex; align-items:center; gap:.65rem; flex-wrap:wrap; margin-bottom:1.2rem; }
.filter-bar input[type=text] { max-width:220px; }
.filter-bar select { max-width:170px; }
</style>
</head>
<body>
<div class="admin-wrap">
  <?php include 'sidebar-admin.php'; ?>
  <div class="admin-main">

    <div class="admin-topbar">
      <div class="topbar-left">
        <h1>Verifikasi Pendaftar</h1>
        <div class="tb-sub">Kelola dan verifikasi data calon siswa</div>
      </div>
      <div class="topbar-right">
        <span class="tb-badge"><i class="fas fa-shield-alt"></i> Administrator</span>
        <div class="admin-avatar"><?= $inisial ?></div>
      </div>
    </div>

    <div class="admin-page">

      <a href="dashboard-admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

      <?php if ($msg): ?>
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <?php if ($detail): ?>
      <!-- ── HALAMAN DETAIL ── -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem">
        <div>
          <h2 style="font-size:1.2rem;font-weight:800">Detail Pendaftar</h2>
          <p style="font-size:0.8rem;color:var(--text-muted)">Data lengkap calon siswa</p>
        </div>
        <a href="verifikasi-admin.php" class="btn btn-outline"><i class="fas fa-list"></i> Kembali ke Daftar</a>
      </div>

      <div class="grid-cols-2">
        <!-- Kiri: Info siswa -->
        <div class="a-card">
          <div class="a-card-title">Data Siswa</div>
          <div class="a-card-sub"></div>
          <div class="detail-grid">
            <div class="d-item"><label>Nama Siswa</label><p><?= htmlspecialchars($detail['nama_siswa']) ?></p></div>
            <div class="d-item"><label>Jenis Kelamin</label><p><?= $detail['jenis_kelamin']==='L'?'Laki-laki':'Perempuan' ?></p></div>
            <div class="d-item"><label>Tanggal Lahir</label><p><?= date('d M Y', strtotime($detail['tanggal_lahir'])) ?></p></div>
            <div class="d-item"><label>No. HP</label><p><?= htmlspecialchars($detail['no_hp']) ?></p></div>
            <div class="d-item"><label>Nama Ayah</label><p><?= htmlspecialchars($detail['nama_ayah']) ?></p></div>
            <div class="d-item"><label>Nama Ibu</label><p><?= htmlspecialchars($detail['nama_ibu']) ?></p></div>
            <div class="d-item" style="grid-column:1/-1">
              <label>Alamat</label>
              <p style="font-weight:500;line-height:1.5"><?= nl2br(htmlspecialchars($detail['alamat'])) ?></p>
            </div>
            <div class="d-item"><label>Tanggal Daftar</label><p><?= date('d M Y H:i', strtotime($detail['tanggal_daftar'])) ?></p></div>
            <div class="d-item">
              <label>Status Saat Ini</label>
              <p>
                <span class="badge <?= $detail['status']==='pending'?'badge-yellow':($detail['status']==='lulus'?'badge-green':'badge-red') ?>">
                  <?= ucfirst(str_replace('_',' ',$detail['status'])) ?>
                </span>
              </p>
            </div>
          </div>
        </div>

        <!-- Kanan: Dokumen + Ubah status -->
        <div style="display:flex;flex-direction:column;gap:1.2rem">
          <div class="a-card">
            <div class="a-card-title">Dokumen Pendukung</div>
            <div class="a-card-sub"></div>
            <?php
            $dok_items = [
              'akta_kelahiran' => 'Akta Kelahiran',
              'kartu_keluarga' => 'Kartu Keluarga',
              'foto'           => 'Foto Siswa',
            ];
            foreach ($dok_items as $key => $label):
              $ada = !empty($detail[$key]);
            ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.58rem 0;border-bottom:1px solid #f1f5f9">
              <span style="font-size:0.83rem;font-weight:500"><?= $label ?></span>
              <?php if ($ada): ?>
                <button type="button" class="btn btn-outline btn-sm" onclick="openDocModal('<?= htmlspecialchars($detail[$key]) ?>', this)">
                  <i class="fas fa-eye"></i> Lihat
                </button>
              <?php else: ?>
                <span class="badge badge-red"><i class="fas fa-times"></i> Belum diupload</span>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="a-card">
            <div class="a-card-title">Ubah Status Pendaftaran</div>
            <div class="a-card-sub">Status langsung terlihat di dashboard orang tua.</div>
            <form method="POST">
              <input type="hidden" name="siswa_id" value="<?= $detail['id'] ?>">
              <div class="form-group">
                <label>Status Baru</label>
                <select name="status">
                  <option value="pending"     <?= $detail['status']==='pending'     ?'selected':'' ?>>⏳ Menunggu Verifikasi</option>
                  <option value="lulus"       <?= $detail['status']==='lulus'       ?'selected':'' ?>>✅ Diterima</option>
                  <option value="tidak_lulus" <?= $detail['status']==='tidak_lulus' ?'selected':'' ?>>❌ Tidak Diterima</option>
                </select>
              </div>
              <button type="submit" name="update_status" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Status
              </button>
            </form>
          </div>
        </div>
      </div>

      <?php else: ?>
      <!-- ── HALAMAN LIST ── -->
      <div class="page-header">
        <div>
          <h2>Daftar Pendaftar</h2>
          <p>Total <span id="total-count"><?= count($list) ?></span> data ditemukan</p>
        </div>
      </div>

      <div class="filter-bar">
        <input type="text" id="search-input" placeholder="Cari nama / HP / ayah..." value="<?= htmlspecialchars($q) ?>">
        <select id="status-filter">
          <option value="semua">Semua Status</option>
          <option value="pending">Menunggu Verifikasi</option>
          <option value="lulus">Diterima</option>
          <option value="tidak_lulus">Tidak Diterima</option>
        </select>
        <a href="verifikasi-admin.php" class="btn btn-outline btn-sm">Reset</a>
      </div>

      <div class="a-card">
        <?php if ($list): ?>
        <div class="table-wrap">
          <table id="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Siswa</th>
                <th>Orang Tua</th>
                <th>No. HP</th>
                <th>Dokumen</th>
                <th>Tanggal Daftar</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="table-body">
            <?php foreach ($list as $i => $s): ?>
            <tr class="data-row" data-status="<?= $s['status'] ?>" data-search="<?= strtolower($s['nama_siswa'] . ' ' . $s['no_hp'] . ' ' . $s['nama_ayah']) ?>">
              <td><?= $i+1 ?></td>
              <td>
                <div style="font-weight:600"><?= htmlspecialchars($s['nama_siswa']) ?></div>
                <div style="font-size:0.7rem;color:var(--text-muted)">
                  <?= $s['jenis_kelamin']==='L'?'Laki-laki':'Perempuan' ?> ·
                  <?= date('d M Y', strtotime($s['tanggal_lahir'])) ?>
                </div>
              </td>
              <td>
                <div style="font-size:0.82rem"><?= htmlspecialchars($s['nama_ayah']) ?></div>
                <div style="font-size:0.7rem;color:var(--text-muted)"><?= htmlspecialchars($s['nama_ibu']) ?></div>
              </td>
              <td style="font-size:0.8rem"><?= htmlspecialchars($s['no_hp']) ?></td>
              <td>
                <span class="dok-chip <?= !empty($s['akta_kelahiran'])?'dok-ok':'dok-no' ?>">
                  <i class="fas <?= !empty($s['akta_kelahiran'])?'fa-check':'fa-times' ?>"></i> Akta
                </span>
                <span class="dok-chip <?= !empty($s['kartu_keluarga'])?'dok-ok':'dok-no' ?>">
                  <i class="fas <?= !empty($s['kartu_keluarga'])?'fa-check':'fa-times' ?>"></i> KK
                </span>
                <span class="dok-chip <?= !empty($s['foto'])?'dok-ok':'dok-no' ?>">
                  <i class="fas <?= !empty($s['foto'])?'fa-check':'fa-times' ?>"></i> Foto
                </span>
              </td>
              <td style="font-size:0.77rem;white-space:nowrap"><?= date('d M Y', strtotime($s['tanggal_daftar'])) ?></td>
              <td>
                <span class="badge <?= $s['status']==='pending'?'badge-yellow':($s['status']==='lulus'?'badge-green':'badge-red') ?>">
                  <i class="fas <?= $s['status']==='pending'?'fa-hourglass-half':($s['status']==='lulus'?'fa-check-circle':'fa-times-circle') ?>"></i>
                  <?= ucfirst(str_replace('_',' ',$s['status'])) ?>
                </span>
              </td>
              <td>
                <a href="verifikasi-admin.php?detail=<?= $s['id'] ?>" class="btn btn-primary btn-sm">
                  <i class="fas fa-search"></i> Detail
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div id="no-results" style="display:none">
          <div class="empty-state">
            <i class="fas fa-search"></i>
            Tidak ada data yang cocok dengan pencarian.
          </div>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-users"></i>
          Tidak ada data siswa.
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>
// Live Search Function
function liveSearch() {
  const searchInput = document.getElementById('search-input');
  const statusFilter = document.getElementById('status-filter');
  const rows = document.querySelectorAll('.data-row');
  const noResults = document.getElementById('no-results');
  const tableBody = document.getElementById('table-body');
  let visibleCount = 0;

  const searchTerm = searchInput.value.toLowerCase().trim();
  const statusValue = statusFilter.value;

  rows.forEach(row => {
    const searchAttr = row.getAttribute('data-search');
    const statusAttr = row.getAttribute('data-status');
    
    const matchSearch = searchAttr.includes(searchTerm);
    const matchStatus = statusValue === 'semua' || statusAttr === statusValue;
    
    if (matchSearch && matchStatus) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Tampilkan pesan jika tidak ada hasil
  if (visibleCount === 0) {
    tableBody.style.display = 'none';
    noResults.style.display = 'block';
    document.getElementById('total-count').textContent = '0';
  } else {
    tableBody.style.display = '';
    noResults.style.display = 'none';
    document.getElementById('total-count').textContent = visibleCount;
  }
}

// Event Listeners
document.getElementById('search-input').addEventListener('keyup', liveSearch);
document.getElementById('status-filter').addEventListener('change', liveSearch);

// Document Modal Viewer
function openDocModal(filename, btn) {
  const modal = document.getElementById('docModal');
  const viewer = document.getElementById('docViewer');
  const ext = filename.split('.').pop().toLowerCase();
  
  // Show loading
  viewer.innerHTML = '<div style="padding: 2rem; text-align: center;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
  modal.style.display = 'flex';
  
  // Build URL
  const url = '../api/serve-dokumen.php?file=' + encodeURIComponent(filename);
  
  // Render based on file type
  if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
    // Image - dengan ukuran yang konsisten
    const img = new Image();
    img.onload = function() {
      viewer.innerHTML = `
        <div style="
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 1rem;
        ">
          <img src="${url}" style="
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
          ">
        </div>
      `;
    };
    img.onerror = function() {
      viewer.innerHTML = '<div style="padding: 2rem; text-align: center; color: red;"><i class="fas fa-exclamation-circle"></i> Error loading image</div>';
    };
    img.src = url;
  } else if (ext === 'pdf') {
    // PDF
    viewer.innerHTML = '<iframe src="' + url + '" style="width: 100%; height: 100%; border: none; border-radius: 8px;"></iframe>';
  } else {
    // Other files
    viewer.innerHTML = '<div style="padding: 2rem; text-align: center;"><i class="fas fa-file"></i><br><a href="' + url + '" class="btn btn-primary" target="_blank">Download File</a></div>';
  }
}

function closeDocModal() {
  document.getElementById('docModal').style.display = 'none';
}

// Close modal when click outside
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('docModal');
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        closeDocModal();
      }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        closeDocModal();
      }
    });
  }
});
</script>

<!-- Document Viewer Modal -->
<div id="docModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); z-index: 10000; align-items: center; justify-content: center; padding: 1rem;">
  <div style="position: relative; background: white; border-radius: 12px; width: 100%; max-width: 900px; height: 80vh; max-height: 600px; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
    <button onclick="closeDocModal()" style="position: absolute; top: 0.8rem; right: 0.8rem; background: white; border: 2px solid #e2e8f0; width: 40px; height: 40px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; z-index: 10001; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
      <i class="fas fa-times"></i>
    </button>
    <div id="docViewer" style="flex: 1; display: flex; align-items: center; justify-content: center; width: 100%; overflow: auto;"></div>
  </div>
</div>

</body>
</html>