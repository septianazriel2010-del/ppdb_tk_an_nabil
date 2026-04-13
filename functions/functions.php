<?php
require_once __DIR__ . '/../config/database.php';

// Function Login
function loginUser($data) {
    global $conn;

    $email = htmlspecialchars($data['email']);
    $password = $data['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}

// Function Register
function registerUser($data) {
    global $conn;

    $nama = htmlspecialchars($data['nama']);
    $email = htmlspecialchars($data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = 'orangtua';

    

    // cek nama
    $cekNama = mysqli_query($conn, "SELECT * FROM users WHERE nama='$nama'");
    if (mysqli_fetch_assoc($cekNama)) {
        return "Nama sudah terdaftar!";
    }

    // cek email
    $cekEmail = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_fetch_assoc($cekEmail)) {
        return "Email sudah terdaftar!";
    }

    $email = $data['email'];

if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
    return "Email harus menggunakan @gmail.com!";
}

    

    mysqli_query($conn, "INSERT INTO users (nama, email, password, role) 
    VALUES ('$nama', '$email', '$password', '$role')");

    return true; // 🔥 ubah jadi true, bukan angka
}