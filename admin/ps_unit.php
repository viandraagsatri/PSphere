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

$action = isset($_GET['action']) ? $_GET['action'] : '';

if (isset($_POST['simpan'])) {

    $nama_ps       = $_POST['nama_ps'];
    $tipe          = $_POST['tipe'];
    $harga_per_jam = $_POST['harga_per_jam'];
    $status_ps     = $_POST['status_ps'];

    mysqli_query($conn, "
        INSERT INTO ps_unit(
            nama_ps,
            tipe,
            harga_per_jam,
            status_ps
        )
        VALUES(
            '$nama_ps',
            '$tipe',
            '$harga_per_jam',
            '$status_ps'
        )
    ");

    header("Location: ps_unit.php");
    exit;
}

if (isset($_POST['update'])) {

    $id             = $_POST['id'];
    $nama_ps        = $_POST['nama_ps'];
    $tipe           = $_POST['tipe'];
    $harga_per_jam  = $_POST['harga_per_jam'];
    $status_ps      = $_POST['status_ps'];

    mysqli_query($conn, "
        UPDATE ps_unit
        SET
            nama_ps='$nama_ps',
            tipe='$tipe',
            harga_per_jam='$harga_per_jam',
            status_ps='$status_ps'
        WHERE id_ps='$id'
    ");

    header("Location: ps_unit.php");
    exit;
}

if ($action == 'hapus') {

    $id = $_GET['id'];

    mysqli_query($conn, "
        DELETE FROM ps_unit
        WHERE id_ps='$id'
    ");

    header("Location: ps_unit.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data PS Unit</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Data PS Unit</h1>
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
    ?>

    <h2>Tambah PS Unit</h2>
    <form method="POST">
        <label>Nama PS</label><br>
        <input type="text"
            name="nama_ps"
            required>

        <br><br>

        <label>Tipe PS</label><br>
        <select name="tipe" required>
            <option value="">-- Pilih Tipe --</option>
            <option value="PS3">PS3</option>
            <option value="PS4">PS4</option>
            <option value="PS5">PS5</option>
        </select>

        <br><br>

        <label>Harga Per Jam</label><br>
        <input type="number"
            name="harga_per_jam"
            required>

        <br><br>

        <label>Status PS</label><br>
        <select name="status_ps" required>

            <option value="">-- Pilih Status --</option>
            <option value="tersedia">Tersedia</option>
            <option value="dipakai">Dipakai</option>
            <option value="pemeliharaan">Pemeliharaan</option>

        </select>

        <br><br>

        <button class="btn-danger" type="submit" name="simpan">
            Simpan
        </button>

        <a href="ps_unit.php">
            <button class="btn-danger" type="button">
                Batal
            </button>
        </a>
    </form>

    <?php
    } elseif ($action == 'edit') {

        $id = $_GET['id'];

        $query = mysqli_query($conn, "
            SELECT * FROM ps_unit
            WHERE id_ps='$id'
        ");

        $data = mysqli_fetch_assoc($query);
    ?>

    <h2>Edit PS Unit</h2>
    <form method="POST">
        <input type="hidden"
            name="id"
            value="<?= $data['id_ps']; ?>">

        <label>Nama PS</label><br>
        <input type="text"
            name="nama_ps"
            value="<?= $data['nama_ps']; ?>"
            required>

        <br><br>

        <label>Tipe PS</label><br>
        <select name="tipe" required>
            <option value="PS3"
                <?= ($data['tipe'] == 'PS3') ? 'selected' : ''; ?>>
                PS3
            </option>
            <option value="PS4"
                <?= ($data['tipe'] == 'PS4') ? 'selected' : ''; ?>>
                PS4
            </option>
            <option value="PS5"
                <?= ($data['tipe'] == 'PS5') ? 'selected' : ''; ?>>
                PS5
            </option>
        </select>

        <br><br>

        <label>Harga Per Jam</label><br>
        <input type="number"
            name="harga_per_jam"
            value="<?= $data['harga_per_jam']; ?>"
            required>

        <br><br>

        <label>Status PS</label><br>
        <select name="status_ps" required>
            <option value="tersedia"
                <?= ($data['status_ps'] == 'tersedia') ? 'selected' : ''; ?>>
                Tersedia
            </option>
            <option value="dipakai"
                <?= ($data['status_ps'] == 'dipakai') ? 'selected' : ''; ?>>
                Dipakai
            </option>
            <option value="pemeliharaan"
                <?= ($data['status_ps'] == 'pemeliharaan') ? 'selected' : ''; ?>>
                Pemeliharaan
            </option>
        </select>

        <br><br>

        <button class="btn-danger" type="submit" name="update">
            Update
        </button>

        <a href="ps_unit.php">
            <button class="btn-danger" type="button">
                Batal
            </button>
        </a>
    </form>

    <?php
    } else {

        $query = mysqli_query($conn, "
            SELECT * FROM ps_unit
        ");

    ?>

    <a href="ps_unit.php?action=tambah">
        Tambah PS Unit
    </a>

    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Nama PS</th>
            <th>Tipe</th>
            <th>Harga Per Jam</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    <?php

    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {

    ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= $data['nama_ps']; ?></td>
        <td><?= $data['tipe']; ?></td>
        <td>
            Rp <?= number_format($data['harga_per_jam']); ?>
        </td>
        <td><?= $data['status_ps']; ?></td>
        <td>
            <a href="ps_unit.php?action=edit&id=<?= $data['id_ps']; ?>">
                Edit
            </a>

            |

            <a href="ps_unit.php?action=hapus&id=<?= $data['id_ps']; ?>"
            onclick="return confirm('Yakin hapus data?')">
                Hapus
            </a>
        </td>
    </tr>

    <?php } ?>
    </table>
    <?php } ?>
</body>
</html>