<?php
session_start();
require 'koneksi.php'; // PENTING: Koneksi ke database

// --- LOGIKA FILTERING & SORTING DARI DATABASE ---

// 1. Siapkan variabel filter
$filter_categories = $_GET['category'] ?? []; 
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$sort_order = $_GET['sort'] ?? '';
$search_query = $_GET['search'] ?? ''; // Tambahan untuk fitur search bar

// 2. Mulai Query Dasar
$sql = "SELECT * FROM products";
$where_clauses = [];

// A. Filter Kategori (SQL IN)
if (!empty($filter_categories)) {
    // Sanitasi data agar aman dari SQL Injection
    $sanitized_cats = array_map(function($cat) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $cat) . "'";
    }, $filter_categories);
    
    $cat_string = implode(',', $sanitized_cats);
    $where_clauses[] = "category IN ($cat_string)";
}

// B. Filter Range Harga
if ($min_price != '') {
    $min = mysqli_real_escape_string($conn, $min_price);
    $where_clauses[] = "price >= $min";
}
if ($max_price != '') {
    $max = mysqli_real_escape_string($conn, $max_price);
    $where_clauses[] = "price <= $max";
}

// C. Filter Pencarian (Search Bar)
if ($search_query != '') {
    $search = mysqli_real_escape_string($conn, $search_query);
    $where_clauses[] = "name LIKE '%$search%'";
}

// Gabungkan semua kondisi WHERE
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

// D. Sorting (SQL ORDER BY)
if ($sort_order == 'price_low') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort_order == 'price_high') {
    $sql .= " ORDER BY price DESC";
} else {
    // Default urutan terbaru
    $sql .= " ORDER BY created_at DESC";
}

