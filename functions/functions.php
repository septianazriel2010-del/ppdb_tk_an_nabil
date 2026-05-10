<?php
date_default_timezone_set('Asia/Jakarta');

/**
 * functions/functions.php — Fungsi utama aplikasi
 *
 * CHANGELOG FIX:
 *  - generateAndSendOTP : date() → gmdate() agar expires_at tersimpan UTC
 *  - verifyOTP          : NOW()  → UTC_TIMESTAMP() agar perbandingan waktu konsisten
 */

require_once __DIR__ . '/../config/database.php';

// ════════════════════════════════════════════════════════════════════════════
// KONFIGURASI EMAIL OTP
// ════════════════════════════════════════════════════════════════════════════
define('MAIL_FROM',    'raannabilVerif@gmail.com');
define('MAIL_NAME',    'ppdb-tk');
define('MAIL_PASS',    'bsou pcnx zpda nydz');
define('OTP_LIFETIME', 5 * 60);


// ════════════════════════════════════════════════════════════════════════════
// loginUser()
// ════════════════════════════════════════════════════════════════════════════
function loginUser(array $data): array|string
{
    global $conn;

    $email    = trim($data['email']    ?? '');
    $password = trim($data['password'] ?? '');

    if (!$email || !$password) {
        return 'Email dan password wajib diisi.';
    }

    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user || !password_verify($password, $user['password'])) {
        return 'Email atau password salah.';
    }

    if (!(int)$user['is_verified']) {
        return 'EMAIL_NOT_VERIFIED';
    }

    return $user;
}


// ════════════════════════════════════════════════════════════════════════════
// registerUser()
// ════════════════════════════════════════════════════════════════════════════
function registerUser(array $data): bool|string
{
    global $conn;

    $nama     = trim($data['nama']     ?? '');
    $email    = trim($data['email']    ?? '');
    $password = trim($data['password'] ?? '');
    $role     = 'orangtua';

    if (!$nama || !$email || !$password) {
        return 'Semua field wajib diisi.';
    }
    if (!preg_match('/^[A-Za-z0-9 ]+$/', $nama)) {
        return 'Nama tidak boleh menggunakan simbol.';
    }
    if (!preg_match('/^[a-zA-Z0-9._%+\-]+@gmail\.com$/', $email)) {
        return 'Email harus menggunakan @gmail.com!';
    }
    if (strlen($password) < 6) {
        return 'Password minimal 6 karakter.';
    }

    $stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE nama = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $nama);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        return 'Nama sudah terdaftar!';
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, 'SELECT id, is_verified FROM users WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result   = mysqli_stmt_get_result($stmt);
    $existing = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($existing) {
        if ((int)$existing['is_verified'] === 1) {
            return 'Email sudah terdaftar! Silakan login.';
        }
        $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = mysqli_prepare($conn,
        'INSERT INTO users (nama, email, password, role, is_verified) VALUES (?, ?, ?, ?, 0)'
    );
    mysqli_stmt_bind_param($stmt, 'ssss', $nama, $email, $hash, $role);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return generateAndSendOTP($email);
}


