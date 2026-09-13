<?php
$servername = "localhost";
$username = "root";      // Default user XAMPP
$password = "";          // Default password XAMPP (kosong)
$dbname = "db_imamart";  // Harus sama dengan nama database yang kamu buat di SQL

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>