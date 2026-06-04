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

$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search != '') {
    $query = mysqli_query($conn, "
        SELECT transaksi.*, pelanggan.nama_pelanggan, ps_unit.nama_ps, ps_unit.tipe
        FROM transaksi
        JOIN booking ON transaksi.id_booking = booking.id_booking
        JOIN pelanggan ON booking.id_pelanggan = pelanggan.id_pelanggan
        JOIN ps_unit ON booking.id_ps = ps_unit.id_ps
        WHERE pelanggan.nama_pelanggan LIKE '%$search%' 
           OR ps_unit.nama_ps LIKE '%$search%' 
           OR transaksi.metode_bayar LIKE '%$search%'
        ORDER BY transaksi.tanggal_transaksi ASC
    ");
} else {
    $query = mysqli_query($conn, "
        SELECT transaksi.*, pelanggan.nama_pelanggan, ps_unit.nama_ps, ps_unit.tipe
        FROM transaksi
        JOIN booking ON transaksi.id_booking = booking.id_booking
        JOIN pelanggan ON booking.id_pelanggan = pelanggan.id_pelanggan
        JOIN ps_unit ON booking.id_ps = ps_unit.id_ps
        ORDER BY transaksi.tanggal_transaksi ASC
    ");
}
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
        <div class="sidebar-brand"><i class="fa-solid fa-gamepad"></i> RENTAL PS</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="pelanggan.php"><i class="fa-solid fa-users"></i> Data Pelanggan</a></li>
            <li><a href="ps_unit.php"><i class="fa-solid fa-tv"></i> Kelola Mesin PS</a></li>
            <li><a href="laporantransaksi.php" class="active"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Transaksi</a></li>
            <li><a href="pengguna.php"><i class="fa-solid fa-user-shield"></i> Data Pengguna</a></li>
        </ul>
        <div class="sidebar-bottom">
            <a href="../auth/logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <form action="" method="GET" class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Cari nama atau metode..." value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            </form>

            <div class="user-profile">
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nama']; ?></span>
                    <span class="user-role">Administrator</span>
                </div>
                <div class="user-avatar"><?= substr($_SESSION['nama'], 0, 1); ?></div>
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
                            <th width="5%">No</th>
                            <th>Pelanggan</th>
                            <th>PS</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th style="text-align: right;">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    while($data = mysqli_fetch_assoc($query)){
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td style="font-weight: bold; color: #1e293b;"><?= $data['nama_pelanggan']; ?></td>
                            <td><?= $data['nama_ps']; ?> <span style="font-size: 11px; color: #64748b;">(<?= $data['tipe']; ?>)</span></td>
                            <td><?= date('d-m-Y, H:i', strtotime($data['tanggal_transaksi'])); ?></td>
                            <td>
                                <?php
                                    if ($data['metode_bayar'] == 'cash') {
                                        echo '<span style="background-color: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;">CASH</span>';
                                    } elseif ($data['metode_bayar'] == 'qris') {
                                        echo '<span style="background-color: #f3e8ff; color: #6b21a8; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;">QRIS</span>';
                                    } else {
                                        echo '<span style="background-color: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;">TRANSFER</span>';
                                    }
                                ?>
                            </td>
                            <td style="text-align: right; font-weight: bold; font-size: 15px; color: #0f172a;">
                                Rp <?= number_format($data['total_bayar'],0,',','.'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(mysqli_num_rows($query) == 0) { echo "<tr><td colspan='6' style='text-align:center;'>Data tidak ditemukan</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>