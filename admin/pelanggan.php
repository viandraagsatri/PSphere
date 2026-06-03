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
    $id             = $_POST['id'];
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_hp          = $_POST['no_hp'];
    $alamat         = $_POST['alamat'];

    mysqli_query($conn, "
        UPDATE pelanggan
        SET nama_pelanggan='$nama_pelanggan', no_hp='$no_hp', alamat='$alamat'
        WHERE id_pelanggan='$id'
    ");
    header("Location: pelanggan.php");
    exit;
}

if ($action == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM pelanggan WHERE id_pelanggan='$id'");
    header("Location: pelanggan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - PSphere</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-gamepad"></i> RENTAL PS</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="pelanggan.php" class="active"><i class="fa-solid fa-users"></i> Data Pelanggan</a></li>
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
            <form action="" method="GET" class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Cari pelanggan..." value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <?php if(isset($_GET['action'])) { ?>
                    <input type="hidden" name="action" value="<?= $_GET['action']; ?>">
                <?php } ?>
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
            <?php if ($action == 'tambah') { ?>
            <div class="recent-activity">
                <h2 class="page-title">Tambah Pelanggan</h2>
                <form method="POST" class="form-container">
                    <div class="input-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" required>
                    </div>
                    <div class="input-group">
                        <label>No HP</label>
                        <input type="text" name="no_hp" required>
                    </div>
                    <div class="input-group">
                        <label>Alamat</label>
                        <textarea name="alamat" rows="4"></textarea>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="simpan"><i class="fa-solid fa-save"></i> Simpan</button>
                        <a href="pelanggan.php" class="btn-secondary"><i class="fa-solid fa-xmark"></i> Batal</a>
                    </div>
                </form>
            </div>

            <?php } elseif ($action == 'edit') { 
                $id = $_GET['id'];
                $query = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id_pelanggan='$id'");
                $data = mysqli_fetch_assoc($query);
            ?>
            <div class="recent-activity">
                <h2 class="page-title">Edit Pelanggan</h2>
                <form method="POST" class="form-container">
                    <input type="hidden" name="id" value="<?= $data['id_pelanggan']; ?>">
                    <div class="input-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" value="<?= $data['nama_pelanggan']; ?>" required>
                    </div>
                    <div class="input-group">
                        <label>No HP</label>
                        <input type="text" name="no_hp" value="<?= $data['no_hp']; ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Alamat</label>
                        <textarea name="alamat" rows="4"><?= $data['alamat']; ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="update"><i class="fa-solid fa-save"></i> Update</button>
                        <a href="pelanggan.php" class="btn-secondary"><i class="fa-solid fa-xmark"></i> Batal</a>
                    </div>
                </form>
            </div>

            <?php } else { 
                // Logika Filter Pencarian
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if ($search != '') {
                    $query = mysqli_query($conn, "SELECT * FROM pelanggan WHERE nama_pelanggan LIKE '%$search%' OR no_hp LIKE '%$search%' OR alamat LIKE '%$search%'");
                } else {
                    $query = mysqli_query($conn, "SELECT * FROM pelanggan");
                }
            ?>
            <div class="header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="page-title" style="margin: 0;">Data Pelanggan</h2>
                <a href="pelanggan.php?action=tambah" class="btn-primary" style="width: auto;"><i class="fa-solid fa-plus"></i> Tambah Pelanggan</a>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Pelanggan</th>
                            <th>No HP</th>
                            <th>Alamat</th>
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
                            <td><?= $data['no_hp']; ?></td>
                            <td><?= $data['alamat']; ?></td>
                            <td>
                                <div class="action-group">
                                    <a href="pelanggan.php?action=edit&id=<?= $data['id_pelanggan']; ?>" class="action-btn edit-btn" title="Edit Data"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="pelanggan.php?action=hapus&id=<?= $data['id_pelanggan']; ?>" class="action-btn delete-btn" onclick="return confirm('Yakin hapus data pelanggan ini?')" title="Hapus Data"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(mysqli_num_rows($query) == 0) { echo "<tr><td colspan='5' style='text-align:center;'>Data tidak ditemukan</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>