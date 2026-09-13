<?php
session_start();
require 'koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM vouchers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Page</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        lato: ['Lato', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        html { overflow-y: scroll; }
        .logo-white { filter: brightness(0) invert(1); }
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
                <a href="voucher.php" class="text-white border-b-2 border-white">Voucher</a>
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

        <!-- Mobile Menu -->
        <nav id="mobile-menu" class="hidden md:hidden bg-blue-700 text-white font-bold flex-col space-y-3 px-4 py-4 shadow-inner">
            <a href="home.php" class="block hover:text-blue-200 transition">Home</a>
            <a href="product.php" class="block hover:text-blue-200 transition">Product</a>
            <a href="voucher.php" class="block hover:text-blue-200 transition">Voucher</a>
            <a href="tentang_kami.php" class="block hover:text-blue-200 transition">Tentang Kami</a>
        </nav>
    </header>

    <div class="container mx-auto px-4 py-12 flex-grow">
        <h2 class="text-3xl font-black text-center text-gray-800 mb-2">Voucher Spesial</h2>
        <p class="text-center text-gray-500 mb-10">Salin kode di bawah dan gunakan saat pembayaran.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($v = mysqli_fetch_assoc($result)): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-xl transition flex h-40">
                <div class="<?php echo $v['color_class']; ?> w-1/3 flex flex-col items-center justify-center text-white p-2">
                    <span class="text-[10px] font-bold uppercase opacity-80 mb-1">Hemat</span>
                    <h3 class="text-2xl font-black">Rp <?php echo number_format($v['besar_diskon']/1000, 0); ?>rb</h3>
                </div>

                <div class="w-2/3 p-4 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg leading-tight mb-1"><?php echo $v['nama_voucher']; ?></h4>
                        <p class="text-xs text-gray-500 line-clamp-2"><?php echo $v['deskripsi']; ?></p>
                    </div>
                    <button onclick="copyCode('<?php echo $v['nama_voucher']; ?>')" class="mt-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold py-2 px-4 rounded-lg flex items-center justify-center gap-2 transition active:scale-95">
                        <i class="fa-regular fa-copy"></i> Salin Kode
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
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
        function copyCode(code) {
            navigator.clipboard.writeText(code);
            alert("Kode '" + code + "' berhasil disalin! Gunakan di halaman Checkout.");
        }
    </script>
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            menu.classList.toggle('flex');
        }
    </script>
</body>
</html>