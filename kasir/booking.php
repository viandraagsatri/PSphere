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
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_ps        = $_POST['id_ps'];
    $jam_mulai    = $_POST['jam_mulai'];
    $jam_selesai  = $_POST['jam_selesai'];
    
    $total_jam = (strtotime($jam_selesai) - strtotime($jam_mulai)) / 3600;

    mysqli_query($conn, "
        INSERT INTO booking(id_pelanggan, id_ps, jam_mulai, jam_selesai, total_jam, status_booking)
        VALUES('$id_pelanggan', '$id_ps', '$jam_mulai', '$jam_selesai', '$total_jam', 'booking')
    ");

    mysqli_query($conn, "
        UPDATE ps_unit SET status_ps='dipakai' WHERE id_ps='$id_ps'
    ");

    header("Location: booking.php");
    exit;
}

if (isset($_POST['update'])) {
    $id_booking   = $_POST['id_booking'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_ps_baru   = $_POST['id_ps'];
    $jam_mulai    = $_POST['jam_mulai'];
    $jam_selesai  = $_POST['jam_selesai'];
    
    $total_jam = (strtotime($jam_selesai) - strtotime($jam_mulai)) / 3600;

    $ambil = mysqli_query($conn, "SELECT * FROM booking WHERE id_booking='$id_booking'");
    $booking_lama = mysqli_fetch_assoc($ambil);
    $id_ps_lama = $booking_lama['id_ps'];

    mysqli_query($conn, "UPDATE ps_unit SET status_ps='tersedia' WHERE id_ps='$id_ps_lama'");

    mysqli_query($conn, "
        UPDATE booking
        SET
            id_pelanggan='$id_pelanggan',
            id_ps='$id_ps_baru',
            jam_mulai='$jam_mulai',
            jam_selesai='$jam_selesai',
            total_jam='$total_jam'
        WHERE id_booking='$id_booking'
    ");

    mysqli_query($conn, "UPDATE ps_unit SET status_ps='dipakai' WHERE id_ps='$id_ps_baru'");

    header("Location: booking.php");
    exit;
}

if ($action == 'hapus') {
    $id = $_GET['id'];
    $ambil = mysqli_query($conn, "SELECT * FROM booking WHERE id_booking='$id'");
    $data_booking = mysqli_fetch_assoc($ambil);
    $id_ps = $data_booking['id_ps'];

    mysqli_query($conn, "DELETE FROM booking WHERE id_booking='$id'");
    mysqli_query($conn, "UPDATE ps_unit SET status_ps='tersedia' WHERE id_ps='$id_ps'");

    header("Location: booking.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Booking - PSphere</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-gamepad"></i> RENTAL PSphere
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="booking.php" class="active"><i class="fa-solid fa-calendar-check"></i> Kelola Booking</a></li>
            <li><a href="transaksi.php"><i class="fa-solid fa-cash-register"></i> Kelola Transaksi</a></li>
        </ul>
        <div class="sidebar-bottom">
            <a href="../auth/logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <form action="" method="GET" class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Cari pelanggan atau PS..." value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
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
                $pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan");
                $ps = mysqli_query($conn, "SELECT * FROM ps_unit WHERE status_ps='tersedia'");
            ?>
            <div class="recent-activity">
                <h2 class="page-title">Tambah Booking Baru</h2>
                <form method="POST" class="form-container">
                    <div class="input-group">
                        <label>Pelanggan</label>
                        <select name="id_pelanggan" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
                                <option value="<?= $p['id_pelanggan']; ?>"><?= $p['nama_pelanggan']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Pilih Mesin PS (Tersedia)</label>
                        <select name="id_ps" required>
                            <option value="">-- Pilih PS --</option>
                            <?php while ($psu = mysqli_fetch_assoc($ps)) { ?>
                                <option value="<?= $psu['id_ps']; ?>">
                                    <?= $psu['nama_ps']; ?> - <?= $psu['tipe']; ?> (Rp <?= number_format($psu['harga_per_jam'],0,',','.'); ?>/jam)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Jam Mulai</label>
                        <input type="datetime-local" name="jam_mulai" required>
                    </div>

                    <div class="input-group">
                        <label>Jam Selesai</label>
                        <input type="datetime-local" name="jam_selesai" required>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="simpan"><i class="fa-solid fa-save"></i> Simpan Booking</button>
                        <a href="booking.php" class="btn-secondary"><i class="fa-solid fa-xmark"></i> Batal</a>
                    </div>
                </form>
            </div>

            <?php } elseif ($action == 'edit') { 
                $id = $_GET['id'];
                $booking = mysqli_query($conn, "SELECT * FROM booking WHERE id_booking='$id'");
                $data_booking = mysqli_fetch_assoc($booking);

                $pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan");
                $ps = mysqli_query($conn, "SELECT * FROM ps_unit");
            ?>
            <div class="recent-activity">
                <h2 class="page-title">Edit Booking</h2>
                <form method="POST" class="form-container">
                    <input type="hidden" name="id_booking" value="<?= $data_booking['id_booking']; ?>">

                    <div class="input-group">
                        <label>Pelanggan</label>
                        <select name="id_pelanggan" required>
                            <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
                                <option value="<?= $p['id_pelanggan']; ?>" <?= ($p['id_pelanggan'] == $data_booking['id_pelanggan']) ? 'selected' : ''; ?>>
                                    <?= $p['nama_pelanggan']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Pilih Mesin PS</label>
                        <select name="id_ps" required>
                            <?php while ($psu = mysqli_fetch_assoc($ps)) { ?>
                                <option value="<?= $psu['id_ps']; ?>" <?= ($psu['id_ps'] == $data_booking['id_ps']) ? 'selected' : ''; ?>>
                                    <?= $psu['nama_ps']; ?> - <?= $psu['tipe']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Jam Mulai</label>
                        <input type="datetime-local" name="jam_mulai" value="<?= date('Y-m-d\TH:i', strtotime($data_booking['jam_mulai'])); ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Jam Selesai</label>
                        <input type="datetime-local" name="jam_selesai" value="<?= date('Y-m-d\TH:i', strtotime($data_booking['jam_selesai'])); ?>" required>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="update"><i class="fa-solid fa-save"></i> Update Booking</button>
                        <a href="booking.php" class="btn-secondary"><i class="fa-solid fa-xmark"></i> Batal</a>
                    </div>
                </form>
            </div>

            <?php } else { 
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if ($search != '') {
                    $query = mysqli_query($conn, "
                        SELECT booking.*, pelanggan.nama_pelanggan, ps_unit.nama_ps, ps_unit.tipe 
                        FROM booking 
                        JOIN pelanggan ON booking.id_pelanggan = pelanggan.id_pelanggan 
                        JOIN ps_unit ON booking.id_ps = ps_unit.id_ps 
                        WHERE pelanggan.nama_pelanggan LIKE '%$search%' 
                           OR ps_unit.nama_ps LIKE '%$search%' 
                           OR booking.status_booking LIKE '%$search%'
                        ORDER BY booking.id_booking ASC
                    ");
                } else {
                    $query = mysqli_query($conn, "
                        SELECT booking.*, pelanggan.nama_pelanggan, ps_unit.nama_ps, ps_unit.tipe 
                        FROM booking 
                        JOIN pelanggan ON booking.id_pelanggan = pelanggan.id_pelanggan 
                        JOIN ps_unit ON booking.id_ps = ps_unit.id_ps 
                        ORDER BY booking.id_booking ASC
                    ");
                }
            ?>
            <div class="header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="page-title" style="margin: 0;">Kelola Booking</h2>
                <a href="booking.php?action=tambah" class="btn-primary" style="width: auto;"><i class="fa-solid fa-plus"></i> Buat Booking Baru</a>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Pelanggan</th>
                            <th>Mesin PS</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th width="12%" style="text-align: center;">Aksi</th>
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
                            <td><?= date('d M Y, H:i', strtotime($data['jam_mulai'])); ?></td>
                            <td><?= date('d M Y, H:i', strtotime($data['jam_selesai'])); ?></td>
                            <td><strong><?= $data['total_jam']; ?> Jam</strong></td>
                            <td>
                                <?php
                                    if ($data['status_booking'] == 'booking') {
                                        echo '<span style="background-color: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Booking Aktif</span>';
                                    } elseif ($data['status_booking'] == 'selesai') {
                                        echo '<span style="background-color: #bbf7d0; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Selesai</span>';
                                    } else {
                                        echo '<span style="background-color: #fecaca; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Batal</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="booking.php?action=edit&id=<?= $data['id_booking']; ?>" class="action-btn edit-btn" title="Edit Booking"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="booking.php?action=hapus&id=<?= $data['id_booking']; ?>" class="action-btn delete-btn" onclick="return confirm('Yakin ingin membatalkan dan menghapus data booking ini?')" title="Hapus Booking"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(mysqli_num_rows($query) == 0) { echo "<tr><td colspan='8' style='text-align:center;'>Data booking tidak ditemukan</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>

        </div>
    </div>
</body>
</html>