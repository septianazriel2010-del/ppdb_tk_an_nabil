<?php
session_start();
require_once '../functions/functions.php';

if (isset($_POST['login'])) {
    $user = loginUser($_POST);

    if ($user) {
        $_SESSION['login'] = true;
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];

        // redirect berdasarkan role
        if ($user['role'] == 'admin') {
            header("Location: dashboard-admin.php");
        } else {
            header("Location: dashboard-orangtua.php");
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }

    
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/style.css">
<title>Login</title>
</head>

<body>
<section class="login" id="login">
<div class="login-container">
    <h2>Login</h2>

    <?php if (isset($error)) : ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required minlength="6">
                <span onclick="togglePassword()">👁️</span>
            </div>
        </div>

        <button type="submit" name="login">Masuk</button>
        <p style="margin-top:1rem; text-align:center;">
        Belum punya akun? <a href="register.php">Daftar</a>
</p>
    </form>
</div>
</section>
<script>
// hide password
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>