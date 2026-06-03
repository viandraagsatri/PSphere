<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}
if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

$q_pelanggan = mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggan");
$d_pelanggan = mysqli_fetch_assoc($q_pelanggan);

$q_ps = mysqli_query($conn, "SELECT COUNT(*) as total FROM ps_unit");
$d_ps = mysqli_fetch_assoc($q_ps);

$q_pendapatan = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transaksi");
$d_pendapatan = mysqli_fetch_assoc($q_pendapatan);
$total_pendapatan = $d_pendapatan['total'] ? $d_pendapatan['total'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PSphere</title>
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
            <li><a href="pelanggan.php"><i class="fa-solid fa-users"></i> Data Pelanggan</a></li>
            <li><a href="ps_unit.php"><i class="fa-solid fa-tv"></i> Kelola Mesin PS</a></li>
            <li><a href="laporantransaksi.php"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Transaksi</a></li>
            <li><a href="pengguna.php"><i class="fa-solid fa-user-shield"></i> Data Pengguna</a></li>
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
                    <span class="user-role">Administrator</span>
                </div>
                <div class="user-avatar">
                    <?= substr($_SESSION['nama'], 0, 1); ?>
                </div>
            </div>
        </div>

        <div class="content-area">
            <h2 class="page-title">Ringkasan Sistem</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <p>Total Pelanggan</p>
                        <h3><?= $d_pelanggan['total']; ?></h3>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-purple">
                        <i class="fa-solid fa-tv"></i>
                    </div>
                    <div class="stat-details">
                        <p>Total Mesin PS</p>
                        <h3><?= $d_ps['total']; ?></h3>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div class="stat-details">
                        <p>Total Pendapatan</p>
                        <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                    </div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Aktivitas Terakhir</h3>
                <p>Area ini bisa digunakan untuk menampilkan tabel transaksi terbaru atau laporan log admin di masa depan.</p>
            </div>
        </div>
    </div>

</body>
</html>