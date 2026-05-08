<?php
session_start();
require_once '../functions/functions.php';

if (!isset($_SESSION['pending_email'])) {
    header('Location: register.php');
    exit;
}

$email   = $_SESSION['pending_email'];
$error   = '';
$success = '';

// ── Kirim ulang OTP ──────────────────────────────────────────────────────────
// ✅ FIX: Cek value === '1', bukan hanya isset().
//    Sebelumnya hidden input sudah punya name="resend" dari HTML sehingga
//    selalu terkirim dan resendOTP() terpanggil setiap kali form di-submit.
if (isset($_POST['resend']) && $_POST['resend'] === '1') {
    $result = resendOTP($email);
    if ($result === true) {
        $success = 'Kode OTP baru telah dikirim ke email Anda.';
    } else {
        $error = $result;
    }
}

// ── Verifikasi OTP ───────────────────────────────────────────────────────────
if (isset($_POST['verify'])) {
    $otp_input = trim(($_POST['otp'] ?? ''));
    $result    = verifyOTP($email, $otp_input);

    if ($result === true) {
        unset($_SESSION['pending_email']);
        echo "<script>alert('Akun berhasil diverifikasi! Silakan login.'); window.location='login.php';</script>";
        exit;
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
<title>Verifikasi OTP</title>
<style>
    .otp-inputs {
        display: flex;
        gap: .5rem;
        justify-content: center;
        margin: .5rem 0;
    }
    .otp-inputs input {
        width: 2.8rem;
        height: 2.8rem;
        text-align: center;
        font-size: 1.3rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        outline: none;
        transition: border-color .2s;
    }
    .otp-inputs input:focus {
        border-color: #4a90e2;
    }
    .otp-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .85rem;
        margin-top: .4rem;
        color: #555;
    }
    #resend-btn {
        background: none;
        border: none;
        color: #4a90e2;
        cursor: pointer;
        padding: 0;
        font-size: .85rem;
        text-decoration: underline;
    }
    #resend-btn:disabled {
        color: #aaa;
        cursor: default;
        text-decoration: none;
    }
    .email-hint {
        font-size: .85rem;
        color: #555;
        text-align: center;
        margin-bottom: 1rem;
    }
</style>
</head>
<body>
<section class="register" id="register">
<div class="login-container">
    <h2>Verifikasi OTP</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success" style="color:green;"><?= htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <p class="email-hint">
        Kode OTP (6 digit) telah dikirim ke<br>
        <strong><?= maskEmail($email); ?></strong>
    </p>

    <form method="POST" action="" id="otp-form">
        <div class="input-group">
            <label>Masukkan Kode OTP</label>
            <div class="otp-inputs" id="otp-boxes">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
            </div>
            <!-- Hidden input nilai OTP gabungan -->
            <input type="hidden" name="otp" id="otp-hidden">
        </div>

        <div class="otp-meta">
            <span>Berlaku: <span id="timer">05:00</span></span>
            <button type="button" id="resend-btn" disabled onclick="doResend()">Kirim Ulang</button>
        </div>

        <!--
            ✅ FIX: Hapus name="resend" dari sini.
            Name & value di-set oleh JavaScript hanya saat tombol Kirim Ulang diklik,
            sehingga tidak ikut terkirim saat submit verifikasi biasa.
        -->
        <input type="hidden" id="resend-trigger">

        <button type="submit" name="verify" style="margin-top:1rem;">Verifikasi</button>
        <p style="margin-top:1rem; text-align:center;">
            <a href="register.php">← Kembali ke Register</a>
        </p>
    </form>
</div>
</section>

<script>
// ── OTP box navigation ────────────────────────────────────────────────────────
const boxes  = Array.from(document.querySelectorAll('#otp-boxes input'));
const hidden = document.getElementById('otp-hidden');
const form   = document.getElementById('otp-form');

boxes.forEach((box, i) => {
    box.addEventListener('input', () => {
        box.value = box.value.replace(/\D/g, '');
        if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
        syncHidden();
    });

    box.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !box.value && i > 0) {
            boxes[i - 1].focus();
            boxes[i - 1].value = '';
            syncHidden();
        }
    });

    box.addEventListener('paste', e => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData)
            .getData('text').replace(/\D/g, '').slice(0, 6);
        pasted.split('').forEach((ch, j) => {
            if (boxes[j]) boxes[j].value = ch;
        });
        const next = Math.min(pasted.length, boxes.length - 1);
        boxes[next].focus();
        syncHidden();
    });
});

function syncHidden() {
    hidden.value = boxes.map(b => b.value).join('');
}

// Validasi sebelum submit: pastikan semua kotak terisi
form.addEventListener('submit', e => {
    syncHidden();
    if (e.submitter && e.submitter.name === 'verify') {
        if (hidden.value.length !== 6) {
            e.preventDefault();
            alert('Harap isi semua 6 digit kode OTP.');
            boxes[0].focus();
        }
    }
});

// ── Countdown timer ───────────────────────────────────────────────────────────
const EXPIRE_SECONDS = 5 * 60;
const timerEl   = document.getElementById('timer');
const resendBtn = document.getElementById('resend-btn');

let remaining = EXPIRE_SECONDS;

const tick = setInterval(() => {
    remaining--;
    if (remaining <= 0) {
        clearInterval(tick);
        timerEl.textContent = '00:00';
        timerEl.style.color = 'red';
        resendBtn.disabled  = false;
        return;
    }
    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
    const s = String(remaining % 60).padStart(2, '0');
    timerEl.textContent = `${m}:${s}`;
}, 1000);

// ✅ FIX: Set name="resend" dan value="1" hanya saat tombol ini diklik,
//    bukan dari awal di HTML. Ini mencegah resend terkirim saat verify.
function doResend() {
    const trigger = document.getElementById('resend-trigger');
    trigger.name  = 'resend';
    trigger.value = '1';
    form.submit();
}
</script>

</body>
</html>