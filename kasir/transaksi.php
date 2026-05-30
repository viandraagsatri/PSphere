<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if (isset($_POST['simpan'])) {

    $id_booking       = $_POST['id_booking'];
    $metode_bayar     = $_POST['metode_bayar'];

    $query = mysqli_query($conn, "
        SELECT
            booking.*,
            ps_unit.harga_per_jam,
            ps_unit.id_ps
        FROM booking

        JOIN ps_unit
            ON booking.id_ps = ps_unit.id_ps

        WHERE booking.id_booking='$id_booking'
    ");

    $data = mysqli_fetch_assoc($query);
    $harga_per_jam = $data['harga_per_jam'];
    $total_jam = $data['total_jam'];
    $id_ps = $data['id_ps'];
    $total_bayar = $harga_per_jam * $total_jam;

    mysqli_query($conn, "
        INSERT INTO transaksi(
            id_booking,
            tanggal_transaksi,
            total_bayar,
            metode_bayar
        )
        VALUES(
            '$id_booking',
            NOW(),
            '$total_bayar',
            '$metode_bayar'
        )
    ");

    mysqli_query($conn, "
        UPDATE booking
        SET status_booking='selesai'
        WHERE id_booking='$id_booking'
    ");

    mysqli_query($conn, "
        UPDATE ps_unit
        SET status_ps='tersedia'
        WHERE id_ps='$id_ps'
    ");

    header("Location: transaksi.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Data Transaksi</h1>

    <p>
        Selamat datang,
        <?= $_SESSION['nama']; ?>
    </p>

    <a href="dashboard.php">Kembali Dashboard</a>
    |
    <a href="../auth/logout.php">Logout</a>
    <hr>
    <?php

    if ($action == 'tambah') {
        $booking = mysqli_query($conn, "
            SELECT
                booking.*,
                pelanggan.nama_pelanggan,
                ps_unit.nama_ps,
                ps_unit.tipe

            FROM booking

            JOIN pelanggan
                ON booking.id_pelanggan = pelanggan.id_pelanggan

            JOIN ps_unit
                ON booking.id_ps = ps_unit.id_ps

            WHERE booking.status_booking='booking'
        ");
    ?>

    <h2>Tambah Transaksi</h2>
    <form method="POST">
        <label>Booking</label><br>
        <select name="id_booking" required>
            <option value="">
                -- Pilih Booking --
            </option>
            <?php while ($b = mysqli_fetch_assoc($booking)) { ?>
            <option value="<?= $b['id_booking']; ?>">
                <?= $b['nama_pelanggan']; ?>
                -
                <?= $b['nama_ps']; ?>
                -
                <?= $b['tipe']; ?>
                -
                <?= $b['total_jam']; ?> Jam
            </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Metode Bayar</label><br>
        <select name="metode_bayar" required>
            <option value="">
                -- Pilih Metode --
            </option>
            <option value="cash">
                Cash
            </option>
            <option value="qris">
                QRIS
            </option>
            <option value="transfer">
                Transfer
            </option>
        </select>

        <br><br>

        <button class="btn-danger" type="submit" name="simpan">
            Simpan Transaksi
        </button>
        <a href="transaksi.php">
            <button class="btn-danger" type="button">
                Batal
            </button>
        </a>
    </form>

    <?php

    } else {
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

    <a href="transaksi.php?action=tambah">
        Tambah Transaksi
    </a>

    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Pelanggan</th>
            <th>PS</th>
            <th>Tipe</th>
            <th>Tanggal</th>
            <th>Total Bayar</th>
            <th>Metode</th>
        </tr>
    <?php

    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
    ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= $data['nama_pelanggan']; ?></td>
        <td><?= $data['nama_ps']; ?></td>
        <td><?= $data['tipe']; ?></td>
        <td><?= $data['tanggal_transaksi']; ?></td>
        <td>
            Rp <?= number_format($data['total_bayar']); ?>
        </td>
        <td><?= $data['metode_bayar']; ?></td>
    </tr>
    <?php } ?>
    </table>
    <?php } ?>
</body>
</html>