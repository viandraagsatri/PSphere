<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'kasir') {
    echo "Akses ditolak!";
    exit;
}

$q_booking = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status_booking = 'booking'");
$d_booking = mysqli_fetch_assoc($q_booking);

$q_ps = mysqli_query($conn, "SELECT COUNT(*) as total FROM ps_unit WHERE status_ps = 'tersedia'");
$d_ps = mysqli_fetch_assoc($q_ps);

$q_transaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
$d_transaksi = mysqli_fetch_assoc($q_transaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - PSphere</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-gamepad"></i> RENTAL PS
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="booking.php"><i class="fa-solid fa-calendar-check"></i> Kelola Booking</a></li>
            <li><a href="transaksi.php"><i class="fa-solid fa-cash-register"></i> Kelola Transaksi</a></li>
        </ul>
        <div class="sidebar-bottom">
            <a href="../auth/logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div></div>
            
            <div class="user-profile">
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nama']; ?></span>
                    <span class="user-role">Kasir</span>
                </div>
                <div class="user-avatar">
                    <?= substr($_SESSION['nama'], 0, 1); ?>
                </div>
            </div>
        </div>

        <div class="content-area">
            <h2 class="page-title">Ringkasan Hari Ini</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="stat-details">
                        <p>Booking Aktif</p>
                        <h3><?= $d_booking['total']; ?></h3>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-purple">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <p>PS Tersedia</p>
                        <h3><?= $d_ps['total']; ?></h3>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div class="stat-details">
                        <p>Total Transaksi</p>
                        <h3><?= $d_transaksi['total']; ?></h3>
                    </div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Aktivitas Kasir</h3>
                <p>Gunakan menu di samping untuk mengelola pesanan (booking) dan memproses pembayaran (transaksi) dari pelanggan.</p>
            </div>
        </div>
    </div>

</body>
</html>