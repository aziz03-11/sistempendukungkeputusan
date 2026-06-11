<?php
// Pastikan session dimulai untuk keamanan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Validasi login
if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
    header("Location: login.php?id=out");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pengambilan Keputusan SMART</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden; /* Mencegah layar terpotong / scroll ke samping */
        }
        
        /* Navbar Atas */
        .navbar-top {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: 70px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            z-index: 1030;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 70px; /* Mulai tepat di bawah navbar */
            bottom: 0;
            left: 0;
            width: 250px;
            background-color: #212529;
            color: #fff;
            z-index: 1020;
            overflow-y: auto;
            transition: all 0.3s ease;
            padding-top: 1rem;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.75);
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            border-radius: 0 50px 50px 0;
            margin-right: 15px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        /* Konten Utama */
        .main-content {
            margin-top: 70px; /* Jarak sebesar tinggi navbar atas */
            margin-left: 250px; /* Jarak sebesar lebar sidebar */
            padding: 2rem;
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease;
        }

        /* Tampilan Mobile & Layar Kecil */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px; /* Sembunyikan sidebar ke luar layar */
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                margin-left: 0; /* Konten memenuhi layar */
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-top px-4 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-primary border-0 d-md-none me-3" type="button" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand text-primary fw-bold m-0" href="index.php">
            <i class="fas fa-chart-line me-2"></i>SPK SMART
        </a>
    </div>
    
    <div class="dropdown">
        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
            <img src="https://ui-avatars.com/api/?name=Admin&background=0d6efd&color=fff" class="rounded-circle me-2" width="32" height="32" alt="User">
            <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin' ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
            <li><a class="dropdown-item text-danger fw-semibold" href="login.php?id=logout"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
        </ul>
    </div>
</nav>

<nav id="sidebarMenu" class="sidebar shadow">
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fas fa-home"></i> Beranda
            </a>
        </li>
        <li class="nav-item mt-3">
            <span class="text-muted ms-4 fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">DATA MASTER</span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="alternatif.php">
                <i class="fas fa-user-graduate"></i> Data Siswa
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="kriteria.php">
                <i class="fas fa-list-check"></i> Data Kriteria
            </a>
        </li>
        <li class="nav-item mt-3">
            <span class="text-muted ms-4 fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">PROSES SPK</span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="nilai.php">
                <i class="fas fa-pen-to-square"></i> Input Penilaian
            </a>
        </li>
        <li class="nav-item mt-3 pt-3 border-top border-secondary">
            <a class="nav-link text-warning fw-bold" href="spk.php">
                <i class="fas fa-crown"></i> Hasil Keputusan
            </a>
        </li>
    </ul>
</nav>

<main class="main-content">