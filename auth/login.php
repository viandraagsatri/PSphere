<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "
        SELECT * FROM users
        WHERE username='$username'
        AND password='$password'
    ");

    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['login'] = true;
        $_SESSION['nama']  = $data['nama_user'];
        $_SESSION['role']  = $data['role'];

        if ($data['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../kasir/dashboard.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rental PS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <svg class="logo" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 6H3C1.89543 6 1 6.89543 1 8V16C1 17.1046 1.89543 18 3 18H21C22.1046 18 23 17.1046 23 16V8C23 6.89543 22.1046 6 21 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 12H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="15.5" cy="13.5" r="1.5" fill="currentColor"/>
                    <circle cx="18.5" cy="10.5" r="1.5" fill="currentColor"/>
                </svg>
            </div>
            
            <h1>Selamat Datang</h1>
            <p class="subtitle">Silakan login untuk melanjutkan</p>

            <?php if (isset($error)): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>

                <button class="btn-primary" type="submit" name="login">Masuk Sekarang</button>
            </form>
        </div>
    </div>
</body>
</html>