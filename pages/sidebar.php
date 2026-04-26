<?php
// sidebar.php — include di setiap halaman
// Tentukan halaman aktif sebelum include: $active_page = 'dashboard';
if (!isset($active_page)) $active_page = '';
?>
<div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>

<div class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo"><i class="fas fa-school"></i></div>
    <h2>PPDB RA An-Nabil</h2>
    <p>Portal Orang Tua</p>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-label">Menu Utama</div>
    <ul>
      <li>
        <a href="dashboard-orangtua.php" class="<?= $active_page==='dashboard' ? 'active' : '' ?>">
          <i class="fas fa-home nav-icon"></i> Beranda
        </a>
      </li>
      <li>
        <a href="info-pendaftaran.php" class="<?= $active_page==='info' ? 'active' : '' ?>">
          <i class="fas fa-info-circle nav-icon"></i> Info Pendaftaran
        </a>
      </li>
      <li>
        <a href="pendaftaran.php" class="<?= $active_page==='pendaftaran' ? 'active' : '' ?>">
          <i class="fas fa-file-alt nav-icon"></i> Pendaftaran
        </a>
      </li>
      <li>
        <a href="status.php" class="<?= $active_page==='status' ? 'active' : '' ?>">
          <i class="fas fa-chart-line nav-icon"></i> Status
        </a>
      </li>
    </ul>

    <div class="nav-label">Informasi</div>
    <ul>
      <li>
        <a href="#" id="toggleMenu" class="<?= in_array($active_page,['pengumuman','berita']) ? 'active' : '' ?>">
          <i class="fas fa-bullhorn nav-icon"></i> Informasi
          <i class="fas fa-chevron-down chevron <?= in_array($active_page,['pengumuman','berita']) ? 'open' : '' ?>" id="chevronIcon"></i>
        </a>
        <ul class="submenu <?= in_array($active_page,['pengumuman','berita']) ? 'show' : '' ?>" id="submenu">
          <li><a href="pengumuman.php" class="<?= $active_page==='pengumuman' ? 'active' : '' ?>"><i class="fas fa-bell nav-icon"></i> Pengumuman</a></li>
          <li><a href="berita.php" class="<?= $active_page==='berita' ? 'active' : '' ?>"><i class="fas fa-newspaper nav-icon"></i> Berita</a></li>
        </ul>
      </li>
      <li>
        <a href="panduan.php" class="<?= $active_page==='panduan' ? 'active' : '' ?>">
          <i class="fas fa-book nav-icon"></i> Panduan
        </a>
      </li>
    </ul>

    <div class="nav-label">Akun</div>
    <ul>
      <li>
        <a href="profil.php" class="<?= $active_page==='profil' ? 'active' : '' ?>">
          <i class="fas fa-user nav-icon"></i> Profil Akun
        </a>
      </li>
      <li>
        <a href="logout.php" class="logout-link">
          <i class="fas fa-sign-out-alt nav-icon"></i> Logout
        </a>
      </li>
    </ul>
  </nav>
</div>

<script>
  document.getElementById('hamburger').addEventListener('click', function(){
    document.querySelector('.sidebar').classList.toggle('active');
  });
  const toggleMenu = document.getElementById('toggleMenu');
  if (toggleMenu) {
    toggleMenu.addEventListener('click', function(e){
      e.preventDefault();
      document.getElementById('submenu').classList.toggle('show');
      document.getElementById('chevronIcon').classList.toggle('open');
    });
  }
</script>