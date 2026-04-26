<?php
session_start();
require_once '../config/database.php';
require_once '../functions/functions.php';

// Cek apakah user sudah login
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Proses ganti password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif (strlen($new_password) < 8) {
        $error = "Password baru minimal 8 karakter!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi password tidak cocok!";
    } else {
        // Ambil password dari database
        $query = "SELECT password FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            if ($user) {
                // Verifikasi password lama
                if (password_verify($old_password, $user['password'])) {
                    // Hash password baru
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update password di database dengan timestamp
                    $update_query = "UPDATE users SET password = ?, password_updated_at = NOW() WHERE email = ?";
                    $update_stmt = $conn->prepare($update_query);
                    
                    if ($update_stmt) {
                        $update_stmt->bind_param("ss", $hashed_password, $email);
                        
                        if ($update_stmt->execute()) {
                            // Redirect dengan success message
                            header('Location: profil.php?password_success=1');
                            exit;
                        } else {
                            $error = "Gagal mengubah password: " . $update_stmt->error;
                        }
                        $update_stmt->close();
                    } else {
                        $error = "Error prepare statement: " . $conn->error;
                    }
                } else {
                    $error = "Password lama tidak sesuai!";
                }
            } else {
                $error = "User tidak ditemukan!";
            }
        } else {
            $error = "Error prepare statement: " . $conn->error;
        }
    }
    
    // Jika ada error, redirect kembali ke profil dengan pesan error
    if (isset($error)) {
        header('Location: profil.php?password_error=' . urlencode($error));
        exit;
    }
} else {
    // Jika bukan POST, redirect ke profil
    header('Location: profil.php');
    exit;
}
?>
