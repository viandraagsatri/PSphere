<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_rental_ps";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>