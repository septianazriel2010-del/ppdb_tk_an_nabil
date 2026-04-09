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
<html>
<head>
<title>Register</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<section class="register" id="register">
<div class="card">
    <h2>Register</h2>

    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

    <form method="POST">

    <!-- Nama (tanpa simbol) -->
    <input 
        type="text" 
        name="nama" 
        placeholder="Nama"
        pattern="[A-Za-z0-9 ]+" 
        title="Tidak boleh memakai simbol"
        required
    >

    <!-- Email -->
    <input 
    type="email" 
    name="email" 
    placeholder="Email (@gmail.com)" 
    pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
    title="Harus menggunakan email @gmail.com"
    required
>

    <!-- Password minimal 6 -->
    <input 
        type="password" 
        name="password" 
        placeholder="Password (min 6 karakter)"
        minlength="6"
        required
    >

    <button name="register">Daftar</button>
</form>

    <p style="margin-top:1rem;">
        Sudah punya akun? <a href="login.php">Login</a>
    </p>
</div>
</section>

</body>
</html>