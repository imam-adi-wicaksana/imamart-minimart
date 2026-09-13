<?php
session_start();
require 'koneksi.php';

// 1. Cek apakah yang akses adalah Admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak!']);
    exit;
}

// 2. Ambil data dari AJAX
$input = json_decode(file_get_contents('php://input'), true);
$invoice = mysqli_real_escape_string($conn, $input['invoice']);
$status = mysqli_real_escape_string($conn, $input['status']);

// 3. Update Database
$query = "UPDATE orders SET status = '$status' WHERE invoice_number = '$invoice'";

if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>