// ════════════════════════════════════════════════════════════════════════════
// generateAndSendOTP()
// ════════════════════════════════════════════════════════════════════════════
function generateAndSendOTP(string $email): bool|string
{
    global $conn;

    $stmt = mysqli_prepare($conn, 'DELETE FROM otp_tokens WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // ✅ FIX: gmdate() → simpan expires_at dalam UTC, bukan local time.
    //    Konsisten dengan UTC_TIMESTAMP() di query verifyOTP.
    $expiresAt = gmdate('Y-m-d H:i:s', time() + OTP_LIFETIME);

    $stmt = mysqli_prepare($conn,
        'INSERT INTO otp_tokens (email, token, expires_at) VALUES (?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'sss', $email, $otp, $expiresAt);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!sendOTPEmail($email, $otp)) {
        return 'Gagal mengirim email OTP. Coba lagi nanti.';
    }

    return true;
}


// ════════════════════════════════════════════════════════════════════════════
// verifyOTP()
// ════════════════════════════════════════════════════════════════════════════
function verifyOTP(string $email, string $otpInput): bool|string
{
    global $conn;

    if (!preg_match('/^\d{6}$/', $otpInput)) {
        return 'Kode OTP harus 6 digit angka.';
    }

    // ✅ FIX: UTC_TIMESTAMP() → bandingkan waktu dalam UTC,
    //    konsisten dengan expires_at yang disimpan via gmdate().
    $stmt = mysqli_prepare($conn,
        'SELECT * FROM otp_tokens
          WHERE email = ? AND used = 0 AND expires_at > UTC_TIMESTAMP()
          ORDER BY id DESC LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$row) {
        return 'Kode OTP tidak valid atau sudah kedaluwarsa.';
    }

    if (!hash_equals($row['token'], $otpInput)) {
        return 'Kode OTP salah.';
    }

    $stmt = mysqli_prepare($conn, 'UPDATE otp_tokens SET used = 1 WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $row['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, 'UPDATE users SET is_verified = 1 WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return true;
}


// ════════════════════════════════════════════════════════════════════════════
// resendOTP()
// ════════════════════════════════════════════════════════════════════════════
function resendOTP(string $email): bool|string
{
    global $conn;

    $stmt = mysqli_prepare($conn,
        'SELECT id FROM users WHERE email = ? AND is_verified = 0 LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $found = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if (!$found) {
        return 'Akun tidak ditemukan atau sudah terverifikasi.';
    }

    return generateAndSendOTP($email);
}


// ════════════════════════════════════════════════════════════════════════════
// maskEmail()
// ════════════════════════════════════════════════════════════════════════════
function maskEmail(string $email): string
{
    [$local, $domain] = explode('@', $email, 2);
    $len    = strlen($local);
    $masked = $len <= 2
        ? str_repeat('*', $len)
        : $local[0] . str_repeat('*', max(1, $len - 2)) . $local[$len - 1];
    return $masked . '@' . $domain;
}


// ════════════════════════════════════════════════════════════════════════════
// sendOTPEmail()
// ════════════════════════════════════════════════════════════════════════════
function sendOTPEmail(string $toEmail, string $otp): bool
{
    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_FROM;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_NAME);
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Kode OTP Verifikasi Akun - ' . MAIL_NAME;
        $mail->Body    = buildOTPEmailBody($otp);

        $mail->send();
        return true;

    } catch (PHPMailer\PHPMailer\Exception $e) {
        error_log('PHPMailer Error: ' . $mail->ErrorInfo);
        die('SMTP Error: ' . $mail->ErrorInfo);
        return false;
    }
}


// ════════════════════════════════════════════════════════════════════════════
// buildOTPEmailBody()
// ════════════════════════════════════════════════════════════════════════════
function buildOTPEmailBody(string $otp): string
{
    $minutes = (int)(OTP_LIFETIME / 60);
    return <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head><meta charset="UTF-8"></head>
    <body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px;">
      <div style="max-width:480px;margin:auto;background:#fff;border-radius:8px;
                  padding:32px;box-shadow:0 2px 8px rgba(0,0,0,.08);">
        <h2 style="color:#333;margin-top:0;">Verifikasi Akun Anda</h2>
        <p style="color:#555;">Gunakan kode OTP berikut untuk mengaktifkan akun Anda:</p>
        <div style="text-align:center;margin:24px 0;">
          <span style="display:inline-block;font-size:2.2rem;font-weight:700;
                       letter-spacing:.4rem;color:#4a90e2;background:#eef4fd;
                       padding:12px 24px;border-radius:8px;">{$otp}</span>
        </div>
        <p style="color:#555;">Kode ini berlaku selama <strong>{$minutes} menit</strong>.</p>
        <p style="color:#888;font-size:.85rem;margin-top:24px;">
          Jika Anda tidak mendaftar, abaikan email ini.
        </p>
      </div>
    </body>
    </html>
    HTML;
}