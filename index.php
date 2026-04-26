<?php
require_once 'functions/db.php';

$pengumuman_index = [];
$res = $conn->query("SELECT id, judul, isi, gambar, tanggal FROM pengumuman ORDER BY tanggal DESC LIMIT 4");
if ($res) while($r = $res->fetch_assoc()) $pengumuman_index[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RA An-Nabil – Sekolah Anak Usia Dini</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <style>
        :root {
            --yellow: #FFD93D;
            --orange: #FF6B35;
            --green: #4CAF88;
            --sky: #4FC3F7;
            --purple: #9C6FDE;
            --pink: #FF80AB;
            --cream: #FFF8EE;
            --dark: #2D2D2D;
            --white: #FFFFFF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--cream);
            color: var(--dark);
            overflow-x: hidden;
        }

        /* ===== FLOATING SHAPES (decorative) ===== */
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.15;
            pointer-events: none;
            animation: floatBob 6s ease-in-out infinite;
        }
        @keyframes floatBob {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* ===== NAVBAR ===== */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: var(--white);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 0 2rem;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nav-logo img {
            width: 42px; height: 42px;
            border-radius: 50%;
            border: 3px solid var(--yellow);
        }
        .nav-logo span {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: var(--orange);
        }
        .nav-logo span b { color: var(--green); }
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        .nav-links a {
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--dark);
            transition: color 0.2s;
            position: relative;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0;
            width: 0; height: 3px;
            background: var(--orange);
            border-radius: 10px;
            transition: width 0.3s;
        }
        .nav-links a:hover { color: var(--orange); }
        .nav-links a:hover::after { width: 100%; }

        /* PPDB button di navbar */
        .nav-ppdb {
            background: var(--orange) !important;
            color: white !important;
            padding: 8px 18px !important;
            border-radius: 30px !important;
            font-weight: 800 !important;
            transition: background 0.2s, transform 0.2s !important;
        }
        .nav-ppdb::after { display: none !important; }
        .nav-ppdb:hover {
            background: #e85a25 !important;
            color: white !important;
            transform: translateY(-2px);
        }

        /* PPDB di mobile menu */
        .mobile-ppdb {
            background: linear-gradient(135deg, #FF6B35, #e85a25) !important;
            color: white !important;
            border-radius: 14px !important;
            margin-top: 0.3rem;
        }
        .mobile-ppdb:hover { background: #d94f1a !important; color: white !important; }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh;
            background: linear-gradient(160deg, #FFF3C4 0%, #FFE0B2 40%, #E8F5E9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            padding-top: 70px;
        }

        /* Decorative circles */
        .hero .c1 { width: 300px; height: 300px; background: var(--yellow); top: -80px; left: -80px; animation-delay: 0s; }
        .hero .c2 { width: 200px; height: 200px; background: var(--orange); bottom: -50px; right: -50px; animation-delay: 1.5s; }
        .hero .c3 { width: 120px; height: 120px; background: var(--sky); top: 20%; right: 10%; animation-delay: 3s; }
        .hero .c4 { width: 80px; height: 80px; background: var(--purple); bottom: 25%; left: 8%; animation-delay: 2s; }

        /* Stars */
        .star {
            position: absolute;
            font-size: 1.5rem;
            animation: twinkle 3s ease-in-out infinite;
            opacity: 0.5;
        }
        @keyframes twinkle {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.3); opacity: 1; }
        }

        .hero-content { position: relative; z-index: 2; text-align: center; display: flex; flex-direction: column; align-items: center; }

        .hero-badge {
            display: inline-block;
            background: var(--orange);
            color: white;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 6px 18px;
            border-radius: 30px;
            margin-bottom: 1.2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            animation: bounceIn 0.8s ease;
        }

        @keyframes bounceIn {
            0% { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .hero-logo-img {
            width: 130px; height: 130px;
            border-radius: 50%;
            border: 6px solid var(--white);
            box-shadow: 0 8px 40px rgba(255,107,53,0.25);
            margin-bottom: 1.5rem;
            animation: popIn 0.9s ease 0.2s both;
        }

        @keyframes popIn {
            0% { transform: scale(0) rotate(-10deg); opacity: 0; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        .hero h1 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2.8rem, 7vw, 5rem);
            line-height: 1.1;
            margin-bottom: 1rem;
            animation: slideUp 0.9s ease 0.3s both;
        }

        @keyframes slideUp {
            from { transform: translateY(40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .hero h1 .word-ra { color: var(--orange); }
        .hero h1 .word-an { color: var(--green); }
        .hero h1 .word-nabil { color: var(--purple); }

        .hero p.subtitle {
            font-size: 1.15rem;
            color: #555;
            max-width: 520px;
            margin: 0 auto 2rem;
            font-weight: 600;
            animation: slideUp 0.9s ease 0.5s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: slideUp 0.9s ease 0.7s both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1rem;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            border: none;
        }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .btn-primary { background: var(--orange); color: white; }
        .btn-secondary { background: var(--white); color: var(--orange); border: 2.5px solid var(--orange); }

        /* Floating emoji */
        .floating-emoji {
            position: absolute;
            font-size: 2.5rem;
            animation: floatEmoji 7s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }
        @keyframes floatEmoji {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-25px) rotate(5deg); }
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        .section-title .tag {
            display: inline-block;
            background: var(--yellow);
            color: var(--dark);
            font-weight: 800;
            font-size: 0.8rem;
            padding: 5px 16px;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.8rem;
        }
        .section-title h2 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 4vw, 3rem);
            color: var(--dark);
        }
        .section-title h2 span { color: var(--orange); }

        /* ===== STATS BANNER ===== */
        .stats-banner {
            background: var(--orange);
            padding: 2.5rem 2rem;
            display: flex;
            justify-content: center;
            gap: 0;
            flex-wrap: wrap;
        }
        .stat-item {
            text-align: center;
            padding: 1rem 3rem;
            border-right: 2px solid rgba(255,255,255,0.3);
        }
        .stat-item:last-child { border-right: none; }
        .stat-item .num {
            font-family: 'Fredoka One', cursive;
            font-size: 2.8rem;
            color: var(--yellow);
            display: block;
        }
        .stat-item .label {
            color: rgba(255,255,255,0.9);
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* ===== PROFILE SECTION ===== */
        .profile-section {
            padding: 6rem 2rem;
            background: var(--white);
            position: relative;
            overflow: hidden;
        }
        .profile-section::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 250px; height: 250px;
            background: var(--yellow);
            border-radius: 50%;
            opacity: 0.1;
        }

        .profile-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .profile-card {
            border-radius: 24px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .profile-card.visi {
            background: linear-gradient(135deg, #4FC3F7 0%, #0288D1 100%);
            color: white;
        }
        .profile-card.misi {
            background: linear-gradient(135deg, #81C784 0%, #388E3C 100%);
            color: white;
        }
        .profile-card .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .profile-card h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.8rem;
            margin-bottom: 1.2rem;
        }
        .profile-card p {
            font-size: 1rem;
            line-height: 1.8;
            opacity: 0.95;
        }
        .profile-card .visi-letter {
            display: inline-flex;
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.25);
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin-right: 8px;
            font-size: 1rem;
        }
        .visi-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .sejarah-box {
            max-width: 1100px;
            margin: 0 auto;
            background: linear-gradient(135deg, #FFF3C4, #FFE0B2);
            border-radius: 24px;
            padding: 2.5rem 3rem;
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }
        .sejarah-icon {
            font-size: 4rem;
            flex-shrink: 0;
        }
        .sejarah-box h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.8rem;
            color: var(--orange);
            margin-bottom: 1rem;
        }
        .sejarah-box p {
            line-height: 1.9;
            color: #555;
            font-size: 1rem;
        }

        /* ===== PROGRAMS SECTION ===== */
        .programs-section {
            padding: 6rem 2rem;
            background: var(--cream);
        }
        .programs-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }
        .program-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border-bottom: 5px solid transparent;
        }
        .program-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.12);
        }
        .program-card.c1 { border-color: var(--orange); }
        .program-card.c2 { border-color: var(--sky); }
        .program-card.c3 { border-color: var(--green); }
        .program-card.c4 { border-color: var(--purple); }

        .program-icon-wrap {
            width: 72px; height: 72px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.2rem;
        }
        .program-card.c1 .program-icon-wrap { background: #FFF3E0; }
        .program-card.c2 .program-icon-wrap { background: #E1F5FE; }
        .program-card.c3 .program-icon-wrap { background: #E8F5E9; }
        .program-card.c4 .program-icon-wrap { background: #F3E5F5; }

        .program-card h4 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.25rem;
            margin-bottom: 0.6rem;
            color: var(--dark);
        }
        .program-card p {
            font-size: 0.9rem;
            color: #777;
            line-height: 1.6;
        }

        /* ===== PENGUMUMAN SECTION ===== */
        .pengumuman-section {
            padding: 6rem 2rem;
            background: var(--white);
            position: relative;
        }
        .pengumuman-section::before {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 300px; height: 300px;
            background: var(--pink);
            border-radius: 50%;
            opacity: 0.07;
        }
        .pengumuman-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.8rem;
        }
        .peng-card {
            background: var(--cream);
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid transparent;
        }
        .peng-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.1);
            border-color: var(--yellow);
        }
        .peng-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .peng-placeholder {
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .peng-body { padding: 1.5rem; }
        .peng-date {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .peng-body h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        .peng-body p {
            font-size: 0.88rem;
            color: #777;
            line-height: 1.6;
        }
        .peng-colors { --c1: #FDECEA; --c2: #E8F5FD; --c3: #F3FDE8; --c4: #FAF0FD; }
        .peng-card:nth-child(1) .peng-placeholder { background: #FDECEA; }
        .peng-card:nth-child(2) .peng-placeholder { background: #E8F5FD; }
        .peng-card:nth-child(3) .peng-placeholder { background: #F3FDE8; }
        .peng-card:nth-child(4) .peng-placeholder { background: #FAF0FD; }

        /* ===== FOOTER ===== */
        footer {
            background: #2D2D2D;
            color: #ccc;
            text-align: center;
            padding: 3rem 2rem 2rem;
        }
        footer .footer-logo {
            font-family: 'Fredoka One', cursive;
            font-size: 2rem;
            color: var(--yellow);
            margin-bottom: 0.5rem;
        }
        footer p { font-size: 0.9rem; margin-bottom: 0.3rem; }
        footer .footer-bottom {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.8rem;
            color: #888;
        }
        footer .socials {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.2rem;
        }
        footer .socials a {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.2s, transform 0.2s;
        }
        footer .socials a:hover { background: var(--orange); transform: translateY(-3px); }

        /* ===== SCROLL ANIMATION ===== */
        [data-anim] {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        [data-anim].visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== HAMBURGER BUTTON ===== */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 42px; height: 42px;
            background: var(--cream);
            border: 2px solid var(--yellow);
            border-radius: 12px;
            cursor: pointer;
            padding: 8px;
            transition: background 0.2s;
            z-index: 1100;
        }
        .hamburger:hover { background: var(--yellow); }
        .hamburger span {
            display: block;
            height: 3px;
            border-radius: 3px;
            background: var(--orange);
            transition: transform 0.35s ease, opacity 0.25s ease, width 0.3s ease;
            transform-origin: center;
        }
        .hamburger span:nth-child(2) { width: 70%; align-self: flex-end; }

        /* Hamburger open state */
        .hamburger.open span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .hamburger.open span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

        /* ===== MOBILE MENU OVERLAY ===== */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 70px; left: 0; right: 0;
            background: var(--white);
            padding: 1.5rem 2rem 2rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            z-index: 999;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            transform: translateY(-10px);
            opacity: 0;
            transition: transform 0.35s ease, opacity 0.3s ease;
        }
        .mobile-menu.open {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }
        .mobile-menu ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }
        .mobile-menu ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--dark);
            padding: 0.9rem 1.2rem;
            border-radius: 14px;
            transition: background 0.2s, color 0.2s;
        }
        .mobile-menu ul li a:hover,
        .mobile-menu ul li a.active {
            background: var(--cream);
            color: var(--orange);
        }
        .mobile-menu ul li a .menu-icon {
            font-size: 1.2rem;
            width: 28px;
            text-align: center;
        }
        .mobile-menu .menu-divider {
            height: 1px;
            background: #f0e8dc;
            margin: 0.5rem 0;
        }

        /* Overlay backdrop */
        .menu-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.25);
            z-index: 998;
            top: 70px;
        }
        .menu-backdrop.open { display: block; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .nav-links { display: none; }
            .hamburger { display: flex; }
        }
        @media (max-width: 768px) {
            .profile-grid { grid-template-columns: 1fr; }
            .sejarah-box { flex-direction: column; }
            .stat-item { padding: 1rem 1.5rem; }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav>
    <a href="#beranda" class="nav-logo">
        <img src="assets/img/icon-ra-an-nabil.jpeg" alt="Logo RA An-Nabil">
        <span>RA <b>An-Nabil</b></span>
    </a>
    <ul class="nav-links">
        <li><a href="#beranda">Beranda</a></li>
        <li><a href="#profile">Profil</a></li>
        <li><a href="#program">Program</a></li>
        <li><a href="#berita">Pengumuman</a></li>
        <li><a href="#kontak">Kontak</a></li>
        <li><a href="pages/login.php" class="nav-ppdb">🎒 PPDB</a></li>
    </ul>
    <!-- Hamburger Button -->
    <button class="hamburger" id="hamburgerBtn" aria-label="Buka menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <ul>
        <li><a href="#beranda" class="mobile-link"><span class="menu-icon">🏠</span> Beranda</a></li>
        <li><a href="#profile" class="mobile-link"><span class="menu-icon">🌱</span> Profil Sekolah</a></li>
        <li><a href="#program" class="mobile-link"><span class="menu-icon">🎓</span> Program</a></li>
        <li><a href="#berita" class="mobile-link"><span class="menu-icon">📢</span> Pengumuman</a></li>
        <li><div class="menu-divider"></div></li>
        <li><a href="#kontak" class="mobile-link"><span class="menu-icon">📞</span> Kontak</a></li>
        <li><a href="ppdb.php" class="mobile-link mobile-ppdb"><span class="menu-icon">🎒</span> PPDB</a></li>
    </ul>
</div>
<!-- Backdrop -->
<div class="menu-backdrop" id="menuBackdrop"></div>

<!-- ===== HERO ===== -->
<section class="hero" id="beranda">
    <!-- Decorative circles -->
    <div class="shape c1"></div>
    <div class="shape c2"></div>
    <div class="shape c3"></div>
    <div class="shape c4"></div>

    <!-- Floating emojis -->
    <span class="floating-emoji" style="top:15%;left:5%;animation-delay:0s;">🌟</span>
    <span class="floating-emoji" style="top:70%;left:3%;animation-delay:1s;">🎨</span>
    <span class="floating-emoji" style="top:20%;right:6%;animation-delay:2s;">🎒</span>
    <span class="floating-emoji" style="top:65%;right:4%;animation-delay:0.5s;">📚</span>
    <span class="floating-emoji" style="top:85%;left:20%;animation-delay:1.5s;">🌈</span>
    <span class="floating-emoji" style="top:10%;right:20%;animation-delay:3s;">✏️</span>

    <div class="hero-content">
        <div class="hero-badge">🏫 Yayasan AN NABIL</div>
        <div style="display:flex; justify-content:center; margin-bottom:1.5rem;">
        </div>
        <h1>
            Selamat Datang di<br>
            <span class="word-ra">RA</span> <span class="word-an">An-</span><span class="word-nabil">Nabil</span>
        </h1>
        <p class="subtitle">Tempat Si Kecil Tumbuh, Bermain, dan Belajar dengan Penuh Keceriaan 🎉</p>
        <div class="hero-buttons">
            <a href="#profile" class="btn btn-primary">✨ Kenali Kami</a>
            <a href="#berita" class="btn btn-secondary">📢 Pengumuman</a>
        </div>
    </div>
</section>

<!-- ===== STATS BANNER ===== -->
<div class="stats-banner">
    <div class="stat-item">
        <span class="num">2012</span>
        <span class="label">Tahun Berdiri</span>
    </div>
    <div class="stat-item">
        <span class="num">10+</span>
        <span class="label">Tahun Pengalaman</span>
    </div>
    <div class="stat-item">
        <span class="num">C</span>
        <span class="label">Akreditasi (649)</span>
    </div>
    <div class="stat-item">
        <span class="num">100%</span>
        <span class="label">Berbasis Karakter</span>
    </div>
</div>

<!-- ===== PROFILE ===== -->
<section class="profile-section" id="profile">
    <div class="section-title" data-anim>
        <div class="tag">🌱 Tentang Kami</div>
        <h2>Profil <span>Sekolah</span></h2>
    </div>

    <div class="profile-grid">
        <div class="profile-card visi" data-anim style="transition-delay:0.1s">
            <div class="card-icon">🌟</div>
            <h3>Visi</h3>
            <?php
            $visi = [
                'B' => 'Berahlak Karimah',
                'A' => 'Aktif',
                'T' => 'Terampil',
                'I' => 'Inovatif',
                'K' => 'Kerja Sama',
            ];
            foreach ($visi as $l => $t): ?>
            <div class="visi-item">
                <span class="visi-letter"><?= $l ?></span> <?= $t ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="profile-card misi" data-anim style="transition-delay:0.2s">
            <div class="card-icon">🎯</div>
            <h3>Misi</h3>
            <p>Berakhlakul karimah dengan membiasakan 5S (Senyum, Sapa, Salam, Sopan, Santun). Anak aktif mengeksplorasi pembelajaran melalui permainan. Anak terampil dalam berkreativitas. Mengembangkan pembelajaran melalui imajinasi. Menjalin kerja sama yang baik dengan warga belajar.</p>
        </div>
    </div>

    <div class="sejarah-box" data-anim style="transition-delay:0.3s">
        <div class="sejarah-icon">🏫</div>
        <div>
            <h3>Sejarah Sekolah</h3>
            <p>RA AN NABIL merupakan lembaga pendidikan anak usia dini yang berada di bawah naungan Yayasan AN NI'MAH. Lembaga ini didirikan pada tahun 2012 dengan tujuan memberikan pendidikan dasar keagamaan dan pembentukan karakter bagi anak usia dini.</p>
            <br>
            <p>Seiring berjalannya waktu, RA AN NABIL terus berupaya meningkatkan kualitas pendidikan dan pelayanan kepada peserta didik. Pada tahun 2022, RA AN NABIL memperoleh Akreditasi C dengan nilai 649 sebagai bentuk pengakuan terhadap mutu lembaga.</p>
        </div>
    </div>
</section>

<!-- ===== PROGRAM SECTION ===== -->
<section class="programs-section" id="program">
    <div class="section-title" data-anim>
        <div class="tag">🎓 Kegiatan</div>
        <h2>Program <span>Unggulan</span></h2>
    </div>
    <div class="programs-grid">
        <div class="program-card c1" data-anim style="transition-delay:0.1s">
            <div class="program-icon-wrap">🤲</div>
            <h4>Pendidikan Agama</h4>
            <p>Pembentukan karakter islami melalui pembiasaan doa, hafalan, dan akhlak mulia.</p>
        </div>
        <div class="program-card c2" data-anim style="transition-delay:0.2s">
            <div class="program-icon-wrap">🎨</div>
            <h4>Kreativitas & Seni</h4>
            <p>Mengembangkan imajinasi anak lewat kegiatan menggambar, mewarnai, dan kerajinan tangan.</p>
        </div>
        <div class="program-card c3" data-anim style="transition-delay:0.3s">
            <div class="program-icon-wrap">🏃</div>
            <h4>Motorik & Bermain</h4>
            <p>Aktivitas fisik yang menyenangkan untuk mendukung perkembangan motorik anak.</p>
        </div>
        <div class="program-card c4" data-anim style="transition-delay:0.4s">
            <div class="program-icon-wrap">📖</div>
            <h4>Baca Tulis Hitung</h4>
            <p>Pengenalan huruf, angka, dan kata dasar secara menyenangkan dan bermakna.</p>
        </div>
    </div>
</section>

<!-- ===== PENGUMUMAN ===== -->
<section class="pengumuman-section" id="berita">
    <div class="section-title" data-anim>
        <div class="tag">📢 Info Terbaru</div>
        <h2>Pengumuman <span>Sekolah</span></h2>
    </div>

    <div class="pengumuman-grid">
        <?php
        $placeholders = ['🔔', '📝', '🎉', '📌'];
        $placeholder_bgs = ['#FDECEA','#E8F5FD','#F3FDE8','#FAF0FD'];

        if (count($pengumuman_index) > 0):
            $supported_images = ['jpg','jpeg','png','gif','webp'];
            foreach ($pengumuman_index as $i => $peng):
                $ext = strtolower(pathinfo($peng['gambar'] ?? '', PATHINFO_EXTENSION));
                $valid_img = !empty($peng['gambar']) && in_array($ext, $supported_images);
                $emoji = $placeholders[$i % 4];
                $bg = $placeholder_bgs[$i % 4];
        ?>
        <div class="peng-card" data-anim style="transition-delay:<?= $i * 0.1 ?>s">
            <?php if ($valid_img): ?>
            <img class="peng-img" src="uploads/pengumuman/<?= htmlspecialchars($peng['gambar']) ?>" alt="<?= htmlspecialchars($peng['judul']) ?>">
            <?php else: ?>
            <div class="peng-placeholder" style="background:<?= $bg ?>"><?= $emoji ?></div>
            <?php endif; ?>
            <div class="peng-body">
                <div class="peng-date"><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($peng['tanggal'])) ?></div>
                <h3><?= htmlspecialchars($peng['judul']) ?></h3>
                <p><?= htmlspecialchars(mb_substr(strip_tags($peng['isi']), 0, 110)) . (mb_strlen($peng['isi']) > 110 ? '...' : '') ?></p>
            </div>
        </div>
        <?php endforeach;
        else: ?>
        <div class="peng-card" data-anim>
            <div class="peng-placeholder" style="background:#FFF3C4">🔔</div>
            <div class="peng-body">
                <h3>Belum Ada Pengumuman</h3>
                <p>Pengumuman dari sekolah akan ditampilkan di sini. Pantau terus ya!</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer id="kontak">
    <div class="footer-logo">RA An-Nabil 🌟</div>
    <p>Di bawah naungan <strong>Yayasan AN NI'MAH</strong></p>
    <p style="margin-top:0.5rem;">📍 Alamat sekolah Anda di sini &nbsp;|&nbsp; 📞 (021) 000-0000</p>
    <div class="socials">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-whatsapp"></i></a>
    </div>
    <div class="footer-bottom">
        &copy; <?= date('Y') ?> RA An-Nabil. Dibuat dengan 💛 untuk generasi penerus bangsa.
    </div>
</footer>

<script>
// Scroll animation observer
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
    });
}, { threshold: 0.12 });
document.querySelectorAll('[data-anim]').forEach(el => observer.observe(el));

// Hamburger menu
const btn = document.getElementById('hamburgerBtn');
const menu = document.getElementById('mobileMenu');
const backdrop = document.getElementById('menuBackdrop');

function openMenu() {
    btn.classList.add('open');
    menu.classList.add('open');
    backdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeMenu() {
    btn.classList.remove('open');
    menu.classList.remove('open');
    backdrop.classList.remove('open');
    document.body.style.overflow = '';
}

btn.addEventListener('click', () => {
    btn.classList.contains('open') ? closeMenu() : openMenu();
});
backdrop.addEventListener('click', closeMenu);

// Close menu when a link is clicked
document.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', closeMenu);
});
</script>
</body>
</html>