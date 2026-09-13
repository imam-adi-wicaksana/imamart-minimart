<?php
require 'koneksi.php';
header('Content-Type: application/json');

// Ambil data JSON dari Javascript fetch()
$input = json_decode(file_get_contents('php://input'), true);
$code = mysqli_real_escape_string($conn, $input['code']);

// Cek di database (sesuai tabel baru)
$query = "SELECT * FROM vouchers WHERE nama_voucher = '$code' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $diskon = (int)$row['besar_diskon'];

    echo json_encode([
        'status' => 'success',
        'discount_amount' => $diskon,
        'title' => $row['nama_voucher']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Kode voucher tidak ditemukan!'
    ]);
}
?>