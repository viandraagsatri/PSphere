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
    $nama_user = $_POST['nama_user'];
    $username  = $_POST['username'];
    $password  = $_POST['password'];
    $role      = $_POST['role'];

    mysqli_query($conn, "
        INSERT INTO users(nama_user, username, password, role)
        VALUES('$nama_user','$username','$password','$role')
    ");

    header("Location: pengguna.php");
    exit;
}

if (isset($_POST['update'])) {
    $id        = $_POST['id'];
    $nama_user = $_POST['nama_user'];
    $username  = $_POST['username'];
    $password  = $_POST['password'];
    $role      = $_POST['role'];

    mysqli_query($conn, "
        UPDATE users
        SET
            nama_user='$nama_user',
            username='$username',
            password='$password',
            role='$role'
        WHERE id_user='$id'
    ");
    header("Location: pengguna.php");
    exit;
}

if ($action == 'hapus') {
    $id = $_GET['id'];

    if ($id == $_SESSION['id_user']) {
        echo "<script>alert('Tidak bisa menghapus akun yang sedang login!'); window.location='pengguna.php';</script>";
        exit;
    }

    mysqli_query($conn, "
        DELETE FROM users
        WHERE id_user='$id'
    ");

    header("Location: pengguna.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - PSphere</title>
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
            <li><a href="pelanggan.php"><i class="fa-solid fa-users"></i> Data Pelanggan</a></li>
            <li><a href="ps_unit.php"><i class="fa-solid fa-tv"></i> Kelola Mesin PS</a></li>
            <li><a href="laporantransaksi.php"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Transaksi</a></li>
            <li><a href="pengguna.php" class="active"><i class="fa-solid fa-user-shield"></i> Data Pengguna</a></li>
        </ul>
        <div class="sidebar-bottom">
            <a href="../auth/logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <form action="" method="GET" class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Cari nama, username, atau role..." value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <?php if(isset($_GET['action'])) { ?>
                    <input type="hidden" name="action" value="<?= $_GET['action']; ?>">
                <?php } ?>
            </form>

            <div class="user-profile">
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['nama']; ?></span>
                    <span class="user-role">Administrator</span>
                </div>
                <div class="user-avatar">
                    <?= substr($_SESSION['nama'], 0, 1); ?>
                </div>
            </div>
        </div>

        <div class="content-area">
            
            <?php if ($action == 'tambah') { ?>
            <div class="recent-activity">
                <h2 class="page-title">Tambah Pengguna</h2>
                <form method="POST" class="form-container">
                    <div class="input-group">
                        <label>Nama User</label>
                        <input type="text" name="nama_user" required>
                    </div>

                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="text" name="password" required>
                    </div>

                    <div class="input-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="simpan">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                        <a href="pengguna.php" class="btn-secondary">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                    </div>
                </form>
            </div>

            <?php } elseif ($action == 'edit') { 
                $id = $_GET['id'];
                $query = mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id'");
                $data = mysqli_fetch_assoc($query);
            ?>
            <div class="recent-activity">
                <h2 class="page-title">Edit Pengguna</h2>
                <form method="POST" class="form-container">
                    <input type="hidden" name="id" value="<?= $data['id_user']; ?>">
                    
                    <div class="input-group">
                        <label>Nama User</label>
                        <input type="text" name="nama_user" value="<?= isset($data) ? $data['nama_user'] : ''; ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= isset($data) ? $data['username'] : ''; ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="text" name="password" value="<?= isset($data) ? $data['password'] : ''; ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="admin" <?= isset($data) && $data['role']=='admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="kasir" <?= isset($data) && $data['role']=='kasir' ? 'selected' : ''; ?>>Kasir</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="update">
                            <i class="fa-solid fa-save"></i> Update
                        </button>
                        <a href="pengguna.php" class="btn-secondary">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                    </div>
                </form>
            </div>

            <?php } else { 
                // Logika Filter Pencarian
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if ($search != '') {
                    $query = mysqli_query($conn, "SELECT * FROM users WHERE nama_user LIKE '%$search%' OR username LIKE '%$search%' OR role LIKE '%$search%'");
                } else {
                    $query = mysqli_query($conn, "SELECT * FROM users");
                }
            ?>
            <div class="header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="page-title" style="margin: 0;">Data Pengguna</h2>
                <a href="pengguna.php?action=tambah" class="btn-primary" style="width: auto;"><i class="fa-solid fa-plus"></i> Tambah Pengguna</a>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Pengguna</th>
                            <th>Username</th>
                            <th>Role</th>
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
                            <td style="font-weight: bold; color: #1e293b;"><?= $data['nama_user']; ?></td>
                            <td><?= $data['username']; ?></td>
                            <td>
                                <?php if ($data['role'] == 'admin') { ?>
                                    <span style="background-color: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;">ADMIN</span>
                                <?php } else { ?>
                                    <span style="background-color: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;">KASIR</span>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="pengguna.php?action=edit&id=<?= $data['id_user']; ?>" class="action-btn edit-btn" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <a href="pengguna.php?action=hapus&id=<?= $data['id_user']; ?>" class="action-btn delete-btn" onclick="return confirm('Yakin ingin menghapus pengguna ini?')" title="Hapus Data">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(mysqli_num_rows($query) == 0) { echo "<tr><td colspan='5' style='text-align:center;'>Data pengguna tidak ditemukan</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>