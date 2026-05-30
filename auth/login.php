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
<html>
<head>
    <title>Login Rental PS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Rental PS</h2>

    <?php
    if (isset($error)) {
        echo $error;
    }
    ?>

    <div class="login-container">
        <div class="login-card">
            <h1>Login</h1>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username">

                <label>Password</label>
                <input type="password" name="password">

                <button class="btn-danger" type="submit" name="login">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>