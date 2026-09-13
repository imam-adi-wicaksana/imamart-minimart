<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - IMAMART</title>
    
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
        body { font-family: 'Lato', sans-serif; }
        .logo-white { filter: brightness(0) invert(1); }
        .pattern-grid {
            background-image: radial-gradient(#ffffff 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.1;
        }
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
                <a href="tentang_kami.php" class="text-white border-b-2 border-white">Tentang Kami</a>
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

    <div class="relative bg-blue-700 text-white overflow-hidden">
        <div class="absolute inset-0 pattern-grid"></div>
        <div class="container mx-auto px-4 py-20 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Lebih Dari Sekadar Minimarket</h1>
            <p class="text-blue-100 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                Kami hadir untuk memenuhi kebutuhan harian keluarga Indonesia dengan kualitas terbaik, harga bersahabat, dan pelayanan sepenuh hati.
            </p>
        </div>
        <div class="absolute bottom-0 w-full leading-none">
            <svg class="block w-full h-12 md:h-24" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="#f9fafb" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </div>

    <div class="flex-grow bg-gray-50 pb-16">
        
        <div class="container mx-auto px-4 py-12">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="w-full md:w-1/2">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition duration-500">
                        <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80" alt="Interior Toko" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6">
                            <span class="text-white font-bold text-lg"><i class="fa-solid fa-location-dot mr-2"></i> Gerai Pertama, 2018</span>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <span class="text-blue-600 font-bold tracking-wider uppercase text-sm mb-2 block">Cerita Kami</span>
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Berawal dari Niat Sederhana</h2>
                    <p class="text-gray-600 leading-loose mb-4 text-justify">
                        <b>IMAMART</b> didirikan pada tahun 2018 dengan visi sederhana: menyediakan akses mudah terhadap produk segar dan berkualitas bagi masyarakat sekitar. Bermula dari sebuah toko kelontong kecil di sudut kota, kami tumbuh berkat kepercayaan pelanggan setia kami.
                    </p>
                    <p class="text-gray-600 leading-loose text-justify">
                        Kini, IMAMART telah berkembang menjadi minimarket modern yang tidak hanya menjual produk, tetapi juga memberikan pengalaman belanja yang nyaman dan efisien melalui integrasi teknologi digital.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white py-12 border-y border-gray-200">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-4xl font-black text-blue-600 mb-2">5+</div>
                        <div class="text-gray-500 font-medium">Tahun Melayani</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-blue-600 mb-2">10k+</div>
                        <div class="text-gray-500 font-medium">Produk Tersedia</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-blue-600 mb-2">50k+</div>
                        <div class="text-gray-500 font-medium">Pelanggan Bahagia</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-blue-600 mb-2">24/7</div>
                        <div class="text-gray-500 font-medium">Layanan Online</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Kenapa Memilih IMAMART?</h2>
                <p class="text-gray-500 mt-2">Komitmen kami untuk kepuasan belanja Anda.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition text-center group">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fa-solid fa-leaf text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Selalu Segar</h3>
                    <p class="text-gray-600 text-sm">Kami menjamin kesegaran buah, sayur, dan daging setiap hari langsung dari petani lokal.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition text-center group">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fa-solid fa-tags text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Harga Jujur</h3>
                    <p class="text-gray-600 text-sm">Harga kompetitif tanpa biaya tersembunyi. Hemat belanja setiap hari dengan promo menarik.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition text-center group">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fa-solid fa-truck-fast text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Pengiriman Cepat</h3>
                    <p class="text-gray-600 text-sm">Pesanan online Anda akan kami antar dengan aman dan cepat sampai ke depan pintu rumah.</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 py-16">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-12">Tokoh di Balik Layar</h2>
                
                <div class="flex flex-wrap justify-center gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-md w-64">
                        <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-blue-100">
                            <img src="https://i.pinimg.com/1200x/3f/a2/71/3fa2719b31ff19cedeb2e5dced2d6aa5.jpg" alt="Founder" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-bold text-lg">Imam Makmum</h4>
                        <p class="text-blue-600 text-sm font-semibold mb-2">Founder & CEO</p>
                        <p class="text-gray-500 text-xs italic">"Melayani dengan hati adalah kunci utama bisnis kami."</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md w-64">
                        <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-blue-100">
                            <img src="https://i.pinimg.com/736x/5c/69/8f/5c698fa4feb8e18968d59d2550a14652.jpg" alt="Manager" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-bold text-lg">Shobirotul Mikhanah</h4>
                        <p class="text-blue-600 text-sm font-semibold mb-2">Operasional Manager</p>
                        <p class="text-gray-500 text-xs italic">"Kualitas produk adalah prioritas nomor satu."</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md w-64">
                        <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-blue-100">
                            <img src="https://i.pinimg.com/736x/31/28/b1/3128b1ee378a180225177e029c40a26d.jpg" alt="Tech Lead" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-bold text-lg">Andramedia Cesa</h4>
                        <p class="text-blue-600 text-sm font-semibold mb-2">IT Specialist</p>
                        <p class="text-gray-500 text-xs italic">"Inovasi teknologi untuk kemudahan belanja."</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 mt-16">
            <div class="bg-blue-600 rounded-2xl p-10 text-center text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cartographer.png')]"></div>
                
                <h2 class="text-3xl font-bold mb-4 relative z-10">Siap untuk Berbelanja?</h2>
                <p class="text-blue-100 mb-8 max-w-xl mx-auto relative z-10">Temukan ribuan produk kebutuhan harian dengan harga terbaik hanya di IMAMART.</p>
                <a href="product.php" class="bg-white text-blue-600 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition transform hover:scale-105 inline-block relative z-10 shadow-lg">
                    Belanja Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
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