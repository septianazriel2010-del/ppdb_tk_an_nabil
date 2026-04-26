<?php
require_once '../functions/db.php';
require_once '../functions/auth_admin.php';
requireAdmin();

$active_page = 'pengumuman';
$nama    = getAdminName();
$inisial = strtoupper(substr($nama, 0, 2));
$msg     = '';
$msg_type = 'success';
$action  = $_GET['action'] ?? 'list';
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── HAPUS ──
if (isset($_GET['hapus'])) {
    $hid = (int)$_GET['hapus'];
    $r = $conn->query("SELECT gambar FROM pengumuman WHERE id=$hid")->fetch_assoc();
    if ($r && !empty($r['gambar'])) {
        $path = dirname(__DIR__) . '/uploads/pengumuman/' . $r['gambar'];
        if (file_exists($path)) unlink($path);
    }
    $conn->query("DELETE FROM pengumuman WHERE id=$hid");
    header('Location: pengumuman-admin.php?msg=hapus_ok');
    exit;
}

// ── SIMPAN (tambah/edit) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul   = trim($_POST['judul'] ?? '');
    $isi     = trim($_POST['isi']   ?? '');
    $id_edit = (int)($_POST['id_edit'] ?? 0);
    $gambar  = '';

    // Upload gambar opsional
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $ok_ext = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $ok_ext) && $_FILES['gambar']['size'] < 3_000_000) {
            $dir = dirname(__DIR__) . '/uploads/pengumuman/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = uniqid('peng_') . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], $dir . $fname);
            $gambar = $fname;
        } else {
            $msg      = 'Format/ukuran gambar tidak valid (maks 3 MB, JPG/PNG/GIF/WEBP).';
            $msg_type = 'danger';
        }
    }

    if (!$msg && $judul && $isi) {
        if ($id_edit > 0) {
            if ($gambar) {
                // hapus gambar lama
                $lama = $conn->query("SELECT gambar FROM pengumuman WHERE id=$id_edit")->fetch_assoc();
                if ($lama && !empty($lama['gambar'])) {
                    $path = dirname(__DIR__) . '/uploads/pengumuman/' . $lama['gambar'];
                    if (file_exists($path)) unlink($path);
                }
                $stmt = $conn->prepare("UPDATE pengumuman SET judul=?, isi=?, gambar=? WHERE id=?");
                $stmt->bind_param("sssi", $judul, $isi, $gambar, $id_edit);
            } else {
                $stmt = $conn->prepare("UPDATE pengumuman SET judul=?, isi=? WHERE id=?");
                $stmt->bind_param("ssi", $judul, $isi, $id_edit);
            }
            $stmt->execute(); $stmt->close();
            $msg = 'Pengumuman berhasil diperbarui dan sudah tampil di dashboard orang tua!';
        } else {
            $stmt = $conn->prepare("INSERT INTO pengumuman (judul, isi, gambar) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $judul, $isi, $gambar);
            $stmt->execute(); $stmt->close();
            $msg = 'Pengumuman berhasil diterbitkan dan langsung tampil di dashboard orang tua!';
        }
        $action = 'list';
    } elseif (!$msg) {
        $msg = 'Judul dan isi wajib diisi.';
        $msg_type = 'danger';
        $action  = $id_edit > 0 ? 'edit' : 'tambah';
        $edit_id = $id_edit;
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_ok') $msg = 'Pengumuman berhasil dihapus.';

// Data untuk form edit
$edit_data = null;
if ($action === 'edit' && $edit_id) {
    $edit_data = $conn->query("SELECT * FROM pengumuman WHERE id=$edit_id")->fetch_assoc();
    if (!$edit_data) { $action = 'list'; } // fallback jika id tidak ada
}

// List semua pengumuman
$list = [];
$res  = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal DESC");
while ($r = $res->fetch_assoc()) $list[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Pengumuman — Admin PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrap">
  <?php include 'sidebar-admin.php'; ?>
  <div class="admin-main">

    <div class="admin-topbar">
      <div class="topbar-left">
        <h1>Kelola Pengumuman</h1>
        <div class="tb-sub">Konten yang tampil di dashboard orang tua</div>
      </div>
      <div class="topbar-right">
        <span class="tb-badge"><i class="fas fa-shield-alt"></i> Administrator</span>
        <div class="admin-avatar"><?= $inisial ?></div>
      </div>
    </div>

    <div class="admin-page">

      <a href="dashboard-admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

      <?php if ($msg): ?>
      <div class="alert alert-<?= $msg_type ?>">
        <i class="fas <?= $msg_type==='success'?'fa-check-circle':'fa-exclamation-triangle' ?>"></i>
        <?= htmlspecialchars($msg) ?>
      </div>
      <?php endif; ?>

      <?php if ($action === 'tambah' || $action === 'edit'): ?>
      <!-- ── FORM ── -->
      <div class="a-card">
        <div class="a-card-title"><?= $action==='edit'?'Edit Pengumuman':'Tambah Pengumuman Baru' ?></div>
        <div class="a-card-sub">
          <?= $action==='edit'
            ? 'Perubahan akan langsung terlihat di dashboard orang tua.'
            : 'Pengumuman baru akan langsung tampil di dashboard orang tua setelah disimpan.' ?>
        </div>

        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id_edit" value="<?= $edit_data['id'] ?? 0 ?>">

          <div class="form-group">
            <label>Judul Pengumuman <span style="color:var(--danger)">*</span></label>
            <input type="text" name="judul" placeholder="Masukkan judul pengumuman..."
              required value="<?= htmlspecialchars($edit_data['judul'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Isi Pengumuman <span style="color:var(--danger)">*</span></label>
            <textarea name="isi" placeholder="Tulis isi pengumuman..." required><?= htmlspecialchars($edit_data['isi'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Gambar <span style="color:var(--text-muted);font-weight:400">(opsional · maks 3 MB · JPG/PNG/WEBP)</span></label>
            <?php if (!empty($edit_data['gambar'])): ?>
            <div style="margin-bottom:0.5rem">
              <img src="uploads/pengumuman/<?= htmlspecialchars($edit_data['gambar']) ?>"
                style="max-height:110px;border-radius:var(--radius-sm);border:1px solid var(--border)" alt="gambar saat ini">
              <div style="font-size:0.7rem;color:var(--text-muted);margin-top:3px">Upload baru untuk mengganti gambar lama.</div>
            </div>
            <?php endif; ?>
            <input type="file" name="gambar" accept="image/*">
          </div>

          <div style="display:flex;gap:0.65rem;flex-wrap:wrap;margin-top:0.5rem">
            <button type="submit" class="btn btn-primary">
              <i class="fas <?= $action==='edit'?'fa-save':'fa-paper-plane' ?>"></i>
              <?= $action==='edit'?'Simpan Perubahan':'Terbitkan Pengumuman' ?>
            </button>
            <a href="pengumuman-admin.php" class="btn btn-outline">
              <i class="fas fa-times"></i> Batal
            </a>
          </div>
        </form>
      </div>

      <?php else: ?>
      <!-- ── LIST ── -->
      <div class="page-header">
        <div>
          <h2>Daftar Pengumuman</h2>
          <p>Total <?= count($list) ?> pengumuman diterbitkan</p>
        </div>
        <a href="pengumuman-admin.php?action=tambah" class="btn btn-primary">
          <i class="fas fa-plus"></i> Tambah Pengumuman
        </a>
      </div>

      <div class="a-card">
        <?php if ($list): ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Judul & Isi</th>
                <th>Gambar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $i => $p): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td>
                <div style="font-weight:600;font-size:0.85rem"><?= htmlspecialchars($p['judul']) ?></div>
                <div style="font-size:0.73rem;color:var(--text-muted);margin-top:2px">
                  <?= htmlspecialchars(mb_substr(strip_tags($p['isi']), 0, 75)) ?>...
                </div>
              </td>
              <td>
                <?php if (!empty($p['gambar'])): ?>
                  <img src="uploads/pengumuman/<?= htmlspecialchars($p['gambar']) ?>"
                    style="height:40px;border-radius:6px;border:1px solid var(--border)" alt="">
                <?php else: ?>
                  <span class="badge badge-gray">Tidak ada</span>
                <?php endif; ?>
              </td>
              <td style="font-size:0.78rem;white-space:nowrap"><?= date('d M Y', strtotime($p['tanggal'])) ?></td>
              <td>
                <div class="td-actions">
                  <a href="pengumuman-admin.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <a href="pengumuman-admin.php?hapus=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Yakin hapus pengumuman ini?')">
                    <i class="fas fa-trash"></i> Hapus
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-bullhorn"></i>
          Belum ada pengumuman.<br>
          <a href="pengumuman-admin.php?action=tambah" class="btn btn-primary" style="margin-top:1rem">
            <i class="fas fa-plus"></i> Buat Pengumuman Pertama
          </a>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>