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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <h1>Dashboard Admin</h1>
    <p>
        Selamat datang, <?= $_SESSION['nama']; ?>
    </p>

    <hr>

    <div class="card">
        <h3>Menu Admin</h3>

        <div class="menu">
            <a href="pelanggan.php" class="btn">
                Data Pelanggan
            </a>

            <a href="ps_unit.php" class="btn">
                Data PS
            </a>

            <a href="transaksi.php" class="btn">
                Data Transaksi
            </a>

            <a href="../auth/logout.php" class="btn-danger">
                Logout
            </a>
        </div>
    </div>

</body>
</html>