// 3. Eksekusi Query
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - IMAMART</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html { overflow-y: scroll; }
        body { font-family: 'Lato', sans-serif; }
        .logo-white { filter: brightness(0) invert(1); }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
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
                <a href="product.php" class="text-white border-b-2 border-white">Product</a>
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

    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="flex flex-col md:flex-row gap-8">
            
            <aside class="w-full md:w-1/4">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 sticky top-24">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-bold text-lg text-gray-800"><i class="fa-solid fa-filter text-blue-600 mr-2"></i>Filter</h2>
                        <a href="product.php" class="text-xs text-blue-500 hover:underline">Reset</a>
                    </div>
                    
                    <form action="product.php" method="GET" id="filterForm">
                        
                        <?php if($search_query): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        <?php endif; ?>

                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Kategori</h3>
                            <div class="space-y-2">
                                <?php function isChecked($val, $arr) { return in_array($val, $arr) ? 'checked' : ''; } ?>

                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="category[]" value="buah_sayur" class="form-checkbox text-blue-600 rounded focus:ring-blue-500" <?php echo isChecked('buah_sayur', $filter_categories); ?>>
                                    <span class="text-sm text-gray-600">Buah & Sayur</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="category[]" value="makanan" class="form-checkbox text-blue-600 rounded focus:ring-blue-500" <?php echo isChecked('makanan', $filter_categories); ?>>
                                    <span class="text-sm text-gray-600">Makanan</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="category[]" value="makanan_ringan" class="form-checkbox text-blue-600 rounded focus:ring-blue-500" <?php echo isChecked('makanan_ringan', $filter_categories); ?>>
                                    <span class="text-sm text-gray-600">Makanan Ringan</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="category[]" value="minuman" class="form-checkbox text-blue-600 rounded focus:ring-blue-500" <?php echo isChecked('minuman', $filter_categories); ?>>
                                    <span class="text-sm text-gray-600">Minuman</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="category[]" value="sembako" class="form-checkbox text-blue-600 rounded focus:ring-blue-500" <?php echo isChecked('sembako', $filter_categories); ?>>
                                    <span class="text-sm text-gray-600">Sembako</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="category[]" value="kebutuhan_rumah" class="form-checkbox text-blue-600 rounded focus:ring-blue-500" <?php echo isChecked('kebutuhan_rumah', $filter_categories); ?>>
                                    <span class="text-sm text-gray-600">Kebutuhan Rumah</span>
                                </label>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-4">

                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Range Harga</h3>
                            <div class="flex items-center gap-2 mb-2">
                                <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="Min" class="w-full bg-gray-50 border border-gray-300 text-sm rounded p-2 focus:outline-none focus:border-blue-500">
                                <span class="text-gray-400">-</span>
                                <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="Max" class="w-full bg-gray-50 border border-gray-300 text-sm rounded p-2 focus:outline-none focus:border-blue-500">
                            </div>
                        </div>

                        <hr class="border-gray-100 my-4">

                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Urutkan</h3>
                            <select name="sort" class="w-full bg-white border border-gray-300 text-sm rounded p-2 focus:outline-none focus:border-blue-500">
                                <option value="">Terbaru</option>
                                <option value="price_low" <?php echo ($sort_order == 'price_low') ? 'selected' : ''; ?>>Termurah</option>
                                <option value="price_high" <?php echo ($sort_order == 'price_high') ? 'selected' : ''; ?>>Termahal</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </aside>

            <main class="w-full md:w-3/4">
                
                <?php if(mysqli_num_rows($result) == 0): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-8 text-center rounded-lg">
                        <p class="text-gray-600 text-lg mb-2">Ops, Produk tidak ditemukan!</p>
                        <p class="text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
                        <a href="product.php" class="inline-block mt-4 text-blue-600 font-bold hover:underline">Reset Semua Filter</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <?php while($product = mysqli_fetch_assoc($result)): ?>
                        <div class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition group flex flex-col justify-between">
                            
                            <div class="h-48 bg-gray-50 flex items-center justify-center relative overflow-hidden">
                                <img src="uploads/<?php echo $product['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                     onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                                
                                <?php if(!empty($product['promo'])): ?>
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-md uppercase">
                                        <?php echo $product['promo']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 text-sm mb-1 truncate" title="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php echo $product['name']; ?>
                                </h3>
                                
                                <div class="text-xs text-gray-500 mb-2 capitalize">
                                    <?php echo str_replace('_', ' ', $product['category']); ?>
                                </div>
                                
                                <div class="flex justify-between items-end">
                                    <div>
                                        <?php if($product['old_price'] > 0): ?>
                                            <p class="text-gray-400 text-[10px] line-through">Rp <?php echo number_format($product['old_price'], 0, ',', '.'); ?></p>
                                        <?php endif; ?>
                                        <p class="text-blue-600 font-bold text-sm">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                    </div>
                                    
                                    <button onclick="addToCartAndCheckout(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, 'uploads/<?php echo $product['image']; ?>')" 
                                            class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-blue-600 hover:text-white transition active:bg-blue-800">
                                        <i class="fa-solid fa-cart-arrow-down"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </main>
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

    <div id="toast-message" class="fixed top-24 right-5 bg-red-500 text-white px-5 py-3 rounded-lg shadow-xl transform transition-all duration-300 translate-x-full opacity-0 z-50 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-xl"></i>
        <div>
            <h4 class="font-bold text-sm">Berhasil!</h4>
            <p class="text-xs" id="toast-text">Produk masuk ke daftar checkout.</p>
        </div>
        <a href="checkout.php" class="ml-3 bg-white text-red-600 text-xs font-bold px-3 py-1.5 rounded hover:bg-red-50 transition shadow-sm">
            Lihat
        </a>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('imamart_cart')) || [];
        const cartBadge = document.getElementById('cart-badge');
        
        function updateBadge() {
            let count = cart.reduce((acc, item) => acc + item.qty, 0);
            // Tambahkan pengecekan if (cartBadge) agar JS tidak error jika badge tidak ada di HTML header
            if(cartBadge && count > 0) { 
                cartBadge.innerText = count; 
                cartBadge.classList.remove('hidden'); 
            }
        }
        updateBadge();

        let toastTimeout; // Variabel penahan waktu untuk animasi toast

        // Fungsi Tambah dan Tampilkan Toast Message (tanpa redirect langsung)
        function addToCartAndCheckout(id, name, price, image) {
            // Masukkan produk ke keranjang
            let existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty += 1;
            } else {
                cart.push({ id, name, price, image, qty: 1 });
            }
            localStorage.setItem('imamart_cart', JSON.stringify(cart));
            updateBadge(); // Update angka badge (jika ada)

            // Atur nama produk di pesan Toast
            const toast = document.getElementById('toast-message');
            const toastText = document.getElementById('toast-text');
            toastText.innerText = name + ' masuk ke checkout.';
            
            // Tampilkan Toast (hilangkan class sembunyi)
            toast.classList.remove('translate-x-full', 'opacity-0');
            
            // Reset waktu (jika user klik cepat berulang-ulang, waktunya diulang dari 0)
            clearTimeout(toastTimeout);
            
            // Sembunyikan otomatis setelah 3 detik
            toastTimeout = setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
            }, 3000);
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