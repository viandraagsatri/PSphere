<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Kasir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Dashboard Kasir</h1>

    <p>Selamat datang,
    <?php echo $_SESSION['nama']; ?>
    </p>

    <hr>

    <h3>Menu Kasir</h3>
    <div class="menu">
        <li>
            <a href="booking.php">
                Kelola Booking
            </a>
        </li>
        <li>
            <a href="transaksi.php">
                Kelola Transaksi
            </a>
        </li>
    </div>
        <hr>
            <a href="../auth/logout.php">
                Logout
            </a>
</body>
</html>