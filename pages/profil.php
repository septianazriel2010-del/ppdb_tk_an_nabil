<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'orangtua') {
    header("Location: ../pages/login.php");
    exit;
}

$nama = $_SESSION['nama'] ?? 'User';
$email = $_SESSION['email'] ?? '-';
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
    <h1>Profil</h1>

    <div class="card">
        <p>Nama: <b><?= $nama; ?></b></p>
        <p>Email: <b><?= $email; ?></b></p>
    </div>
</div>

</body>
</html>