<?php
require_once '../functions/db.php';
require_once '../functions/auth_admin.php';
requireAdmin();

$active_page = 'kontak';
$nama    = getAdminName();
$inisial = strtoupper(substr($nama, 0, 2));
$msg = ''; $msg_type = 'success';

// Simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat   = trim($_POST['alamat']   ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $tiktok   = trim($_POST['tiktok']   ?? '');

    // Cek apakah sudah ada record
    $cek = $conn->query("SELECT id FROM kontak LIMIT 1")->fetch_assoc();
    if ($cek) {
        $stmt = $conn->prepare("UPDATE kontak SET alamat=?, whatsapp=?, email=?, tiktok=? WHERE id=?");
        $stmt->bind_param("ssssi", $alamat, $whatsapp, $email, $tiktok, $cek['id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO kontak (alamat, whatsapp, email, tiktok) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $alamat, $whatsapp, $email, $tiktok);
    }
    $stmt->execute(); $stmt->close();
    $msg = 'Informasi kontak berhasil disimpan dan langsung tampil di dashboard orang tua!';
}

$kontak = $conn->query("SELECT * FROM kontak LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kontak Sekolah — Admin PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrap">
  <?php include 'sidebar-admin.php'; ?>
  <div class="admin-main">

    <div class="admin-topbar">
      <div class="topbar-left">
        <h1>Kontak Sekolah</h1>
        <div class="tb-sub">Informasi yang tampil di dashboard orang tua</div>
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
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?>
      </div>
      <?php endif; ?>

      <div class="page-header">
        <div>
          <h2>Informasi Kontak</h2>
          <p>Data ini ditampilkan di dashboard orang tua pada bagian "Informasi Sekolah".</p>
        </div>
      </div>

      <div class="a-card" style="max-width:600px">
        <form method="POST">
          <div class="form-group">
            <label><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-right:5px"></i>Alamat Sekolah</label>
            <textarea name="alamat" placeholder="Masukkan alamat lengkap sekolah..." rows="3"><?= htmlspecialchars($kontak['alamat'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label><i class="fab fa-whatsapp" style="color:#25d366;margin-right:5px"></i>Nomor WhatsApp</label>
            <input type="text" name="whatsapp" placeholder="Contoh: 0812-3456-7890"
              value="<?= htmlspecialchars($kontak['whatsapp'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label><i class="fas fa-envelope" style="color:var(--primary);margin-right:5px"></i>Email Sekolah</label>
            <input type="text" name="email" placeholder="Contoh: ra.annabil@gmail.com"
              value="<?= htmlspecialchars($kontak['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label><i class="fab fa-tiktok" style="margin-right:5px"></i>TikTok <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
            <input type="text" name="tiktok" placeholder="Contoh: @ra.annabil"
              value="<?= htmlspecialchars($kontak['tiktok'] ?? '') ?>">
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Informasi Kontak
          </button>
        </form>
      </div>

    </div>
  </div>
</div>
</body>
</html>