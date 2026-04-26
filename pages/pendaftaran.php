<?php
require_once '../functions/db.php';
require_once '../functions/auth_orangtua.php';

$active_page = 'pendaftaran';
$user_id  = getUserId($conn);
$nama     = $_SESSION['nama'] ?? 'Orang Tua';
$inisial  = strtoupper(substr($nama, 0, 2));

$sukses = '';
$error  = '';

// Cek apakah sudah daftar
$cek = $conn->prepare("SELECT id FROM siswa WHERE user_id = ? LIMIT 1");
$cek->bind_param("i", $user_id);
$cek->execute();
$cek->store_result();
$sudah_daftar = ($cek->num_rows > 0);
$cek->close();

$siswa = null;
$dokumen = null;

if ($sudah_daftar) {
    $s = $conn->prepare("SELECT * FROM siswa WHERE user_id = ?");
    $s->bind_param("i", $user_id);
    $s->execute();
    $siswa = $s->get_result()->fetch_assoc();
    $s->close();

    $d = $conn->prepare("SELECT * FROM dokumen WHERE siswa_id = ?");
    $d->bind_param("i", $siswa['id']);
    $d->execute();
    $dokumen = $d->get_result()->fetch_assoc();
    $d->close();
}

// ── Proses submit form ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_siswa    = $_POST['nama_siswa'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat        = $_POST['alamat'];
    $nama_ayah     = $_POST['nama_ayah'];
    $nama_ibu      = $_POST['nama_ibu'];
    $no_hp         = $_POST['no_hp'];

    if (!$nama_siswa || !$jenis_kelamin || !$tanggal_lahir) {
        $error = "Field wajib belum diisi";
    } else {


        // Simpan / update data siswa
        if (!$sudah_daftar) {
            $stmt = $conn->prepare("INSERT INTO siswa (user_id,nama_siswa,jenis_kelamin,tanggal_lahir,alamat,nama_ayah,nama_ibu,no_hp,status)
            VALUES (?,?,?,?,?,?,?,?,'pending')");
            $stmt->bind_param("isssssss", $user_id,$nama_siswa,$jenis_kelamin,$tanggal_lahir,$alamat,$nama_ayah,$nama_ibu,$no_hp);
            $stmt->execute();
            $siswa_id = $conn->insert_id;
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE siswa SET nama_siswa=?,jenis_kelamin=?,tanggal_lahir=?,alamat=?,nama_ayah=?,nama_ibu=?,no_hp=? WHERE user_id=?");
            $stmt->bind_param("sssssssi",$nama_siswa,$jenis_kelamin,$tanggal_lahir,$alamat,$nama_ayah,$nama_ibu,$no_hp,$user_id);
            $stmt->execute();
            $stmt->close();
            $siswa_id = $siswa['id'];
        }

        // FIX PATH (INI PENTING)
        // Gunakan path absolut yang straightforward untuk Windows
        $base = dirname(dirname(__FILE__));
        $uploads_path = $base . '\\uploads';
        $upload_dir = $uploads_path . '\\dokumen\\';

        // Pastikan direktori ada
        if (!is_dir($uploads_path)) {
            $r1 = @mkdir($uploads_path, 0777, true);
            error_log("Created uploads dir: $r1");
        }
        if (!is_dir($upload_dir)) {
            $r2 = @mkdir($upload_dir, 0777, true);
            error_log("Created dokumen dir: $r2, exists now: " . (is_dir($upload_dir) ? 'yes' : 'no'));
        }

        $allowed = ['jpg','jpeg','png','pdf'];

        function upload_file($key, $dir) {
            if (!isset($_FILES[$key]) || $_FILES[$key]['error'] == 4) return null;

            if ($_FILES[$key]['error'] !== 0) {
                error_log("Upload error for $key: " . $_FILES[$key]['error']);
                return false;
            }

            $allowed = ['jpg','jpeg','png','pdf'];
            $ext = strtolower(pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                error_log("Invalid extension for $key: $ext");
                return false;
            }

            $nama = $key . '_' . time() . '_' . rand(100,999) . '.' . $ext;
            
            // Pastikan direktori parent ada
            if (!is_dir($dir)) {
                $mkdir_result = @mkdir($dir, 0777, true);
                error_log("mkdir($dir) returned: " . ($mkdir_result ? 'true' : 'false'));
                // Double-check setelah mkdir
                if (!is_dir($dir)) {
                    error_log("Directory still does not exist after mkdir: $dir");
                    return false;
                }
            }
            
            $filepath = $dir . $nama;
            error_log("Attempting move_uploaded_file from " . $_FILES[$key]['tmp_name'] . " to $filepath");
            
            if (!move_uploaded_file($_FILES[$key]['tmp_name'], $filepath)) {
                error_log("move_uploaded_file failed for $key to $filepath");
                return false;
            }

            error_log("Successfully uploaded $key to $filepath");
            return $nama;
        }

        $akta = upload_file('akta', $upload_dir);
        $kk   = upload_file('kk', $upload_dir);
        $foto = upload_file('foto', $upload_dir);

        // Simpan / update dokumen
        $cek_dok = $conn->prepare("SELECT id FROM dokumen WHERE siswa_id = ? LIMIT 1");
        $cek_dok->bind_param("i", $siswa_id);
        $cek_dok->execute();
        $cek_dok->store_result();
        $ada_dok = ($cek_dok->num_rows > 0);
        $cek_dok->close();

        if (!$ada_dok) {
            $ins_d = $conn->prepare("INSERT INTO dokumen (siswa_id, akta_kelahiran, kartu_keluarga, foto) VALUES (?,?,?,?)");
            // Inisialisasi variabel dengan benar untuk bind_param
            $akta_save = $akta ? $akta : '';
            $kk_save   = $kk ? $kk : '';
            $foto_save = $foto ? $foto : '';
            $ins_d->bind_param("isss", $siswa_id, $akta_save, $kk_save, $foto_save);
            $ins_d->execute();
            $ins_d->close();
        } else {
            if ($akta) {
                $upd_d = $conn->prepare("UPDATE dokumen SET akta_kelahiran=? WHERE siswa_id=?");
                $upd_d->bind_param("si", $akta, $siswa_id);
                $upd_d->execute();
                $upd_d->close();
            }
            if ($kk) {
                $upd_d = $conn->prepare("UPDATE dokumen SET kartu_keluarga=? WHERE siswa_id=?");
                $upd_d->bind_param("si", $kk, $siswa_id);
                $upd_d->execute();
                $upd_d->close();
            }
            if ($foto) {
                $upd_d = $conn->prepare("UPDATE dokumen SET foto=? WHERE siswa_id=?");
                $upd_d->bind_param("si", $foto, $siswa_id);
                $upd_d->execute();
                $upd_d->close();
            }
        }

        // Refresh data
        $s2 = $conn->prepare("SELECT * FROM siswa WHERE user_id = ? LIMIT 1");
        $s2->bind_param("i", $user_id);
        $s2->execute();
        $siswa = $s2->get_result()->fetch_assoc();
        $s2->close();

        $d2 = $conn->prepare("SELECT * FROM dokumen WHERE siswa_id = ? LIMIT 1");
        $d2->bind_param("i", $siswa['id']);
        $d2->execute();
        $dokumen = $d2->get_result()->fetch_assoc();
        $d2->close();

        $sudah_daftar = true;
        $sukses = 'Data pendaftaran berhasil disimpan! Panitia akan segera memverifikasi berkas Anda.';
    }
}

