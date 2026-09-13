<?php
session_start(); 
require_once 'koneksi.php';
$query_best = "SELECT * FROM products ORDER BY RAND() LIMIT 4";
$result_best = mysqli_query($conn, $query_best);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAMART - Minimarket Pilihan Keluarga</title>
    
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
                <a href="home.php" class="text-white border-b-2 border-white">Home</a>
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

        <!-- Mobile Menu -->
        <nav id="mobile-menu" class="hidden md:hidden bg-blue-700 text-white font-bold flex-col space-y-3 px-4 py-4 shadow-inner">
            <a href="home.php" class="block hover:text-blue-200 transition">Home</a>
            <a href="product.php" class="block hover:text-blue-200 transition">Product</a>
            <a href="voucher.php" class="block hover:text-blue-200 transition">Voucher</a>
            <a href="tentang_kami.php" class="block hover:text-blue-200 transition">Tentang Kami</a>
        </nav>
    </header>

    <main class="flex-grow">
        
        <section class="relative h-[500px] bg-gray-900 flex items-center">
            <div class="absolute inset-0 overflow-hidden">
                <img src="https://i.pinimg.com/1200x/c4/7d/a9/c47da90759cf4cbfe08504655ceb9a66.jpg" 
                     alt="Background Minimarket" 
                     class="w-full h-full object-cover opacity-40">
            </div>

            <div class="container mx-auto px-4 relative z-10 text-center md:text-left">
                <h1 class="text-4xl md:text-6xl font-black text-white leading-tight mb-4 drop-shadow-lg">
                    Belanja Harian <br> Lebih Hemat & Cepat
                </h1>
                <p class="text-lg text-gray-200 mb-8 max-w-lg mx-auto md:mx-0 drop-shadow-md">
                    Ajaklah keluarga atau teman tercinta anda untuk datang ke minimarket dengan harga terjangkau dan lokasi strategis di tengah kota.
                </p>
                <div class="flex gap-4 justify-center md:justify-start">
                    <a href="product.php" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-bold transition transform hover:-translate-y-1 shadow-lg border-2 border-blue-600">
                        Belanja Sekarang
                    </a>
                    <a href="voucher.php" class="bg-transparent hover:bg-white hover:text-blue-600 text-white px-8 py-3 rounded-full font-bold transition transform hover:-translate-y-1 shadow-lg border-2 border-white">
                        Lihat Promo
                    </a>
                </div>
            </div>
        </section>

        <section class="container mx-auto px-4 py-16">
            <h2 class="text-3xl font-bold text-center text-blue-900 mb-12">Kenapa Belanja di IMAMART?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-2xl hover:-translate-y-2 transition duration-300 group">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 transition duration-300">
                        <i class="fa-solid fa-truck-fast text-2xl text-blue-600 group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pengiriman Cepat</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Pesanan diantar langsung ke depan pintu rumah Anda dalam hitungan jam.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-2xl hover:-translate-y-2 transition duration-300 group">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 transition duration-300">
                        <i class="fa-solid fa-tags text-2xl text-blue-600 group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Harga Termurah</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Kami menjamin harga terbaik dan bersaing dengan pasar tradisional.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-2xl hover:-translate-y-2 transition duration-300 group">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 transition duration-300">
                        <i class="fa-solid fa-carrot text-2xl text-blue-600 group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Produk Segar</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Sayur, buah, dan daging selalu segar setiap hari langsung dari petani.</p>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 border-l-4 border-blue-600 pl-4">Produk Terlaris</h2>
            <a href="product.php" class="text-blue-600 font-bold text-sm hover:underline">Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            
            <?php if(mysqli_num_rows($result_best) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result_best)): ?>
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 group flex flex-col justify-between">
                        
                        <div class="h-48 bg-gray-100 flex items-center justify-center relative overflow-hidden">
                            <?php if(!empty($row['promo'])): ?>
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full z-10">
                                    <?php echo $row['promo']; ?>
                                </span>
                            <?php endif; ?>

                            <img src="uploads/<?php echo $row['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                 onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                        </div>

                        <div class="p-5">
                            <h4 class="font-bold text-gray-800 mb-1 truncate" title="<?php echo $row['name']; ?>">
                                <?php echo $row['name']; ?>
                            </h4>
                            
                            <div class="flex flex-col mb-4">
                                <?php if($row['old_price'] > 0): ?>
                                    <span class="text-xs text-gray-400 line-through">
                                        Rp <?php echo number_format($row['old_price'], 0, ',', '.'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-transparent">.</span>
                                <?php endif; ?>

                                <span class="text-lg font-bold text-blue-600">
                                    Rp <?php echo number_format($row['price'], 0, ',', '.'); ?>
                                </span>
                            </div>

                            <button onclick="addToCartAndCheckout(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', <?php echo $row['price']; ?>, 'uploads/<?php echo $row['image']; ?>')" 
                                    class="w-full border border-blue-600 text-blue-600 py-2 rounded-lg font-bold hover:bg-blue-600 hover:text-white transition text-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-4 text-center text-gray-500 py-8">
                    Belum ada produk yang ditampilkan.
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

        <section class="container mx-auto px-4 py-16">
            <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-2xl p-8 md:p-12 flex flex-col md:flex-row justify-between items-center text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-10 rounded-full"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 bg-white opacity-10 rounded-full"></div>

                <div class="relative z-10 text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-3xl md:text-4xl font-black mb-2">VOUCHER IMAMART! ⚡</h2>
                    <p class="text-blue-100 text-lg">Dapatkan voucher diskon di IMAMART khusus pelanggan baru.</p>
                </div>
                <div class="relative z-10">
                    <a href="voucher.php" class="bg-yellow-400 text-yellow-900 hover:bg-yellow-300 px-8 py-3 rounded-full font-bold shadow-lg transition transform hover:scale-105 inline-block">
                        Klaim Voucher
                    </a>
                </div>
            </div>
        </section>

        <section class="bg-blue-50 py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center text-blue-900 mb-12">Apa Kata Pelanggan?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative">
                        <i class="fa-solid fa-quote-right absolute top-6 right-6 text-gray-200 text-4xl"></i>
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://i.pinimg.com/1200x/89/8a/22/898a222eff7268e28b791ef89374f1b5.jpg" class="w-12 h-12 rounded-full" alt="Budi">
                            <div>
                                <h4 class="font-bold text-gray-800">Budi Santoso</h4>
                                <div class="text-yellow-400 text-xs">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic leading-relaxed">"Sumpah sih belanja di sini hemat banget. Barangnya lengkap, harganya miring. Top banget IMAMART!"</p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative">
                        <i class="fa-solid fa-quote-right absolute top-6 right-6 text-gray-200 text-4xl"></i>
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://i.pinimg.com/736x/03/16/e3/0316e3e2311cf149f7326497f18c74cc.jpg" class="w-12 h-12 rounded-full" alt="Siti">
                            <div>
                                <h4 class="font-bold text-gray-800">Siti Aminah</h4>
                                <div class="text-yellow-400 text-xs">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic leading-relaxed">"Suka banget sama pelayanan delivery-nya. Pesen pagi, siang udah sampe depan rumah. Sayurnya juga seger."</p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative">
                        <i class="fa-solid fa-quote-right absolute top-6 right-6 text-gray-200 text-4xl"></i>
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://i.pinimg.com/736x/b2/a6/86/b2a686a2811252e3d98b0f9dabbefc7b.jpg" class="w-12 h-12 rounded-full" alt="Andi">
                            <div>
                                <h4 class="font-bold text-gray-800">Andi Saputra</h4>
                                <div class="text-yellow-400 text-xs">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic leading-relaxed">"Website-nya gampang dipake, nggak ribet. Pilihan pembayarannya juga banyak. Recommended buat belanja bulanan!"</p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative">
                        <i class="fa-solid fa-quote-right absolute top-6 right-6 text-gray-200 text-4xl"></i>
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://i.pinimg.com/736x/a6/c1/a9/a6c1a9ba588af0e200b44ec0f18210ad.jpg" class="w-12 h-12 rounded-full" alt="Imam">
                            <div>
                                <h4 class="font-bold text-gray-800">Imam Adi Wicaksana</h4>
                                <div class="text-yellow-400 text-xs">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic leading-relaxed">"Menurutku pelayanan di sini bagus banget dari online maupun offline, aku harap dipertahanin juga budaya seperti ini oke!"</p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative">
                        <i class="fa-solid fa-quote-right absolute top-6 right-6 text-gray-200 text-4xl"></i>
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://i.pinimg.com/736x/f3/b7/1a/f3b71a3c8b3bcc36ba340e127fc425e5.jpg" class="w-12 h-12 rounded-full" alt="Farah">
                            <div>
                                <h4 class="font-bold text-gray-800">Farah Wijaya</h4>
                                <div class="text-yellow-400 text-xs">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic leading-relaxed">"Rating 10/10! Ada tempat buat nyantai + akses Wi-FI gratis dijamin gak bosen belanja di sini!"</p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 relative">
                        <i class="fa-solid fa-quote-right absolute top-6 right-6 text-gray-200 text-4xl"></i>
                        <div class="flex items-center gap-4 mb-4">
                            <img src="https://i.pinimg.com/736x/be/5f/0f/be5f0f57fc416e9975ae2b553f804bb8.jpg" class="w-12 h-12 rounded-full" alt="Mila">
                            <div>
                                <h4 class="font-bold text-gray-800">Mila Valerina</h4>
                                <div class="text-yellow-400 text-xs">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic leading-relaxed">"Aku gak sabar nunggu voucher baru lagi, soalnya aku suka yg murah-murah gtu :v"</p>
                    </div>

                </div>
            </div>
        </section>

    </main>

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
    // Cek apakah script cart sudah ada, jika belum tambahkan logika ini
    let cart = JSON.parse(localStorage.getItem('imamart_cart')) || [];

    function addToCartAndCheckout(id, name, price, image) {
        let existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({ id, name, price, image, qty: 1 });
        }
        localStorage.setItem('imamart_cart', JSON.stringify(cart));
        
        // Redirect langsung ke checkout
        window.location.href = 'checkout.php';
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