<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if (isset($_POST['simpan'])) {

    $id_pelanggan = $_POST['id_pelanggan'];
    $id_ps        = $_POST['id_ps'];
    $jam_mulai    = $_POST['jam_mulai'];
    $jam_selesai  = $_POST['jam_selesai'];
    $total_jam = (
        strtotime($jam_selesai)
        - strtotime($jam_mulai)
    ) / 3600;

    mysqli_query($conn, "
        INSERT INTO booking(
            id_pelanggan,
            id_ps,
            jam_mulai,
            jam_selesai,
            total_jam,
            status_booking
        )
        VALUES(
            '$id_pelanggan',
            '$id_ps',
            '$jam_mulai',
            '$jam_selesai',
            '$total_jam',
            'booking'
        )
    ");

    mysqli_query($conn, "
        UPDATE ps_unit
        SET status_ps='dipakai'
        WHERE id_ps='$id_ps'
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
    $total_jam = (
        strtotime($jam_selesai)
        - strtotime($jam_mulai)
    ) / 3600;

    $ambil = mysqli_query($conn, "
        SELECT * FROM booking
        WHERE id_booking='$id_booking'
    ");

    $booking_lama = mysqli_fetch_assoc($ambil);

    $id_ps_lama = $booking_lama['id_ps'];

    mysqli_query($conn, "
        UPDATE ps_unit
        SET status_ps='tersedia'
        WHERE id_ps='$id_ps_lama'
    ");

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

    mysqli_query($conn, "
        UPDATE ps_unit
        SET status_ps='dipakai'
        WHERE id_ps='$id_ps_baru'
    ");

    header("Location: booking.php");
    exit;
}

if ($action == 'hapus') {

    $id = $_GET['id'];

    $ambil = mysqli_query($conn, "
        SELECT * FROM booking
        WHERE id_booking='$id'
    ");

    $data_booking = mysqli_fetch_assoc($ambil);

    $id_ps = $data_booking['id_ps'];

    mysqli_query($conn, "
        DELETE FROM booking
        WHERE id_booking='$id'
    ");

    mysqli_query($conn, "
        UPDATE ps_unit
        SET status_ps='tersedia'
        WHERE id_ps='$id_ps'
    ");

    header("Location: booking.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Booking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <h1>Data Booking</h1>
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
        $pelanggan = mysqli_query($conn, "
            SELECT * FROM pelanggan
        ");

        $ps = mysqli_query($conn, "
            SELECT * FROM ps_unit
            WHERE status_ps='tersedia'
        ");
    ?>

    <h2>Tambah Booking</h2>

    <form method="POST">
        <label>Pelanggan</label><br>
        <select name="id_pelanggan" required>
            <option value="">
                -- Pilih Pelanggan --
            </option>

            <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
            <option value="<?= $p['id_pelanggan']; ?>">
                <?= $p['nama_pelanggan']; ?>
            </option>
            <?php } ?>
        </select>

        <br><br>

        <label>PS Unit</label><br>
        <select name="id_ps" required>
            <option value="">
                -- Pilih PS --
            </option>

            <?php while ($psu = mysqli_fetch_assoc($ps)) { ?>
            <option value="<?= $psu['id_ps']; ?>">

                <?= $psu['nama_ps']; ?>
                -
                <?= $psu['tipe']; ?>
            </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Jam Mulai</label><br>
        <input type="datetime-local"
            name="jam_mulai"
            required>

        <br><br>

        <label>Jam Selesai</label><br>
        <input type="datetime-local"
            name="jam_selesai"
            required>

        <br><br>

        <button class="btn-danger" type="submit" name="simpan">
            Simpan Booking
        </button>

        <a href="booking.php">
            <button class="btn-danger" type="button">
                Batal
            </button>
        </a>
    </form>
    <?php

    } elseif ($action == 'edit') {

        $id = $_GET['id'];

        $booking = mysqli_query($conn, "
            SELECT * FROM booking
            WHERE id_booking='$id'
        ");

        $data_booking = mysqli_fetch_assoc($booking);

        $pelanggan = mysqli_query($conn, "
            SELECT * FROM pelanggan
        ");

        $ps = mysqli_query($conn, "
            SELECT * FROM ps_unit
        ");
    ?>

    <h2>Edit Booking</h2>
    <form method="POST">
        <input type="hidden"
            name="id_booking"
            value="<?= $data_booking['id_booking']; ?>">

        <label>Pelanggan</label><br>
        <select name="id_pelanggan" required>
            <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
            <option value="<?= $p['id_pelanggan']; ?>"
                <?= ($p['id_pelanggan'] == $data_booking['id_pelanggan'])
                    ? 'selected'
                    : ''; ?>>

                <?= $p['nama_pelanggan']; ?>
            </option>
            <?php } ?>
        </select>

        <br><br>

        <label>PS Unit</label><br>
        <select name="id_ps" required>
            <?php while ($psu = mysqli_fetch_assoc($ps)) { ?>
            <option value="<?= $psu['id_ps']; ?>"
                <?= ($psu['id_ps'] == $data_booking['id_ps'])
                    ? 'selected'
                    : ''; ?>>

                <?= $psu['nama_ps']; ?>
                -
                <?= $psu['tipe']; ?>
            </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Jam Mulai</label><br>
        <input type="datetime-local"
            name="jam_mulai"
            value="<?= date('Y-m-d\TH:i', strtotime($data_booking['jam_mulai'])); ?>"
            required>

        <br><br>

        <label>Jam Selesai</label><br>
        <input type="datetime-local"
            name="jam_selesai"
            value="<?= date('Y-m-d\TH:i', strtotime($data_booking['jam_selesai'])); ?>"
            required>

        <br><br>

        <button class="btn-danger" type="submit" name="update">
            Update Booking
        </button>

        <a href="booking.php">
            <button class="btn-danger" type="button">
                Batal
            </button>
        </a>
    </form>
    <?php

    } else {
        $query = mysqli_query($conn, "
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

            ORDER BY booking.id_booking DESC
        ");
    ?>

    <a href="booking.php?action=tambah">
        Tambah Booking
    </a>

    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Pelanggan</th>
            <th>PS</th>
            <th>Tipe</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Total Jam</th>
            <th>Status</th>
            <th>Aksi</th>
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
        <td><?= $data['jam_mulai']; ?></td>
        <td><?= $data['jam_selesai']; ?></td>
        <td><?= $data['total_jam']; ?> Jam</td>
        <td><?= $data['status_booking']; ?></td>
        <td>
            <a href="booking.php?action=edit&id=<?= $data['id_booking']; ?>">
                <button class="btn-danger" type="button">
                    Edit
                </button>           
            </a>

            |

            <a href="booking.php?action=hapus&id=<?= $data['id_booking']; ?>"
            onclick="return confirm('Yakin hapus booking?')">

                Hapus

            </a>
        </td>
    </tr>
    <?php } ?>
    </table>
    <?php } ?>
</body>
</html>