$form_pct = 0;
if ($siswa) {
    if (!empty($siswa['nama_siswa']) || !empty($siswa['jenis_kelamin']) || !empty($siswa['tanggal_lahir'])) {
        $form_pct += 20;
    }
    if (!empty($siswa['nama_ayah']) || !empty($siswa['nama_ibu'])) {
        $form_pct += 20;
    }
}
if (!empty($dokumen['akta_kelahiran'])) {
    $form_pct += 20;
}
if (!empty($dokumen['kartu_keluarga'])) {
    $form_pct += 20;
}
if (!empty($dokumen['foto'])) {
    $form_pct += 20;
}

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
  .form-group label .req { color: var(--danger); margin-left: 2px; }
  .form-group input, .form-group select, .form-group textarea {
    padding: 0.6rem 0.85rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.875rem; font-family: inherit; color: var(--text-primary);
    background: var(--bg-white);
    transition: border-color 0.18s, box-shadow 0.18s; outline: none;
  }
  .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
  }
  .form-group textarea { resize: vertical; min-height: 90px; }
  .form-section-title {
    font-size: 0.8rem; font-weight: 700; color: var(--primary);
    text-transform: uppercase; letter-spacing: 0.06em;
    margin: 1.5rem 0 1rem; padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-light);
    grid-column: 1 / -1;
  }
  .file-existing { font-size: 0.78rem; color: var(--success); margin-top: 3px; display: flex; align-items: center; gap: 0.3rem; }
  .btn-submit {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.65rem 1.75rem; background: var(--primary); color: #fff;
    border: none; border-radius: var(--radius-sm);
    font-size: 0.9rem; font-weight: 700; cursor: pointer; font-family: inherit;
    transition: background 0.18s, transform 0.15s; box-shadow: var(--shadow-blue);
  }
  .btn-submit:hover { background: var(--primary-dark); transform: translateY(-1px); }
  .form-actions { display: flex; gap: 0.75rem; align-items: center; margin-top: 1.5rem; grid-column: 1 / -1; }
  .progress-bar-wrap { background: var(--border); border-radius: 99px; height: 8px; margin-bottom: 0.5rem; }
  .progress-bar-fill { background: var(--primary); height: 8px; border-radius: 99px; transition: width 0.5s; }
  .alert-success { background: var(--success-bg); border-left: 4px solid var(--success); color: var(--success); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; padding: 0.9rem 1.1rem; margin-bottom: 1.25rem; font-size: 0.875rem; display: flex; gap: 0.6rem; align-items: flex-start; }
  .alert-error   { background: var(--danger-bg);  border-left: 4px solid var(--danger);  color: var(--danger);  border-radius: 0 var(--radius-sm) var(--radius-sm) 0; padding: 0.9rem 1.1rem; margin-bottom: 1.25rem; font-size: 0.875rem; display: flex; gap: 0.6rem; align-items: flex-start; }
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
      <div class="topbar-right"><div class="avatar-circle"><?= $inisial ?></div></div>
    </div>

    <a href="dashboard-orangtua.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <?php if ($sukses): ?>
    <div class="alert-success"><i class="fas fa-check-circle"></i> <?= $sukses ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
    <?php endif; ?>

    <div class="info-box">
      <i class="fas fa-circle-info"></i>
      <div>Pastikan semua data sesuai dengan dokumen resmi. Data yang tidak sesuai dapat menghambat proses verifikasi. Upload dokumen maksimal 2 MB per file (JPG/PNG/PDF).</div>
    </div>

    <!-- Progress kelengkapan dari database -->
    <div class="card" style="margin-bottom:1.25rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
        <div style="font-size:0.82rem;color:var(--text-secondary);font-weight:600;">Kelengkapan Formulir</div>
        <div style="font-size:0.82rem;font-weight:700;color:var(--primary);"><?= $form_pct ?>%</div>
      </div>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= $form_pct ?>%"></div></div>
      <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:0.75rem;">
        <span class="badge <?= ($siswa && $siswa['nama_siswa']) ? 'badge-blue' : 'badge-gray' ?>"><i class="fas <?= ($siswa && $siswa['nama_siswa']) ? 'fa-check' : 'fa-times' ?>"></i> Data Anak</span>
        <span class="badge <?= ($siswa && $siswa['nama_ayah']) ? 'badge-blue' : 'badge-gray' ?>"><i class="fas <?= ($siswa && $siswa['nama_ayah']) ? 'fa-check' : 'fa-times' ?>"></i> Data Orang Tua</span>
        <span class="badge <?= ($dokumen && $dokumen['akta_kelahiran']) ? 'badge-blue' : 'badge-gray' ?>"><i class="fas <?= ($dokumen && $dokumen['akta_kelahiran']) ? 'fa-check' : 'fa-times' ?>"></i> Akta</span>
        <span class="badge <?= ($dokumen && $dokumen['kartu_keluarga']) ? 'badge-blue' : 'badge-gray' ?>"><i class="fas <?= ($dokumen && $dokumen['kartu_keluarga']) ? 'fa-check' : 'fa-times' ?>"></i> KK</span>
        <span class="badge <?= ($dokumen && $dokumen['foto']) ? 'badge-blue' : 'badge-gray' ?>"><i class="fas <?= ($dokumen && $dokumen['foto']) ? 'fa-check' : 'fa-times' ?>"></i> Foto</span>
      </div>
    </div>

    <div class="card">
      <div class="card-title"><?= $sudah_daftar ? 'Perbarui Data Pendaftaran' : 'Data Pendaftaran Baru' ?></div>
      <div class="card-subtitle">Tahun Ajaran 2025/2026</div>

      <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">

          <div class="form-section-title"><i class="fas fa-child"></i> Data Calon Peserta Didik</div>

          <div class="form-group">
            <label>Nama Lengkap Anak <span class="req">*</span></label>
            <input type="text" name="nama_siswa" value="<?= htmlspecialchars($siswa['nama_siswa'] ?? '') ?>" placeholder="Sesuai akta kelahiran" required>
          </div>
          <div class="form-group">
            <label>Jenis Kelamin <span class="req">*</span></label>
            <select name="jenis_kelamin" required>
              <option value="">-- Pilih --</option>
              <option value="L" <?= ($siswa['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
              <option value="P" <?= ($siswa['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Lahir <span class="req">*</span></label>
            <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($siswa['tanggal_lahir'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>No. HP / WhatsApp Orang Tua <span class="req">*</span></label>
            <input type="tel" name="no_hp" value="<?= htmlspecialchars($siswa['no_hp'] ?? '') ?>" placeholder="08xxxxxxxxxx">
          </div>
          <div class="form-group full">
            <label>Alamat Tempat Tinggal</label>
            <textarea name="alamat" placeholder="Jl. Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota"><?= htmlspecialchars($siswa['alamat'] ?? '') ?></textarea>
          </div>

          <div class="form-section-title"><i class="fas fa-users"></i> Data Orang Tua</div>

          <div class="form-group">
            <label>Nama Ayah</label>
            <input type="text" name="nama_ayah" value="<?= htmlspecialchars($siswa['nama_ayah'] ?? '') ?>" placeholder="Nama lengkap ayah">
          </div>
          <div class="form-group">
            <label>Nama Ibu</label>
            <input type="text" name="nama_ibu" value="<?= htmlspecialchars($siswa['nama_ibu'] ?? '') ?>" placeholder="Nama lengkap ibu">
          </div>

          <div class="form-section-title"><i class="fas fa-paperclip"></i> Upload Dokumen</div>

          <div class="form-group">
            <label>Akta Kelahiran (JPG/PNG/PDF, maks 2 MB)</label>
            <input type="file" name="akta" accept=".pdf,.jpg,.jpeg,.png">
            <?php if (!empty($dokumen['akta_kelahiran'])): ?>
            <div class="file-existing"><i class="fas fa-check-circle"></i> Sudah upload: <?= htmlspecialchars($dokumen['akta_kelahiran']) ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <label>Kartu Keluarga (JPG/PNG/PDF, maks 2 MB)</label>
            <input type="file" name="kk" accept=".pdf,.jpg,.jpeg,.png">
            <?php if (!empty($dokumen['kartu_keluarga'])): ?>
            <div class="file-existing"><i class="fas fa-check-circle"></i> Sudah upload: <?= htmlspecialchars($dokumen['kartu_keluarga']) ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <label>Pas Foto (JPG/PNG, maks 2 MB, latar merah)</label>
            <input type="file" name="foto" accept=".jpg,.jpeg,.png">
            <?php if (!empty($dokumen['foto'])): ?>
            <div class="file-existing"><i class="fas fa-check-circle"></i> Sudah upload: <?= htmlspecialchars($dokumen['foto']) ?></div>
            <?php endif; ?>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-submit">
              <i class="fas fa-paper-plane"></i>
              <?= $sudah_daftar ? 'Perbarui Data' : 'Kirim Pendaftaran' ?>
            </button>
          </div>

        </div>
      </form>
    </div>

  </div>
</div>
</body>
</html>