<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (isset($_POST['simpan'])) {

    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_hp          = $_POST['no_hp'];
    $alamat         = $_POST['alamat'];

    mysqli_query($conn, "
        INSERT INTO pelanggan(nama_pelanggan, no_hp, alamat)
        VALUES('$nama_pelanggan','$no_hp','$alamat')
    ");

    header("Location: pelanggan.php");
    exit;
}

if (isset($_POST['update'])) {

    $id              = $_POST['id'];
    $nama_pelanggan  = $_POST['nama_pelanggan'];
    $no_hp           = $_POST['no_hp'];
    $alamat          = $_POST['alamat'];

    mysqli_query($conn, "
        UPDATE pelanggan
        SET
            nama_pelanggan='$nama_pelanggan',
            no_hp='$no_hp',
            alamat='$alamat'
        WHERE id_pelanggan='$id'
    ");

    header("Location: pelanggan.php");
    exit;
}

if ($action == 'hapus') {

    $id = $_GET['id'];

    mysqli_query($conn, "
        DELETE FROM pelanggan
        WHERE id_pelanggan='$id'
    ");

    header("Location: pelanggan.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pelanggan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Data Pelanggan</h1>

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
    <div class="card">
        <h2>Tambah Pelanggan</h2>
        <form method="POST">
            <label>Nama Pelanggan</label><br>
            <input type="text"
                name="nama_pelanggan"
                required>

            <br><br>

            <label>No HP</label><br>
            <input type="text"
                name="no_hp"
                required>

            <br><br>

            <label>Alamat</label><br>
            <textarea name="alamat"></textarea>

            <br><br>

            <button class="btn-danger" type="submit" name="simpan">
                Simpan
            </button>

            <a href="pelanggan.php">
                <button class="btn-danger" type="button">
                    Batal
                </button>
            </a>
        </form>
    </div>
    <?php

    } elseif ($action == 'edit') {

        $id = $_GET['id'];

        $query = mysqli_query($conn, "
            SELECT * FROM pelanggan
            WHERE id_pelanggan='$id'
        ");

        $data = mysqli_fetch_assoc($query);
    ?>

    <div class="card">
        <h2>Edit Pelanggan</h2>
        <form method="POST">
            <input type="hidden"
                name="id"
                value="<?= $data['id_pelanggan']; ?>">
            <label>Nama Pelanggan</label><br>
            <input type="text"
                name="nama_pelanggan"
                value="<?= $data['nama_pelanggan']; ?>"
                required>

            <br><br>

            <label>No HP</label><br>
            <input type="text"
                name="no_hp"
                value="<?= $data['no_hp']; ?>"
                required>
            <br><br>

            <label>Alamat</label><br>
            <textarea name="alamat"><?= $data['alamat']; ?></textarea>

            <br><br>

            <button class="btn-danger" type="submit" name="update">
                Update
            </button>

            <a href="pelanggan.php">
                <button class="btn-danger" type="button">
                    Batal
                </button>
            </a>
        </form>
    </div>
    <?php

    } else {

        $query = mysqli_query($conn, "
            SELECT * FROM pelanggan
        ");
    ?>

    <a href="pelanggan.php?action=tambah">
        Tambah Pelanggan
    </a>

    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Nama Pelanggan</th>
            <th>No HP</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
    <?php

    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
    ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= $data['nama_pelanggan']; ?></td>
        <td><?= $data['no_hp']; ?></td>
        <td><?= $data['alamat']; ?></td>
        <td>

            <a href="pelanggan.php?action=edit&id=<?= $data['id_pelanggan']; ?>">
                Edit
            </a>

            |

            <a href="pelanggan.php?action=hapus&id=<?= $data['id_pelanggan']; ?>"
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