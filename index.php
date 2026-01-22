<?php
// index.php
session_start();

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'guru') {
        header('Location: admin/dashboard.php');
    } else if ($_SESSION['role'] === 'siswa') {
        header('Location: student/dashboard.php');
    }
    exit();
}

// Cek jika ada pesan logout di URL
$logout_message = '';
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $logout_message = 'Anda telah berhasil logout.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>MATHLine - Pembelajaran PBL Matematika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #10B981;
            --secondary-green: #059669;
            --light-green: #D1FAE5;
            --accent-green: #34D399;
            --dark-green: #065F46;
            --primary-blue: #3B82F6;
            --secondary-blue: #2563EB;
            --light-blue: #DBEAFE;
            --accent-blue: #60A5FA;
            --dark-blue: #1E40AF;
            --gradient-green-blue: linear-gradient(135deg, #10B981 0%, #3B82F6 100%);
            --gradient-light: linear-gradient(135deg, #D1FAE5 0%, #DBEAFE 100%);
            --gradient-teal: linear-gradient(135deg, #0D9488 0%, #0891B2 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* ========== FIX UTAMA UNTUK FOOTER ========== */
        html, body {
            height: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }
        
        /* Main content wrapper - MENYEDIAKAN RUANG UNTUK FOOTER */
        .main-content {
            flex: 1 0 auto;
            width: 100%;
            padding-bottom: 20px; /* Ruang antara konten dan footer */
        }
        
        /* ========== HERO SECTION ========== */
        .hero-section {
            background: var(--gradient-green-blue);
            color: white;
            padding: 60px 0 30px;
            position: relative;
            overflow: hidden;
            min-height: auto;
        }
        
        @media (min-width: 576px) {
            .hero-section {
                padding: 70px 0 40px;
            }
        }
        
        @media (min-width: 768px) {
            .hero-section {
                padding: 100px 0 80px;
                min-height: 85vh;
            }
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 0%, transparent 50%);
        }
        
        /* ========== BUTTONS ========== */
        .btn-gradient {
            background: var(--gradient-green-blue);
            border: none;
            color: white;
            padding: 14px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
            width: 100%;
            max-width: 280px;
            margin: 0 auto 10px;
            display: block;
            min-height: 52px;
        }
        
        @media (min-width: 576px) {
            .btn-gradient {
                max-width: 300px;
            }
        }
        
        @media (min-width: 768px) {
            .btn-gradient {
                padding: 16px 36px;
                font-size: 1.1rem;
                box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
                width: auto;
                max-width: none;
                margin: 0 10px 0 0;
                display: inline-block;
                min-height: auto;
                border-radius: 12px;
            }
        }
        
        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.7s ease;
            z-index: -1;
        }
        
        .btn-gradient:hover, .btn-gradient:active {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            color: white;
        }
        
        @media (min-width: 768px) {
            .btn-gradient:hover {
                transform: translateY(-5px) scale(1.05);
                box-shadow: 0 12px 30px rgba(16, 185, 129, 0.4);
            }
        }
        
        .btn-gradient:hover::before, .btn-gradient:active::before {
            left: 100%;
        }
        
        .btn-outline-green {
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            padding: 14px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.4s ease;
            background: transparent;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            display: block;
            min-height: 52px;
        }
        
        @media (min-width: 576px) {
            .btn-outline-green {
                max-width: 300px;
            }
        }
        
        @media (min-width: 768px) {
            .btn-outline-green {
                padding: 14px 32px;
                width: auto;
                max-width: none;
                display: inline-block;
                margin: 0 10px 0 0;
                min-height: auto;
                border-radius: 12px;
            }
        }
        
        .btn-outline-green:hover, .btn-outline-green:active {
            background: var(--primary-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.2);
        }
        
        @media (min-width: 768px) {
            .btn-outline-green:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2);
            }
        }
        
        /* ========== FOOTER - DIPERBAIKI UNTUK HP ========== */
        .footer-elegant {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: white;
            padding: 30px 0 20px;
            position: relative;
            overflow: hidden;
            width: 100%;
            flex-shrink: 0;
            margin-top: auto;
        }
        
        @media (max-width: 767px) {
            .footer-elegant {
                padding: 25px 0 20px;
                padding-bottom: calc(20px + env(safe-area-inset-bottom, 0)); /* Safe area untuk iPhone */
            }
        }
        
        @media (min-width: 768px) {
            .footer-elegant {
                padding: 60px 0 30px;
            }
        }
        
        @media (min-width: 992px) {
            .footer-elegant {
                padding: 80px 0 40px;
            }
        }
        
        .footer-elegant::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-green-blue);
        }
        
        .footer-elegant .logo {
            margin-bottom: 15px;
            display: inline-block;
        }
        
        @media (min-width: 768px) {
            .footer-elegant .logo {
                margin-bottom: 20px;
            }
        }
        
        .footer-contact {
            margin-top: 15px;
        }
        
        @media (min-width: 768px) {
            .footer-contact {
                margin-top: 20px;
            }
        }
        
        .footer-contact a {
            color: var(--accent-green);
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            padding: 6px 0;
            border-radius: 6px;
            position: relative;
            padding-left: 0;
            font-size: 0.9rem;
            word-break: break-word;
            line-height: 1.4;
        }
        
        @media (max-width: 575px) {
            .footer-contact a {
                font-size: 0.85rem;
                margin-bottom: 6px;
            }
        }
        
        @media (min-width: 768px) {
            .footer-contact a {
                margin-bottom: 12px;
                font-size: 1rem;
                padding: 8px 0;
            }
        }
        
        .footer-contact a::before {
            content: '→';
            position: absolute;
            left: -20px;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .footer-contact a:hover, .footer-contact a:active {
            color: white;
            padding-left: 20px;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .footer-contact a:hover::before, .footer-contact a:active::before {
            left: 0;
            opacity: 1;
        }
        
        .social-icons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 575px) {
            .social-icons {
                gap: 8px;
                margin-top: 15px;
            }
        }
        
        @media (min-width: 768px) {
            .social-icons {
                gap: 15px;
                margin-top: 25px;
            }
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.08);
            color: white;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        @media (min-width: 576px) {
            .social-icons a {
                width: 44px;
                height: 44px;
            }
        }
        
        @media (min-width: 768px) {
            .social-icons a {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
                border-radius: 12px;
            }
        }
        
        .social-icons a:hover, .social-icons a:active {
            background: var(--gradient-green-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        @media (min-width: 768px) {
            .social-icons a:hover {
                transform: translateY(-3px) rotate(5deg);
                box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            }
        }
        
        /* ========== NAVIGATION ========== */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 10px 0;
            transition: all 0.3s ease;
        }
        
        @media (min-width: 768px) {
            .navbar {
                padding: 15px 0;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }
        }
        
        .navbar-toggler {
            border: none;
            padding: 8px;
            font-size: 1.25rem;
            min-height: 44px;
            min-width: 44px;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
        }
        
        /* ========== TYPOGRAPHY ========== */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
        }
        
        .hero-title {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        @media (min-width: 375px) {
            .hero-title {
                font-size: 2rem;
            }
        }
        
        @media (min-width: 576px) {
            .hero-title {
                font-size: 2.5rem;
                text-align: left;
            }
        }
        
        @media (min-width: 768px) {
            .hero-title {
                font-size: 3rem;
                font-weight: 800;
                margin-bottom: 1.5rem;
            }
        }
        
        @media (min-width: 992px) {
            .hero-title {
                font-size: 3.5rem;
            }
        }
        
        /* ========== SECTION SPACING ========== */
        section {
            padding: 40px 0;
        }
        
        @media (min-width: 576px) {
            section {
                padding: 50px 0;
            }
        }
        
        @media (min-width: 768px) {
            section {
                padding: 80px 0;
            }
        }
        
        @media (min-width: 992px) {
            section {
                padding: 100px 0;
            }
        }
        
        /* ========== HERO CONTENT ========== */
        .hero-content {
            position: relative;
            z-index: 2;
            margin-bottom: 25px;
        }
        
        @media (min-width: 576px) {
            .hero-content {
                margin-bottom: 30px;
            }
        }
        
        @media (min-width: 768px) {
            .hero-content {
                margin-bottom: 0;
            }
        }
        
        .hero-illustration {
            position: relative;
            z-index: 2;
        }
        
        @media (max-width: 767px) {
            .hero-illustration {
                margin-top: 20px;
            }
        }
        
        /* ========== ANIMATIONS ========== */
        .math-line-animation {
            position: absolute;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            animation: lineMove 3s linear infinite;
            bottom: 0;
        }
        
        @keyframes lineMove {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-green-blue);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        
        @media (min-width: 768px) {
            .logo {
                font-size: 1.8rem;
                font-weight: 800;
            }
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        
        .floating-element {
            position: absolute;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.08);
            animation: float 8s ease-in-out infinite;
            font-weight: bold;
            z-index: 1;
        }
        
        @media (min-width: 576px) {
            .floating-element {
                font-size: 1.2rem;
                color: rgba(255, 255, 255, 0.1);
            }
        }
        
        @media (min-width: 768px) {
            .floating-element {
                font-size: 2.2rem;
                color: rgba(255, 255, 255, 0.15);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            50% { transform: translateY(-10px) rotate(3deg) scale(1.03); }
        }
        
        @media (min-width: 768px) {
            @keyframes float {
                0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
                50% { transform: translateY(-30px) rotate(15deg) scale(1.1); }
            }
        }
        
        .floating-element:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; }
        .floating-element:nth-child(2) { top: 70%; right: 5%; animation-delay: 1.5s; }
        .floating-element:nth-child(3) { bottom: 20%; left: 10%; animation-delay: 3s; }
        .floating-element:nth-child(4) { top: 40%; right: 10%; animation-delay: 4.5s; }
        
        @media (min-width: 768px) {
            .floating-element:nth-child(1) { top: 20%; left: 10%; }
            .floating-element:nth-child(2) { top: 60%; right: 15%; }
            .floating-element:nth-child(3) { bottom: 30%; left: 20%; }
            .floating-element:nth-child(4) { top: 40%; right: 25%; }
        }
        
        /* ========== LOGOUT ALERT ========== */
        .logout-alert {
            position: relative;
            z-index: 9999;
            animation: slideIn 0.5s ease-out;
            max-width: 400px;
            margin: 15px auto 0;
            padding: 0 15px;
        }
        
        @media (min-width: 768px) {
            .logout-alert {
                position: fixed;
                top: 100px;
                right: 30px;
                left: auto;
                max-width: 320px;
                margin: 0;
                padding: 0;
            }
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* ========== SHAPES ========== */
        .shape-1 {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 40% 60% 60% 40% / 60% 30% 70% 40%;
            background: linear-gradient(45deg, rgba(16, 185, 129, 0.05), rgba(59, 130, 246, 0.05));
            top: 10%;
            right: 5%;
            animation: morph 15s ease-in-out infinite;
            opacity: 0.4;
        }
        
        .shape-2 {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.05), rgba(16, 185, 129, 0.05));
            bottom: 15%;
            left: 5%;
            animation: morph 12s ease-in-out infinite reverse;
            opacity: 0.4;
        }
        
        @media (min-width: 576px) {
            .shape-1, .shape-2 {
                opacity: 0.5;
            }
            .shape-1 {
                width: 120px;
                height: 120px;
            }
            .shape-2 {
                width: 100px;
                height: 100px;
            }
        }
        
        @media (min-width: 768px) {
            .shape-1, .shape-2 {
                opacity: 1;
            }
            .shape-1 {
                width: 300px;
                height: 300px;
                top: 10%;
                right: 10%;
                background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.1));
            }
            .shape-2 {
                width: 200px;
                height: 200px;
                bottom: 10%;
                left: 10%;
                background: linear-gradient(45deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
            }
        }
        
        @keyframes morph {
            0%, 100% { border-radius: 40% 60% 60% 40% / 60% 30% 70% 40%; }
            50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
        }
        
        /* ========== GRADIENT TEXT ========== */
        .gradient-text {
            background: linear-gradient(90deg, #10B981, #3B82F6, #10B981);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient 3s linear infinite;
            display: block;
            margin-bottom: 10px;
            font-size: 1.6rem;
        }
        
        @media (min-width: 375px) {
            .gradient-text {
                font-size: 1.8rem;
            }
        }
        
        @media (min-width: 576px) {
            .gradient-text {
                font-size: 2.5rem;
            }
        }
        
        @media (min-width: 768px) {
            .gradient-text {
                font-size: 3.5rem;
                display: inline;
                margin-bottom: 0;
            }
        }
        
        @keyframes gradient {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        
        /* ========== PARTICLES ========== */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            width: 1px;
            height: 1px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: particleFloat linear infinite;
        }
        
        @media (min-width: 576px) {
            .particle {
                width: 2px;
                height: 2px;
                background: rgba(255, 255, 255, 0.4);
            }
        }
        
        @media (min-width: 768px) {
            .particle {
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, 0.6);
            }
        }
        
        @keyframes particleFloat {
            from { transform: translateY(100vh); }
            to { transform: translateY(-100px); }
        }
        
        /* ========== GRAPH ========== */
        .graph-container {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
        }
        
        @media (min-width: 576px) {
            .graph-container {
                max-width: 400px;
            }
        }
        
        @media (min-width: 768px) {
            .graph-container {
                max-width: 500px;
            }
        }
        
        .graph-container svg {
            width: 100%;
            height: auto;
            max-height: 220px;
            filter: drop-shadow(0 3px 10px rgba(0,0,0,0.1));
        }
        
        @media (min-width: 576px) {
            .graph-container svg {
                max-height: 250px;
                filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1));
            }
        }
        
        @media (min-width: 768px) {
            .graph-container svg {
                max-height: 300px;
                filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));
            }
        }
        
        /* ========== CONTAINER ========== */
        .container {
            padding-left: 15px;
            padding-right: 15px;
            max-width: 100%;
        }
        
        @media (min-width: 576px) {
            .container {
                padding-left: 20px;
                padding-right: 20px;
            }
        }
        
        @media (min-width: 768px) {
            .container {
                padding-left: 30px;
                padding-right: 30px;
                max-width: 1140px;
            }
        }
        
        /* ========== MOBILE NAVIGATION ========== */
        .navbar-collapse {
            background: white;
            border-radius: 10px;
            margin-top: 10px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        @media (min-width: 768px) {
            .navbar-collapse {
                background: transparent;
                margin-top: 0;
                padding: 0;
                box-shadow: none;
            }
        }
        
        .navbar-nav .nav-item {
            width: 100%;
            margin: 5px 0;
        }
        
        .navbar-nav .nav-item .btn {
            width: 100%;
            justify-content: center;
        }
        
        @media (min-width: 768px) {
            .navbar-nav .nav-item {
                width: auto;
                margin: 0;
            }
            
            .navbar-nav .nav-item .btn {
                width: auto;
            }
        }
        
        /* ========== LEAD TEXT ========== */
        .lead {
            font-size: 0.95rem;
            margin-bottom: 15px;
            text-align: center;
            line-height: 1.5;
            padding: 0 10px;
        }
        
        @media (min-width: 375px) {
            .lead {
                font-size: 1rem;
                padding: 0;
            }
        }
        
        @media (min-width: 576px) {
            .lead {
                text-align: left;
                margin-bottom: 20px;
            }
        }
        
        @media (min-width: 768px) {
            .lead {
                font-size: 1.2rem;
                margin-bottom: 30px;
                line-height: 1.6;
            }
        }
        
        /* ========== DEVELOPER CARD ========== */
        .developer-card {
            background: rgba(255, 255, 255, 0.05) !important;
            padding: 15px !important;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-top: 10px;
        }
        
        @media (min-width: 576px) {
            .developer-card {
                padding: 18px !important;
                font-size: 0.9rem;
            }
        }
        
        @media (min-width: 768px) {
            .developer-card {
                padding: 24px !important;
                font-size: 1rem;
            }
        }
        
        /* ========== FOOTER BOTTOM ========== */
        .footer-bottom {
            padding-top: 15px;
            font-size: 0.75rem;
        }
        
        @media (min-width: 576px) {
            .footer-bottom {
                padding-top: 20px;
                font-size: 0.85rem;
            }
        }
        
        @media (min-width: 768px) {
            .footer-bottom {
                padding-top: 30px;
                font-size: 1rem;
            }
        }
        
        /* ========== CTA TEXT ========== */
        .cta-text {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            text-align: center;
            border-left: 3px solid var(--accent-green);
        }
        
        .cta-text h3 {
            color: white;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        
        .cta-text p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        
        @media (min-width: 576px) {
            .cta-text {
                margin-top: 25px;
                padding: 18px;
            }
            
            .cta-text h3 {
                font-size: 1.2rem;
            }
            
            .cta-text p {
                font-size: 0.95rem;
            }
        }
        
        @media (min-width: 768px) {
            .cta-text {
                text-align: left;
                margin-top: 35px;
                padding: 25px;
            }
            
            .cta-text h3 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }
            
            .cta-text p {
                font-size: 1rem;
            }
        }
        
        /* ========== EMAIL TEXT ========== */
        .email-text {
            word-break: break-all;
            font-size: 0.75rem;
        }
        
        @media (min-width: 576px) {
            .email-text {
                font-size: 0.85rem;
                word-break: normal;
            }
        }
        
        @media (min-width: 768px) {
            .email-text {
                font-size: 1rem;
            }
        }
        
        /* ========== UTILITIES ========== */
        .row.g-3 {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }
        
        @media (min-width: 768px) {
            .row.g-3 {
                --bs-gutter-x: 1.5rem;
                --bs-gutter-y: 1.5rem;
            }
        }
        
        /* Smooth transitions */
        * {
            transition: all 0.3s ease;
        }
        
        /* Touch-friendly buttons */
        button, a.btn {
            cursor: pointer;
        }
        
        /* Performance optimization for mobile */
        @media (max-width: 767px) {
            .floating-element, .shape-1, .shape-2, .particle {
                will-change: transform;
                transform: translateZ(0);
            }
            
            /* Reduce animation intensity on mobile */
            .floating-element {
                animation-duration: 12s;
            }
            
            .shape-1, .shape-2 {
                animation-duration: 25s;
            }
        }
        
        /* Fix untuk margin berlebih di mobile */
        @media (max-width: 575px) {
            .mb-4 {
                margin-bottom: 1rem !important;
            }
            
            .mb-3 {
                margin-bottom: 0.75rem !important;
            }
            
            .mt-4 {
                margin-top: 1rem !important;
            }
            
            .pt-3 {
                padding-top: 0.75rem !important;
            }
        }
        
        /* ========== FIX TAMBAHAN UNTUK FOOTER DI HP ========== */
        @media (max-width: 767px) {
            body::after {
                content: '';
                display: block;
                height: 50px; /* Ruang ekstra di bawah footer */
            }
            
            .main-content {
                min-height: calc(100vh - 200px); /* Pastikan konten cukup tinggi */
            }
        }
    </style>
</head>
<body>
    <?php if ($logout_message): ?>
    <div class="logout-alert">
        <div class="alert alert-success alert-dismissible fade show shadow-lg" role="alert" style="border-radius: 12px; border-left: 4px solid var(--primary-green);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3" style="font-size: 1.2rem; color: var(--primary-green);"></i>
                <div style="flex: 1;">
                    <h6 class="mb-1 fw-bold" style="font-size: 0.9rem;">Logout Berhasil</h6>
                    <p class="mb-0" style="font-size: 0.85rem;"><?php echo $logout_message; ?></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="main-content">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container">
                <a class="navbar-brand logo" href="index.php">
                    <i class="fas fa-chart-line me-2"></i>MATHLine
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item">
                            <a href="teacher_login.php" class="btn btn-outline-green">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Login Guru
                            </a>
                        </li>
                        <li class="nav-item mt-2 mt-lg-0">
                            <a href="student_login.php" class="btn btn-gradient">
                                <i class="fas fa-user-graduate me-2"></i>Login Siswa
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            
            <div class="floating-elements">
                <div class="floating-element">y = mx + c</div>
                <div class="floating-element">m₁ × m₂ = -1</div>
                <div class="floating-element">Δy/Δx</div>
                <div class="floating-element">(x,y)</div>
            </div>
            
            <div class="particles" id="particles-js"></div>
            
            <div class="math-line-animation"></div>
            <div class="container position-relative">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-7 hero-content">
                        <h1 class="hero-title">
                            <span class="gradient-text">PERSAMAAN GARIS LURUS</span>
                        </h1>
                        <p class="lead" style="color: rgba(255,255,255,0.85);">
                            Platform pembelajaran matematika berbasis Problem Based Learning 
                            yang menggabungkan teori dengan praktik melalui simulasi interaktif 
                            dan visualisasi grafis yang menarik.
                        </p>
                        
                        
                    <div class="col-12 col-lg-5 hero-illustration">
                        <div class="text-center">
                            <div class="graph-container">
                                <!-- Animated Graph Container -->
                                <svg viewBox="0 0 400 300" preserveAspectRatio="xMidYMid meet">
                                    <defs>
                                        <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#3B82F6;stop-opacity:1" />
                                        </linearGradient>
                                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    
                                    <!-- Grid -->
                                    <rect width="100%" height="100%" fill="url(#grid)" rx="10" />
                                    
                                    <!-- Axes -->
                                    <line x1="50" y1="150" x2="350" y2="150" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                    <line x1="200" y1="50" x2="200" y2="250" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                    
                                    <!-- Animated Line Graph -->
                                    <path id="graphLine" d="M 50 200 L 200 150 L 350 100" stroke="url(#grad1)" stroke-width="3" fill="none" 
                                          stroke-linecap="round" stroke-linejoin="round">
                                        <animate attributeName="stroke-dasharray" from="0, 1000" to="1000, 0" dur="2s" fill="freeze"/>
                                    </path>
                                    
                                    <!-- Data Points -->
                                    <circle cx="50" cy="200" r="5" fill="white" stroke="#10B981" stroke-width="2">
                                        <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <circle cx="200" cy="150" r="5" fill="white" stroke="#3B82F6" stroke-width="2">
                                        <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite" begin="0.5s"/>
                                    </circle>
                                    <circle cx="350" cy="100" r="5" fill="white" stroke="#10B981" stroke-width="2">
                                        <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite" begin="1s"/>
                                    </circle>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer - SUDAH DIPERBAIKI UNTUK HP -->
    <footer class="footer-elegant">
        <div class="container">
            <div class="row g-3">
                <div class="col-12 col-lg-4 mb-3 mb-lg-0">
                    <div class="logo mb-2">MATHLine</div>
                    <p style="color: #CBD5E1; line-height: 1.5; font-size: 0.85rem; margin-bottom: 1rem;">
                        Platform pembelajaran interaktif untuk materi Persamaan Garis Lurus 
                        berbasis Problem Based Learning yang dirancang khusus untuk 
                        meningkatkan pemahaman konsep matematika secara interaktif.
                    </p>
                    <div class="social-icons">
                        <a href="https://facebook.com/SeptrianaManik" target="_blank" title="Facebook Septriana Manik">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="mailto:septrimanik.4222111005@mhs.unimed.ac.id" title="Email Septrimanik">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="https://instagram.com/septriman09_" target="_blank" title="Instagram Septriman09_">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-0">
                    <h4 class="mb-2" style="color: var(--accent-green); font-size: 0.95rem;">Kontak & Sosial Media</h4>
                    <div class="footer-contact">
                        <a href="https://facebook.com/SeptrianaManik" target="_blank">
                            <i class="fab fa-facebook-f me-2"></i>Septriana Manik
                        </a>
                        <a href="mailto:septrimanik.4222111005@mhs.unimed.ac.id">
                            <i class="fas fa-envelope me-2"></i>
                            <span class="email-text">septrimanik.4222111005@mhs.unimed.ac.id</span>
                        </a>
                        <a href="https://instagram.com/septriman09_" target="_blank">
                            <i class="fab fa-instagram me-2"></i>@septriman09_
                        </a>
                        <a href="#">
                            <i class="fas fa-map-marker-alt me-2"></i>Universitas Negeri Medan
                        </a>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-4">
                    <h4 class="mb-2" style="color: var(--accent-blue); font-size: 0.95rem;">Pengembang</h4>
                    <div class="developer-card">
                        <h5 class="text-white mb-1" style="font-size: 0.9rem;">Septriana Manik</h5>
                        <p style="color: #94A3B8;" class="mb-1 small">
                            Mahasiswa Pendidikan Matematika<br>
                            Universitas Negeri Medan
                        </p>
                        <p style="color: #CBD5E1;" class="small mb-0">
                            Platform ini dikembangkan sebagai media pembelajaran inovatif 
                            untuk membantu siswa memahami konsep matematika dengan lebih baik.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom text-center mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
                <p class="mb-0" style="color: #94A3B8; font-size: 0.75rem;">
                    &copy; 2026 MATHLine - Pendidikan Matematika | 
                    Universitas Negeri Medan
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 30) {
                navbar.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.12)';
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.padding = '10px 0';
            } else {
                navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.08)';
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.padding = '12px 0';
            }
        });
        
        // Animate floating elements (optimized for mobile)
        function animateFloatingElements() {
            const elements = document.querySelectorAll('.floating-element');
            const time = Date.now() / 1000;
            
            elements.forEach((element, index) => {
                // Different animation intensity based on screen size
                const intensity = window.innerWidth < 768 ? 0.3 : 1;
                const yOffset = Math.sin(time + index) * 10 * intensity;
                const rotation = Math.sin(time + index * 0.5) * 3 * intensity;
                const scale = 1 + Math.sin(time + index) * 0.03 * intensity;
                element.style.transform = `translateY(${yOffset}px) rotate(${rotation}deg) scale(${scale})`;
            });
            
            requestAnimationFrame(animateFloatingElements);
        }
        
        // Create particles (optimized for mobile)
        function createParticles() {
            const container = document.getElementById('particles-js');
            if (!container) return;
            
            // Clear existing particles
            container.innerHTML = '';
            
            // Less particles on mobile for better performance
            const particleCount = window.innerWidth < 768 ? 10 : 30;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random position
                particle.style.left = Math.random() * 100 + '%';
                
                // Faster animation on mobile
                const duration = window.innerWidth < 768 ? 
                    (8 + Math.random() * 12) : 
                    (10 + Math.random() * 20);
                const delay = Math.random() * 3;
                particle.style.animationDuration = duration + 's';
                particle.style.animationDelay = delay + 's';
                
                container.appendChild(particle);
            }
        }
        
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Start all animations
            animateFloatingElements();
            createParticles();
            
            // Re-create particles on resize (with debounce)
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(createParticles, 250);
            });
            
            // Auto-dismiss logout alert after 5 seconds
            const logoutAlert = document.querySelector('.logout-alert');
            if (logoutAlert) {
                setTimeout(() => {
                    logoutAlert.style.opacity = '0';
                    logoutAlert.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        logoutAlert.style.display = 'none';
                    }, 500);
                }, 5000);
            }
            
            // Mobile touch feedback untuk tombol di navbar
            const buttons = document.querySelectorAll('.navbar .btn-gradient, .navbar .btn-outline-green');
            buttons.forEach(btn => {
                btn.addEventListener('touchstart', function() {
                    this.style.transform = 'translateY(-1px)';
                });
                
                btn.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
            
            // Fix untuk email yang terpotong di mobile
            const emailLinks = document.querySelectorAll('a[href^="mailto:"]');
            emailLinks.forEach(link => {
                const email = link.getAttribute('href').replace('mailto:', '');
                const emailSpan = link.querySelector('.email-text');
                if (emailSpan) {
                    emailSpan.textContent = email;
                }
            });
            
            // Adjust layout for mobile
            function adjustMobileLayout() {
                if (window.innerWidth < 768) {
                    // Ensure footer is visible
                    const footer = document.querySelector('.footer-elegant');
                    if (footer) {
                        footer.style.display = 'block';
                        footer.style.visibility = 'visible';
                        footer.style.opacity = '1';
                        footer.style.position = 'relative';
                        footer.style.bottom = '0';
                    }
                    
                    // Adjust container padding
                    document.querySelectorAll('.container').forEach(container => {
                        container.style.paddingLeft = '12px';
                        container.style.paddingRight = '12px';
                    });
                } else {
                    // Reset for desktop
                    document.querySelectorAll('.container').forEach(container => {
                        container.style.paddingLeft = '';
                        container.style.paddingRight = '';
                    });
                }
            }
            
            // Initial adjustment
            adjustMobileLayout();
            
            // Adjust on resize
            window.addEventListener('resize', adjustMobileLayout);
            
            // Force footer visibility on load
            setTimeout(() => {
                const footer = document.querySelector('.footer-elegant');
                if (footer) {
                    footer.style.display = 'block';
                }
            }, 100);
        });
        
        // Add gradient animation to buttons on hover
        document.querySelectorAll('.btn-gradient').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.background = 'linear-gradient(135deg, #3B82F6 0%, #10B981 100%)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.background = 'linear-gradient(135deg, #10B981 0%, #3B82F6 100%)';
            });
        });
        
        // Fix untuk iOS Safari (menghindari rubber band effect)
        document.body.addEventListener('touchmove', function(e) {
            if (document.body.scrollHeight === window.innerHeight) {
                e.preventDefault();
            }
        }, { passive: false });
    </script>
</body>
</html>