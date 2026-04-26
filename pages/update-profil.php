<?php
session_start();
require_once '../config/database.php';
require_once '../functions/functions.php';

// Cek apakah user sudah login (dari session atau bisa dari database)
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_session = $_SESSION['email'];
    
    // Ambil data dari POST
    $nama = htmlspecialchars($_POST['nama'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');

    // Validasi data
    if (empty($nama)) {
        $error = "Nama tidak boleh kosong!";
    } elseif (empty($email)) {
        $error = "Email tidak boleh kosong!";
    } else {
        // Update database - hanya kolom yang ada di tabel users
        $query = "UPDATE users SET 
                    nama = ?,
                    email = ?
                  WHERE email = ?";
        
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("sss", $nama, $email, $email_session);
            
            if ($stmt->execute()) {
                // Update session dengan data terbaru
                $_SESSION['nama'] = $nama;
                $_SESSION['email'] = $email;
                
                // Redirect ke profil.php dengan success message
                header('Location: profil.php?success=1');
                exit;
            } else {
                $error = "Gagal menyimpan perubahan: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error prepare statement: " . $conn->error;
        }
    }
    
    // Jika ada error, redirect kembali ke profil dengan pesan error
    if (isset($error)) {
        header('Location: profil.php?error=' . urlencode($error));
        exit;
    }
} else {
    // Jika bukan POST, redirect ke profil
    header('Location: profil.php');
    exit;
}
?>
