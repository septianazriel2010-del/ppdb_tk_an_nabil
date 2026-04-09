<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'orangtua') {
    header("Location: ../pages/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>
<?php include 'partials/header.php'; ?>

<div class="main">
    <h1>Pendaftaran</h1>

    <div class="card">
        <p>➡️ Halaman ini nanti isi form pendaftaran anak</p>
    </div>
</div>

</body>
</html>