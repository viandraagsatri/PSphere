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
    SELECT *
    FROM v_laporan_aktivitas
    ORDER BY tanggal ASC
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
        <div class="sidebar-brand"><i class="fa-solid fa-gamepad"></i> RENTAL PS</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="pelanggan.php"><i class="fa-solid fa-users"></i> Data Pelanggan</a></li>
            <li><a href="ps_unit.php"><i class="fa-solid fa-tv"></i> Kelola Mesin PS</a></li>
            <li><a href="laporan.php" class="active"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Aktivitas</a></li>
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
                <div class="user-avatar"><?= substr($_SESSION['nama'], 0, 1); ?></div>
            </div>
        </div>

        <div class="content-area">
            <div class="header-action" style="margin-bottom:20px;">
                <h2 class="page-title">Laporan Aktivitas</h2>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>PS</th>
                            <th>Tanggal</th>
                            <th>Aktivitas</th>
                            <th>Status</th>
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
                            <td><?= $data['nama_ps']; ?></td>
                            <td><?= date('d-m-Y, H:i', strtotime($data['tanggal'])); ?></td>
                            <td><?= $data['aktivitas']; ?></td>
                            <td><?= ucwords(str_replace('_', ' ', $data['status'])); ?></td>
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