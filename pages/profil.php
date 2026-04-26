<?php
session_start();
require_once '../config/database.php';
require_once '../functions/functions.php';
$active_page = 'profil';
$nama = $_SESSION['nama'] ?? 'Orang Tua';
$email = $_SESSION['email'] ?? 'orangtua@email.com';
$inisial = strtoupper(substr($nama, 0, 2));

// Ambil info user dari database untuk mendapatkan waktu update password
$password_updated = 'Belum pernah diubah';
if (isset($_SESSION['email'])) {
    $query = "SELECT password_updated_at FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user && !empty($user['password_updated_at'])) {
            $timestamp = $user['password_updated_at'];
            $password_updated = date('d F Y H:i', strtotime($timestamp));
        }
        $stmt->close();
    }
}

$sukses = isset($_GET['success']) ? "Data profil berhasil diperbarui!" : '';
$sukses = isset($_GET['password_success']) ? "Password berhasil diubah!" : $sukses;
$error = isset($_GET['error']) ? $_GET['error'] : '';
$error = isset($_GET['password_error']) ? $_GET['password_error'] : $error;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Akun — Dashboard PPDB</title>
<link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
  .profil-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border-radius: var(--radius-lg);
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    color: #fff;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }
  .avatar-lg {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: rgba(255,255,255,0.25);
    border: 3px solid rgba(255,255,255,0.5);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
  }
  .profil-header h2 { font-size: 1.3rem; font-weight: 800; margin-bottom: 4px; }
  .profil-header p  { font-size: 0.85rem; opacity: 0.85; }

  .grid-profil { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }

  .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
  .form-group.full { grid-column: 1 / -1; }
  .form-group label { font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); }
  .form-group input, .form-group select {
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
  .form-group input:focus, .form-group select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
  }
  .form-group input[readonly] { background: var(--bg-surface); color: var(--text-muted); cursor: not-allowed; }

  .btn-save {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.6rem 1.5rem;
    background: var(--primary);
    color: #fff; border: none;
    border-radius: var(--radius-sm);
    font-size: 0.875rem; font-weight: 700;
    cursor: pointer; font-family: inherit;
    transition: background 0.18s, transform 0.15s;
    box-shadow: var(--shadow-blue);
  }
  .btn-save:hover { background: var(--primary-dark); transform: translateY(-1px); }

  .divider { height: 1px; background: var(--border); margin: 1.5rem 0; }

  .security-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.85rem 0;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap; gap: 0.75rem;
  }
  .security-item:last-child { border-bottom: none; }
  .security-item .left h4 { font-size: 0.88rem; font-weight: 600; color: var(--text-primary); }
  .security-item .left p  { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }
  .btn-outline {
    padding: 0.45rem 1rem;
    border: 1.5px solid var(--primary);
    color: var(--primary);
    background: var(--bg-white);
    border-radius: var(--radius-sm);
    font-size: 0.8rem; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: all 0.18s;
  }
  .btn-outline:hover { background: var(--primary-light); }

  @media(max-width:600px){ .grid-profil{ grid-template-columns:1fr; } .form-group.full{ grid-column:1; } }
</style>
</head>
<body>
<div class="container-dashboard-orangtua">
  <?php include 'sidebar.php'; ?>

  <div class="content-isi">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Profil Akun</h1>
        <p>Kelola informasi akun Anda</p>
      </div>
      <div class="topbar-right">
        <div class="avatar-circle"><?= $inisial ?></div>
      </div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <?php if ($sukses): ?>
    <div style="background:var(--success-bg); border-left:4px solid var(--success); color:var(--success); border-radius:0 var(--radius-sm) var(--radius-sm) 0; padding:0.9rem 1.1rem; margin-bottom:1.25rem; font-size:0.875rem; display:flex; gap:0.6rem; align-items:flex-start;">
      <i class="fas fa-check-circle"></i> <?= $sukses ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div style="background:var(--danger-bg); border-left:4px solid var(--danger); color:var(--danger); border-radius:0 var(--radius-sm) var(--radius-sm) 0; padding:0.9rem 1.1rem; margin-bottom:1.25rem; font-size:0.875rem; display:flex; gap:0.6rem; align-items:flex-start;">
      <i class="fas fa-exclamation-circle"></i> <?= $error ?>
    </div>
    <?php endif; ?>

    <!-- Header profil -->
    <div class="profil-header">
      <div class="avatar-lg"><?= $inisial ?></div>
      <div>
        <h2><?= htmlspecialchars($nama) ?></h2>
        <p><?= htmlspecialchars($email) ?></p>
        <p style="margin-top:6px;"><span style="background:rgba(255,255,255,0.2); padding:3px 12px; border-radius:99px; font-size:0.75rem; font-weight:700;">Orang Tua / Wali</span></p>
      </div>
    </div>

    <div class="tab-nav">
      <button class="tab-btn active" onclick="switchTab('data', this)"><i class="fas fa-user"></i> Data Diri</button>
      <button class="tab-btn" onclick="switchTab('keamanan', this)"><i class="fas fa-shield-alt"></i> Keamanan</button>
    </div>

    <!-- Tab Data Diri -->
    <div class="tab-panel active" id="tab-data">
      <div class="card">
        <div class="card-title">Informasi Pribadi</div>
        <div class="card-subtitle">Perbarui data diri Anda</div>
        <form method="POST" action="update-profil.php">
          <div class="grid-profil">
            <div class="form-group">
              <label>Nama Lengkap</label>
              <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
          </div>
          <div style="margin-top:1.25rem;">
            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tab Keamanan -->
    <div class="tab-panel" id="tab-keamanan">
      <div class="card">
        <div class="card-title">Keamanan Akun</div>
        <div class="card-subtitle">Kelola password dan keamanan akun Anda</div>

        <div class="security-item">
          <div class="left">
            <h4>Kata Sandi</h4>
            <p>Terakhir diubah: <?= $password_updated ?></p>
          </div>
          <button class="btn-outline" onclick="document.getElementById('formPassword').style.display='block'">Ubah Kata Sandi</button>
        </div>

        <div id="formPassword" style="display:none; margin-top:1rem;">
          <form method="POST" action="ganti-password.php">
            <div class="grid-profil">
              <div class="form-group full">
                <label>Kata Sandi Lama</label>
                <input type="password" name="old_password" placeholder="Masukkan kata sandi lama">
              </div>
              <div class="form-group">
                <label>Kata Sandi Baru</label>
                <input type="password" name="new_password" placeholder="Minimal 8 karakter">
              </div>
              <div class="form-group">
                <label>Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="confirm_password" placeholder="Ulangi kata sandi baru">
              </div>
            </div>
            <div style="margin-top:1rem;">
              <button type="submit" class="btn-save"><i class="fas fa-key"></i> Perbarui Kata Sandi</button>
            </div>
          </form>
        </div>

        <div class="security-item">
          <div class="left">
            <h4>Sesi Aktif</h4>
            <p>Anda login dari perangkat ini</p>
          </div>
          <a href="logout.php" class="btn-outline" style="color:var(--danger); border-color:var(--danger); background:var(--danger-bg);">
            <i class="fas fa-sign-out-alt"></i> Logout Semua Sesi
          </a>
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
</script>
</body>
</html>