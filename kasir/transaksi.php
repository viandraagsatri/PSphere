<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'kasir' && $_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

include '../config/koneksi.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if (isset($_POST['simpan'])) {
    $id_booking   = $_POST['id_booking'];
    $metode_bayar = $_POST['metode_bayar'];

    $query = mysqli_query($conn, "
        SELECT booking.*, ps_unit.harga_per_jam, ps_unit.id_ps
        FROM booking
        JOIN ps_unit ON booking.id_ps = ps_unit.id_ps
        WHERE booking.id_booking='$id_booking'
    ");

    $data = mysqli_fetch_assoc($query);
    $harga_per_jam = $data['harga_per_jam'];
    $total_jam     = $data['total_jam'];
    $id_ps         = $data['id_ps'];
    
    $total_bayar = $harga_per_jam * $total_jam;

    $id_user = $_SESSION['id_user'];
    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "
            INSERT INTO transaksi(id_booking, id_user, tanggal_transaksi, total_bayar, metode_bayar, status_pembayaran)
            VALUES('$id_booking', '$id_user', NOW(), '$total_bayar', '$metode_bayar', 'lunas')
        ");

        mysqli_query($conn, "
            UPDATE booking SET status_booking='selesai' WHERE id_booking='$id_booking'
        ");

        mysqli_query($conn, "
            UPDATE ps_unit SET status_ps='tersedia' WHERE id_ps='$id_ps'
        ");

        mysqli_commit($conn);

        echo "<script>
            alert('Transaksi berhasil');
            window.location='transaksi.php';
        </script>";

    } catch (Exception $e) {

        mysqli_rollback($conn);

        echo "<script>
            alert('Transaksi gagal');
            window.location='transaksi.php?action=tambah';
        </script>";
    }

    header("Location: transaksi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi - PSphere</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-gamepad"></i> RENTAL PS
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="booking.php"><i class="fa-solid fa-calendar-check"></i> Kelola Booking</a></li>
            <li><a href="transaksi.php" class="active"><i class="fa-solid fa-cash-register"></i> Kelola Transaksi</a></li>
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
                <?php if(isset($_GET['action'])) { ?>
                    <input type="hidden" name="action" value="<?= $_GET['action']; ?>">
                <?php } ?>
            </form>

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
            
            <?php if ($action == 'tambah') { 
                $booking = mysqli_query($conn, "
                    SELECT booking.*, pelanggan.nama_pelanggan, ps_unit.nama_ps, ps_unit.tipe
                    FROM booking
                    JOIN pelanggan ON booking.id_pelanggan = pelanggan.id_pelanggan
                    JOIN ps_unit ON booking.id_ps = ps_unit.id_ps
                    WHERE booking.status_booking='booking'
                ");
            ?>
            <div class="recent-activity">
                <h2 class="page-title">Proses Pembayaran (Transaksi)</h2>
                <form method="POST" class="form-container">
                    <div class="input-group">
                        <label>Pilih Data Booking</label>
                        <select name="id_booking" required>
                            <option value="">-- Pilih Booking yang Belum Dibayar --</option>
                            <?php while ($b = mysqli_fetch_assoc($booking)) { ?>
                                <option value="<?= $b['id_booking']; ?>">
                                    <?= $b['nama_pelanggan']; ?> | <?= $b['nama_ps']; ?> (<?= $b['tipe']; ?>) | Durasi: <?= $b['total_jam']; ?> Jam
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode_bayar" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="cash">Tunai (Cash)</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="simpan"><i class="fa-solid fa-check-circle"></i> Selesaikan Pembayaran</button>
                        <a href="transaksi.php" class="btn-secondary"><i class="fa-solid fa-xmark"></i> Batal</a>
                    </div>
                </form>
            </div>

            <?php } else { 
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
                        ORDER BY transaksi.id_transaksi ASC
                    ");
                } else {
                    $query = mysqli_query($conn, "
                        SELECT transaksi.*, pelanggan.nama_pelanggan, ps_unit.nama_ps, ps_unit.tipe
                        FROM transaksi
                        JOIN booking ON transaksi.id_booking = booking.id_booking
                        JOIN pelanggan ON booking.id_pelanggan = pelanggan.id_pelanggan
                        JOIN ps_unit ON booking.id_ps = ps_unit.id_ps
                        ORDER BY transaksi.id_transaksi ASC
                    ");
                }
            ?>
            <div class="header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="page-title" style="margin: 0;">Riwayat Transaksi</h2>
                <a href="transaksi.php?action=tambah" class="btn-primary" style="width: auto;"><i class="fa-solid fa-plus"></i> Proses Pembayaran Baru</a>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Pelanggan</th>
                            <th>Mesin PS</th>
                            <th>Tanggal Transaksi</th>
                            <th>Metode Bayar</th>
                            <th style="text-align: right;">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td style="font-weight: bold; color: #1e293b;"><?= $data['nama_pelanggan']; ?></td>
                            <td><?= $data['nama_ps']; ?> <span style="font-size: 11px; color: #64748b;">(<?= $data['tipe']; ?>)</span></td>
                            <td><?= date('d M Y, H:i', strtotime($data['tanggal_transaksi'])); ?></td>
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
                                Rp <?= number_format($data['total_bayar'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(mysqli_num_rows($query) == 0) { echo "<tr><td colspan='6' style='text-align:center;'>Data transaksi tidak ditemukan</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>

        </div>
    </div>
</body>
</html>