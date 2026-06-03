<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (isset($_POST['simpan'])) {
    $nama_ps       = $_POST['nama_ps'];
    $tipe          = $_POST['tipe'];
    $harga_per_jam = $_POST['harga_per_jam'];
    $status_ps     = $_POST['status_ps'];

    mysqli_query($conn, "INSERT INTO ps_unit(nama_ps, tipe, harga_per_jam, status_ps) VALUES('$nama_ps', '$tipe', '$harga_per_jam', '$status_ps')");
    header("Location: ps_unit.php");
    exit;
}

if (isset($_POST['update'])) {
    $id            = $_POST['id'];
    $nama_ps       = $_POST['nama_ps'];
    $tipe          = $_POST['tipe'];
    $harga_per_jam = $_POST['harga_per_jam'];
    $status_ps     = $_POST['status_ps'];

    mysqli_query($conn, "UPDATE ps_unit SET nama_ps='$nama_ps', tipe='$tipe', harga_per_jam='$harga_per_jam', status_ps='$status_ps' WHERE id_ps='$id'");
    header("Location: ps_unit.php");
    exit;
}

if ($action == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM ps_unit WHERE id_ps='$id'");
    header("Location: ps_unit.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mesin PS - PSphere</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-gamepad"></i> RENTAL PS</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="pelanggan.php"><i class="fa-solid fa-users"></i> Data Pelanggan</a></li>
            <li><a href="ps_unit.php" class="active"><i class="fa-solid fa-tv"></i> Kelola Mesin PS</a></li>
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
                <input type="text" name="search" placeholder="Cari mesin PS..." value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
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
                <h2 class="page-title">Tambah Mesin PS</h2>
                <form method="POST" class="form-container">
                    <div class="input-group">
                        <label>Nama / Nomor PS</label>
                        <input type="text" name="nama_ps" placeholder="Contoh: PS A" required>
                    </div>
                    <div class="input-group">
                        <label>Tipe Konsol</label>
                        <select name="tipe" required>
                            <option value="PS3">PlayStation 3</option>
                            <option value="PS4">PlayStation 4</option>
                            <option value="PS5">PlayStation 5</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Harga Per Jam (Rp)</label>
                        <input type="number" name="harga_per_jam" placeholder="Contoh: 10000" required>
                    </div>
                    <div class="input-group">
                        <label>Status</label>
                        <select name="status_ps" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="dipakai">Dipakai</option>
                            <option value="pemeliharaan">Pemeliharaan (Rusak/Maintenance)</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="simpan"><i class="fa-solid fa-save"></i> Simpan</button>
                        <a href="ps_unit.php" class="btn-secondary"><i class="fa-solid fa-xmark"></i> Batal</a>
                    </div>
                </form>
            </div>

            <?php } elseif ($action == 'edit') { 
                $id = $_GET['id'];
                $query = mysqli_query($conn, "SELECT * FROM ps_unit WHERE id_ps='$id'");
                $data = mysqli_fetch_assoc($query);
            ?>
            <div class="recent-activity">
                <h2 class="page-title">Edit Mesin PS</h2>
                <form method="POST" class="form-container">
                    <input type="hidden" name="id" value="<?= $data['id_ps']; ?>">
                    <div class="input-group">
                        <label>Nama / Nomor PS</label>
                        <input type="text" name="nama_ps" value="<?= $data['nama_ps']; ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Tipe Konsol</label>
                        <select name="tipe" required>
                            <option value="PS3" <?= ($data['tipe'] == 'PS3') ? 'selected' : ''; ?>>PlayStation 3</option>
                            <option value="PS4" <?= ($data['tipe'] == 'PS4') ? 'selected' : ''; ?>>PlayStation 4</option>
                            <option value="PS5" <?= ($data['tipe'] == 'PS5') ? 'selected' : ''; ?>>PlayStation 5</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Harga Per Jam (Rp)</label>
                        <input type="number" name="harga_per_jam" value="<?= $data['harga_per_jam']; ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Status</label>
                        <select name="status_ps" required>
                            <option value="tersedia" <?= ($data['status_ps'] == 'tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="dipakai" <?= ($data['status_ps'] == 'dipakai') ? 'selected' : ''; ?>>Dipakai</option>
                            <option value="pemeliharaan" <?= ($data['status_ps'] == 'pemeliharaan') ? 'selected' : ''; ?>>Pemeliharaan (Rusak/Maintenance)</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="update"><i class="fa-solid fa-save"></i> Update</button>
                        <a href="ps_unit.php" class="btn-secondary"><i class="fa-solid fa-xmark"></i> Batal</a>
                    </div>
                </form>
            </div>

            <?php } else { 
                // Logika Filter Pencarian
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if ($search != '') {
                    $query = mysqli_query($conn, "SELECT * FROM ps_unit WHERE nama_ps LIKE '%$search%' OR tipe LIKE '%$search%' OR status_ps LIKE '%$search%'");
                } else {
                    $query = mysqli_query($conn, "SELECT * FROM ps_unit");
                }
            ?>
            <div class="header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="page-title" style="margin: 0;">Kelola Mesin PS</h2>
                <a href="ps_unit.php?action=tambah" class="btn-primary" style="width: auto;"><i class="fa-solid fa-plus"></i> Tambah Mesin PS</a>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Mesin</th>
                            <th>Tipe</th>
                            <th>Harga Per Jam</th>
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
                            <td style="font-weight: bold; color: #1e293b;"><?= $data['nama_ps']; ?></td>
                            <td>
                                <span style="background-color: #f1f5f9; padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1; font-weight: bold; font-size: 12px; color: #3b82f6;">
                                    <?= $data['tipe']; ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($data['harga_per_jam'], 0, ',', '.'); ?></td>
                            <td>
                                <?php
                                    if ($data['status_ps'] == 'tersedia') {
                                        echo '<span style="color: #22c55e; font-weight: bold;"><i class="fa-solid fa-circle-check"></i> Tersedia</span>';
                                    } elseif ($data['status_ps'] == 'dipakai') {
                                        echo '<span style="color: #f59e0b; font-weight: bold;"><i class="fa-solid fa-gamepad"></i> Dipakai</span>';
                                    } else {
                                        echo '<span style="color: #ef4444; font-weight: bold;"><i class="fa-solid fa-wrench"></i> Pemeliharaan</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="ps_unit.php?action=edit&id=<?= $data['id_ps']; ?>" class="action-btn edit-btn" title="Edit Data"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="ps_unit.php?action=hapus&id=<?= $data['id_ps']; ?>" class="action-btn delete-btn" onclick="return confirm('Yakin hapus data PS ini?')" title="Hapus Data"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(mysqli_num_rows($query) == 0) { echo "<tr><td colspan='6' style='text-align:center;'>Data tidak ditemukan</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>