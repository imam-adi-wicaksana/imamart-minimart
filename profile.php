<?php
session_start();
require 'koneksi.php'; 

// 1. CEK LOGIN
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. AMBIL DATA USER
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$profile = mysqli_fetch_assoc($query_user);

// 3. AMBIL DATA ORDERS (Sesuai Tabel Kamu)
$query_orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC");
$total_transaksi = mysqli_num_rows($query_orders);

// 4. HITUNG TOTAL PENGELUARAN
// Menggunakan kolom 'total_amount' dan mengecualikan status 'Batal'
$query_expense = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE user_id = '$user_id' AND status != 'Batal'");
$data_expense = mysqli_fetch_assoc($query_expense);
$total_pengeluaran = $data_expense['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - IMAMART</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> 
        html { overflow-y: scroll; }
        body { font-family: 'Lato', sans-serif; } 
        .logo-white { filter: brightness(0) invert(1); }
        .scroller::-webkit-scrollbar { width: 6px; height: 6px; }
        .scroller::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="font-lato bg-white text-gray-800 flex flex-col min-h-screen">

    <header class="bg-blue-600 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="home.php">
                    <img src="LOGO_IMAMART.png" alt="IMAMART Logo" class="h-10 w-auto logo-white hover:opacity-90 transition">
                </a>
            </div>

            <nav class="hidden md:flex space-x-8 font-bold text-blue-100">
                <a href="home.php" class="hover:text-white transition">Home</a>
                <a href="product.php" class="hover:text-white transition">Product</a>
                <a href="voucher.php" class="hover:text-white transition">Voucher</a>
                <a href="tentang_kami.php" class="hover:text-white transition">Tentang Kami</a>
            </nav>

            <div class="flex items-center space-x-4">
                <div class="relative hidden sm:block">
                    <input type="text" placeholder="Cari produk..." class="bg-blue-500 text-white placeholder-blue-200 rounded-full px-4 py-1.5 focus:outline-none focus:ring-2 focus:ring-white text-sm w-48 transition-all focus:w-64">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-200 hover:text-white">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                <a href="checkout.php" class="text-2xl text-blue-200 hover:text-white transition relative group mt-1">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <!-- Badge angka jumlah produk (Dijalankan oleh JavaScript) -->
                    <span id="cart-badge" class="absolute -top-1 -right-2 bg-red-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full hidden">
                        0
                    </span>
                    <!-- Tooltip saat di-hover -->
                    <span class="absolute right-0 top-8 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        Keranjang Belanja
                    </span>
                </a>
                
                <?php 
                    // Cek apakah user sudah login
                    if(isset($_SESSION['user'])) {
                        // Jika SUDAH login, arahkan ke profile.php
                        $profile_link = "profile.php";
                        $icon_class = "text-white"; // Indikator aktif (putih terang)
                    } else {
                        // Jika BELUM login, arahkan ke login.php
                        $profile_link = "login.php";
                        $icon_class = "text-blue-200 hover:text-white"; // Indikator belum aktif
                    }
                ?>
                <a href="<?php echo $profile_link; ?>" class="text-2xl <?php echo $icon_class; ?> transition relative group">
                    <i class="fa-solid fa-circle-user"></i>
                    <span class="absolute right-0 top-8 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        <?php echo isset($_SESSION['user']) ? 'Hai, '.$_SESSION['user'] : 'Masuk Akun'; ?>
                    </span>
                </a>
                
                <!-- Burger Button -->
                <button onclick="toggleMobileMenu()" class="md:hidden text-white text-2xl ml-2 focus:outline-none">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <nav id="mobile-menu" class="hidden md:hidden bg-blue-700 text-white font-bold flex-col space-y-3 px-4 py-4 shadow-inner">
            <a href="home.php" class="block hover:text-blue-200 transition">Home</a>
            <a href="product.php" class="block hover:text-blue-200 transition">Product</a>
            <a href="voucher.php" class="block hover:text-blue-200 transition">Voucher</a>
            <a href="tentang_kami.php" class="block hover:text-blue-200 transition">Tentang Kami</a>
        </nav>
    </header>

    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                    <div class="h-28 bg-gradient-to-r from-blue-500 to-indigo-600 relative">
                        <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2">
                            <div class="w-20 h-20 bg-white p-1 rounded-full shadow-lg">
                                <div class="w-full h-full bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold">
                                    <?php echo strtoupper(substr($profile['fullname'], 0, 1)); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-12 pb-6 px-6 text-center">
                        <h2 class="text-lg font-bold text-gray-800"><?php echo $profile['fullname']; ?></h2>
                        <span class="inline-block bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-full mt-1 mb-4">
                            @<?php echo $profile['username']; ?>
                        </span>
                        
                        <div class="text-left space-y-3 mt-2">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-500 shadow-sm">
                                    <i class="fa-regular fa-envelope text-xs"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Email</p>
                                    <p class="text-xs font-bold text-gray-700 truncate"><?php echo $profile['email']; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-500 shadow-sm">
                                    <i class="fa-solid fa-calendar-days text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Bergabung</p>
                                    <p class="text-xs font-bold text-gray-700"><?php echo date('d M Y', strtotime($profile['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>

                        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?');" class="mt-6 flex items-center justify-center gap-2 w-full py-2.5 border border-red-100 text-red-600 font-bold text-xs rounded-xl hover:bg-red-50 transition">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar Akun
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-6">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between relative overflow-hidden group">
                        <div class="relative z-10">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Transaksi</p>
                            <h3 class="text-3xl font-black text-gray-800 mt-1"><?php echo $total_transaksi; ?></h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between relative overflow-hidden group">
                        <div class="relative z-10">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pengeluaran</p>
                            <h3 class="text-3xl font-black text-gray-800 mt-1">
                                <span class="text-base align-top text-gray-500 font-bold mr-0.5">Rp</span><?php echo number_format($total_pengeluaran, 0, ',', '.'); ?>
                            </h3>
                        </div>
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Riwayat Pesanan</h3>
                            <p class="text-xs text-gray-500">Daftar semua transaksi yang pernah kamu lakukan.</p>
                        </div>
                        <a href="product.php" class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition px-4 py-2 rounded-lg shadow-md shadow-blue-200">
                            <i class="fa-solid fa-cart-plus mr-1"></i> Belanja Lagi
                        </a>
                    </div>

                    <div class="overflow-x-auto scroller">
                        <?php if (mysqli_num_rows($query_orders) > 0): ?>
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400 tracking-wide">
                                <tr>
                                    <th class="px-6 py-4">Invoice</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Metode</th>
                                    <th class="px-6 py-4">Total</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php while ($row = mysqli_fetch_assoc($query_orders)): ?>
                                <tr class="hover:bg-blue-50/50 transition duration-150">
                                    
                                    <td class="px-6 py-4">
                                        <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs">
                                            <?php echo $row['invoice_number']; ?>
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-700"><?php echo date('d M Y', strtotime($row['order_date'])); ?></div>
                                        <div class="text-[10px] text-gray-400"><?php echo date('H:i', strtotime($row['order_date'])); ?> WIB</div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-gray-600 border border-gray-200 px-2 py-1 rounded bg-white">
                                            <?php echo strtoupper($row['payment_method']); ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        Rp <?php echo number_format($row['total_amount'], 0, ',', '.'); ?>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-center">
                                        <?php 
                                            $s = $row['status'];
                                            if($s == 'Pending') {
                                                echo '<span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full text-xs font-bold"><span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span> Pending</span>';
                                            } elseif($s == 'Dikirim') {
                                                echo '<span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-truck-fast text-[10px]"></i> Dikirim</span>';
                                            } elseif($s == 'Lunas') {
                                                echo '<span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-check text-[10px]"></i> Lunas</span>';
                                            } else {
                                                echo '<span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-xmark text-[10px]"></i> Batal</span>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="bg-gray-50 p-4 rounded-full mb-3">
                                    <i class="fa-solid fa-receipt text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-bold text-sm">Belum ada riwayat transaksi.</p>
                                <p class="text-gray-400 text-xs mt-1">Data pembelianmu akan muncul di sini.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="bg-blue-600 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <div class="text-center md:text-left">
                    <h3 class="text-xl font-bold mb-4 flex items-center justify-center md:justify-start gap-2">
                        <i class="fa-solid fa-store"></i> IMAMART
                    </h3>
                    <p class="text-blue-100 text-sm leading-relaxed md:pr-4">
                        Minimarket modern pilihan keluarga dengan harga bersahabat, produk segar, dan pelayanan ramah sepenuh hati.
                    </p>
                </div>

                <div class="text-center md:text-left">
                    <h3 class="text-lg font-bold mb-4 border-b-2 border-blue-400 inline-block pb-1">Akses Cepat</h3>
                    <ul class="space-y-2 text-blue-100 text-sm">
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Promo Hari Ini</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Cek Voucher</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Status Pesanan</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Hubungi Kami</a></li>
                    </ul>
                </div>

                <div class="text-center md:text-left">
                    <h3 class="text-lg font-bold mb-4 border-b-2 border-blue-400 inline-block pb-1">Tentang Market</h3>
                    <ul class="space-y-2 text-blue-100 text-sm">
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Profil Pendiri</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Izin Usaha</a></li>
                        <li><a href="#" class="hover:text-white hover:pl-1 transition-all">Jam Operasional</a></li>
                    </ul>
                </div>

                <div class="text-center md:text-left flex flex-col items-center md:items-start">
                    <h3 class="text-lg font-bold mb-4 border-b-2 border-blue-400 inline-block pb-1">Lokasi Kami</h3>
                    <div class="rounded-lg overflow-hidden shadow-lg border-2 border-blue-400 mb-3 aspect-square w-40">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.666466953933!2d106.82496467499002!3d-6.175387060510842!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764587d%3A0x649e330b632945a8!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1709228812345!5m2!1sid!2sid" 
                            style="border:0;"
                            class="w-full h-full" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    <p class="text-xs text-blue-100"><i class="fa-solid fa-location-dot mr-1"></i> Jl. Minimarket No. 1, Jakarta Pusat</p>
                </div>
            </div>

            <hr class="border-blue-500 mb-6">

            <div class="text-center text-sm text-blue-200">
                © <?php echo date("Y"); ?> IMAMART. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            menu.classList.toggle('flex');
        }
    </script>
</body>
</html>