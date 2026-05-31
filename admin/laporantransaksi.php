<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

include '../config/koneksi.php';

$query = mysqli_query($conn, "
    SELECT
        transaksi.*,
        pelanggan.nama_pelanggan,
        ps_unit.nama_ps,
        ps_unit.tipe
    FROM transaksi
    JOIN booking
        ON transaksi.id_booking = booking.id_booking
    JOIN pelanggan
        ON booking.id_pelanggan = pelanggan.id_pelanggan
    JOIN ps_unit
        ON booking.id_ps = ps_unit.id_ps
    ORDER BY transaksi.id_transaksi DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - PSphere</title>
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
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Pencarian cepat...">
            </div>
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
            <div class="header-action" style="margin-bottom:20px;">
                <h2 class="page-title">Laporan Transaksi</h2>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>PS</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    while($data = mysqli_fetch_assoc($query)){
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $data['nama_pelanggan']; ?></td>
                            <td><?= $data['nama_ps']; ?> (<?= $data['tipe']; ?>)</td>
                            <td><?= date('d-m-Y H:i', strtotime($data['tanggal_transaksi'])); ?></td>
                            <td><?= strtoupper($data['metode_bayar']); ?></td>
                            <td>
                                Rp <?= number_format($data['total_bayar'],0,',','.'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>