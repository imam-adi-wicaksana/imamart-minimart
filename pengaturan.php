<?php
session_start();
require 'koneksi.php'; 

// --- 1. PROTEKSI KEAMANAN ---
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- 2. AMBIL DATA KHUSUS ADMIN UTAMA (HARDCODE QUERY) ---
// Kita langsung tembak ke username 'admin' sesuai permintaan kamu
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin' LIMIT 1");
$admin = mysqli_fetch_assoc($query_admin);

// Cek darurat: Jika data 'admin' terhapus di database
if (!$admin) {
    die("Error: Data user 'admin' tidak ditemukan di database. Silakan insert data SQL manual terlebih dahulu.");
}

$admin_id = $admin['id']; // ID = 1

// --- 3. LOGIC CRUD (UPDATE) ---

// A. UPDATE PROFIL
if (isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Update data berdasarkan ID admin yang diambil di atas
    $query_update = "UPDATE users SET fullname='$fullname', email='$email' WHERE id='$admin_id'";
    
    if (mysqli_query($conn, $query_update)) {
        $_SESSION['swal_icon'] = "success";
        $_SESSION['swal_title'] = "Berhasil!";
        $_SESSION['swal_text'] = "Profil Administrator diperbarui.";
        header("Refresh:1.5; url=pengaturan.php"); 
    } else {
        $_SESSION['swal_icon'] = "error";
        $_SESSION['swal_title'] = "Gagal";
        $_SESSION['swal_text'] = mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Admin - IMAMART</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { lato: ['Lato', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="font-lato bg-gray-100 flex h-screen overflow-hidden">

    <aside id="admin-sidebar" class="w-64 bg-blue-800 text-white flex flex-col shadow-2xl z-50 fixed md:relative h-full transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="h-20 flex items-center justify-center border-b border-blue-700 relative">
            <h1 class="text-2xl font-black tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-store text-yellow-400"></i> IMAMART
            </h1>
            <button onclick="toggleAdminSidebar()" class="md:hidden absolute right-4 text-blue-200 hover:text-white text-xl focus:outline-none">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
            <p class="text-xs text-blue-300 font-bold uppercase mb-2 px-4">Menu Utama</p>
            <a href="admin.php" class="flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-blue-700 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-gauge-high w-6"></i> Dashboard
            </a>
            <a href="data_produk.php" class="flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-blue-700 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-box-open w-6"></i> Data Produk
            </a>
            <a href="pesanan_masuk.php" class="flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-blue-700 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-cart-shopping w-6"></i> Pesanan Masuk 
            </a>
            <a href="pelanggan.php" class="flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-blue-700 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-users w-6"></i> Pelanggan
            </a>

            <p class="text-xs text-blue-300 font-bold uppercase mt-6 mb-2 px-4">Lainnya</p>
            <a href="voucher_admin.php" class="flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-blue-700 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-ticket w-6"></i> Voucher
            </a>
            <a href="pengaturan.php" class="flex items-center gap-3 px-4 py-3 bg-blue-700 rounded-xl text-white shadow-lg font-bold transition">
                <i class="fa-solid fa-gear w-6"></i> Pengaturan
            </a>
        </nav>

        <div class="p-4 border-t border-blue-700">
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?');" class="flex items-center justify-center gap-2 w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-bold transition shadow-md">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 z-10">
            <div class="flex items-center gap-4">
                <button onclick="toggleAdminSidebar()" class="md:hidden text-gray-600 text-2xl focus:outline-none"><i class="fa-solid fa-bars"></i></button>
                <h2 class="text-xl font-bold text-gray-800">Pengaturan Akun</h2>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800"><?php echo $admin['fullname']; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?php echo $admin['role']; ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-lg border-2 border-blue-200">
                    <?php echo strtoupper(substr($admin['fullname'], 0, 1)); ?>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                        <div class="w-24 h-24 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-4xl border-4 border-white shadow-lg mx-auto mb-4">
                            <?php echo strtoupper(substr($admin['fullname'], 0, 1)); ?>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800"><?php echo $admin['fullname']; ?></h3>
                        <p class="text-sm text-gray-500 mb-4">@<?php echo $admin['username']; ?></p>
                        
                        <div class="text-left border-t border-gray-100 pt-6 space-y-4">
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Email</p>
                                <p class="text-gray-800 font-semibold text-sm"><?php echo $admin['email']; ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Bergabung</p>
                                <p class="text-gray-800 font-semibold text-sm"><?php echo $admin['created_at']; ?></p>
                            </div>
                             <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">ID User</p>
                                <p class="text-gray-800 font-semibold text-sm"><?php echo $admin['id']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 text-lg mb-6"><i class="fa-solid fa-user-pen mr-2 text-blue-600"></i> Edit Profil</h3>
                        <form action="" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Username</label>
                                    <input type="text" value="<?php echo $admin['username']; ?>" disabled class="w-full border bg-gray-100 rounded-lg p-3 text-gray-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Lengkap</label>
                                    <input type="text" name="fullname" value="<?php echo $admin['fullname']; ?>" required class="w-full border rounded-lg p-3">
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email</label>
                                <input type="email" name="email" value="<?php echo $admin['email']; ?>" required class="w-full border rounded-lg p-3">
                            </div>
                            <div class="text-right">
                                <button type="submit" name="update_profile" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-md">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center">
                            <i class="fa-solid fa-circle-info mr-2 text-blue-500"></i> Panduan & Kebijakan Admin
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex gap-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="text-blue-600 text-xl mt-1"><i class="fa-solid fa-shield-halved"></i></div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Keamanan Akun</h4>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Akun Admin memiliki akses penuh terhadap data penjualan dan produk. 
                                        Dilarang membagikan username atau password kepada pihak yang tidak berkepentingan.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                                <div class="text-yellow-600 text-xl mt-1"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Validasi Data Produk</h4>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Pastikan data harga dan stok produk selalu diupdate secara berkala. Kesalahan input harga adalah tanggung jawab admin yang bertugas saat itu.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 p-4 bg-green-50 rounded-xl border border-green-100">
                                <div class="text-green-600 text-xl mt-1"><i class="fa-solid fa-headset"></i></div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Bantuan Teknis</h4>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Jika terjadi error pada sistem atau database, segera hubungi tim IT Support di: <span class="font-bold font-mono text-green-700">it-support@imamart.com</span>
                                    </p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 text-center">
                                <p class="text-xs text-gray-400">IMAMART System v1.0.5 &copy; 2025</p>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if(isset($_SESSION['swal_icon'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['swal_icon']; ?>',
                title: '<?php echo $_SESSION['swal_title']; ?>',
                text: '<?php echo $_SESSION['swal_text']; ?>',
                timer: 3000,
                showConfirmButton: false
            });
            <?php unset($_SESSION['swal_icon'], $_SESSION['swal_title'], $_SESSION['swal_text']); ?>
        <?php endif; ?>
    </script>
    <script>
        function toggleAdminSidebar() {
            document.getElementById('admin-sidebar').classList.toggle('-translate-x-full');
        }
    </script>
</body>
</html>