<?php
require_once '../functions/functions.php';

if (isset($_POST['register'])) {
    $result = registerUser($_POST);

    if ($result === true) {
    echo "<script>alert('Berhasil daftar!'); window.location='login.php';</script>";
} else {
    $error = $result;
}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/style.css">
<title>Register</title>
</head>
<body>
<section class="register" id="register">
<div class="login-container">
    <h2>Register</h2>

    <?php if (isset($error)) : ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label>Nama</label>
            <input 
                type="text" 
                name="nama" 
                placeholder="Nama"
                pattern="[A-Za-z0-9 ]+" 
                title="Tidak boleh memakai simbol"
                required
            >
        </div>

        <div class="input-group">
            <label>Email</label>
            <input 
                type="email" 
                name="email" 
                placeholder="Email (@gmail.com)" 
                pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                title="Harus menggunakan email @gmail.com"
                required
            >
        </div>

        <div class="input-group">
            <label>Password</label>
            <div class="password-wrapper">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Password (min 6 karakter)"
                    minlength="6"
                    required
                >
                <span onclick="togglePassword()">👁️</span>
            </div>
        </div>

        <button type="submit" name="register">Daftar</button>
        <p style="margin-top:1rem; text-align:center;">
            Sudah punya akun? <a href="login.php">Login</a>
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