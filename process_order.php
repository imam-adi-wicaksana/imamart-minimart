<?php
session_start();
require 'koneksi.php';

header('Content-Type: application/json');

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu!']);
    exit;
}

// 2. Terima Data JSON dari Javascript
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

// 3. Siapkan Data
$user_id = $_SESSION['user_id'];
$customer_name = $_SESSION['user']; // Atau ambil dari input form jika ada
$invoice_number = $input['invoice_number'];
$total_amount = $input['total_amount'];
$payment_method = $input['payment_method'];
$status = ($payment_method == 'TUNAI') ? 'Pending' : 'Lunas'; // Logika sederhana status

// 4. Masukkan ke Database (Tabel orders)
$query = "INSERT INTO orders (invoice_number, user_id, customer_name, total_amount, payment_method, status, order_date) 
          VALUES ('$invoice_number', '$user_id', '$customer_name', '$total_amount', '$payment_method', '$status', NOW())";

if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil disimpan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
}
?>