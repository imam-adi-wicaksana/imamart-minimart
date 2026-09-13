<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi database benar

// --- 1. PROTEKSI KEAMANAN ---
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- AMBIL DATA ADMIN DARI DATABASE ---
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin' LIMIT 1");
$admin = mysqli_fetch_assoc($query_admin);

// Folder upload gambar
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// A. TAMBAH PRODUK
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $old_price = !empty($_POST['old_price']) ? $_POST['old_price'] : "NULL"; // Handle null
    $promo = mysqli_real_escape_string($conn, $_POST['promo']);

    // Upload Gambar
    $image_name = time() . '_' . $_FILES['image']['name']; // Rename agar unik
    $target_file = $target_dir . basename($image_name);
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $query = "INSERT INTO products (name, category, price, old_price, image, promo) 
                  VALUES ('$name', '$category', '$price', $old_price, '$image_name', '$promo')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['swal_icon'] = "success";
            $_SESSION['swal_title'] = "Berhasil!";
            $_SESSION['swal_text'] = "Produk berhasil ditambahkan.";
        } else {
            $_SESSION['swal_icon'] = "error";
            $_SESSION['swal_title'] = "Gagal!";
            $_SESSION['swal_text'] = "Error database: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['swal_icon'] = "error";
        $_SESSION['swal_title'] = "Gagal!";
        $_SESSION['swal_text'] = "Gagal mengupload gambar.";
    }
    header("Location: data_produk.php");
    exit;
}

// B. EDIT PRODUK
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $old_price = !empty($_POST['old_price']) ? $_POST['old_price'] : "NULL";
    $promo = mysqli_real_escape_string($conn, $_POST['promo']);
    
    // Cek apakah ada gambar baru yang diupload
    if (!empty($_FILES['image']['name'])) {
        // Hapus gambar lama
        $q_old = mysqli_query($conn, "SELECT image FROM products WHERE id='$id'");
        $d_old = mysqli_fetch_assoc($q_old);
        if ($d_old && file_exists($target_dir . $d_old['image'])) {
            unlink($target_dir . $d_old['image']);
        }

        // Upload baru
        $image_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . basename($image_name));
        
        $query = "UPDATE products SET name='$name', category='$category', price='$price', old_price=$old_price, promo='$promo', image='$image_name' WHERE id='$id'";
    } else {
        // Update tanpa ganti gambar
        $query = "UPDATE products SET name='$name', category='$category', price='$price', old_price=$old_price, promo='$promo' WHERE id='$id'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['swal_icon'] = "success";
        $_SESSION['swal_title'] = "Berhasil!";
        $_SESSION['swal_text'] = "Data produk diperbarui.";
    } else {
        $_SESSION['swal_icon'] = "error";
        $_SESSION['swal_title'] = "Gagal!";
        $_SESSION['swal_text'] = "Error: " . mysqli_error($conn);
    }
    header("Location: data_produk.php");
    exit;
}

// C. HAPUS PRODUK
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Hapus file fisik gambar
    $q_img = mysqli_query($conn, "SELECT image FROM products WHERE id='$id'");
    $row_img = mysqli_fetch_assoc($q_img);
    if ($row_img && file_exists($target_dir . $row_img['image'])) {
        unlink($target_dir . $row_img['image']);
    }

    // Hapus data di DB
    if(mysqli_query($conn, "DELETE FROM products WHERE id='$id'")){
        $_SESSION['swal_icon'] = "success";
        $_SESSION['swal_title'] = "Terhapus!";
        $_SESSION['swal_text'] = "Produk berhasil dihapus.";
    }
    header("Location: data_produk.php");
    exit;
}

// --- 3. AMBIL DATA PRODUK ---
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - IMAMART</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: { fontFamily: { lato: ['Lato', 'sans-serif'] } }
            }
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
            
            <a href="data_produk.php" class="flex items-center gap-3 px-4 py-3 bg-blue-700 rounded-xl text-white shadow-lg font-bold transition">
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
            <a href="pengaturan.php" class="flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-blue-700 hover:text-white rounded-xl transition">
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
                <h2 class="text-xl font-bold text-gray-800">Manajemen Produk</h2>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800"><?php echo $admin ? $admin['fullname'] : 'Admin Utama'; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?php echo $admin ? $admin['role'] : 'admin'; ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-lg border-2 border-blue-200"><?php echo $admin ? strtoupper(substr($admin['fullname'], 0, 1)) : 'A'; ?></div>
            </div>
        </header>

        <div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
            
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <button onclick="openModal('addModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Produk Baru
                </button>
                
                <div class="relative w-full sm:w-64">
                    <input type="text" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 font-bold text-center">Gambar</th>
                                <th class="p-4 font-bold">Nama Produk</th>
                                <th class="p-4 font-bold">Kategori</th>
                                <th class="p-4 font-bold">Harga</th>
                                <th class="p-4 font-bold">Promo</th>
                                <th class="p-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if(mysqli_num_rows($products) == 0): ?>
                                <tr><td colspan="6" class="p-8 text-center text-gray-400">Belum ada produk. Silakan tambah data.</td></tr>
                            <?php else: ?>
                                <?php while($row = mysqli_fetch_assoc($products)): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="p-4 text-center">
                                        <img src="uploads/<?php echo $row['image']; ?>" alt="img" class="h-12 w-12 object-cover rounded-lg border border-gray-200 mx-auto bg-gray-50">
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800"><?php echo $row['name']; ?></p>
                                        <p class="text-xs text-gray-500">ID: <?php echo $row['id']; ?></p>
                                    </td>
                                    <td class="p-4">
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold capitalize">
                                            <?php echo str_replace('_', ' ', $row['category']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-blue-600 font-mono">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                                        <?php if($row['old_price'] > 0): ?>
                                            <p class="text-xs text-gray-400 line-through">Rp <?php echo number_format($row['old_price'], 0, ',', '.'); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4">
                                        <?php if(!empty($row['promo'])): ?>
                                            <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-bold border border-red-200">
                                                <i class="fa-solid fa-tag"></i> <?php echo $row['promo']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button onclick='editProduct(<?php echo json_encode($row); ?>)' class="text-blue-500 hover:text-blue-700 px-2 transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square text-lg"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="text-red-400 hover:text-red-600 px-2 transition" title="Hapus">
                                            <i class="fa-solid fa-trash text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl transform transition-all scale-100 overflow-hidden">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="bg-blue-600 p-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg"><i class="fa-solid fa-plus-circle"></i> Tambah Produk</h3>
                    <button type="button" onclick="closeModal('addModal')" class="hover:text-gray-200"><i class="fa-solid fa-times"></i></button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" required class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                        <select name="category" required class="w-full border rounded-lg p-2 bg-white">
                            <option value="buah_sayur">Buah & Sayur</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="kebutuhan_rumah">Kebutuhan rumah</option>
                            <option value="makanan_ringan">Makanan Ringan</option>
                            <option value="sembako">Sembako</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="price" required class="w-full border rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga Lama (Coret)</label>
                            <input type="number" name="old_price" class="w-full border rounded-lg p-2" placeholder="Opsional">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Promo Label</label>
                        <input type="text" name="promo" class="w-full border rounded-lg p-2" placeholder="Contoh: Diskon 10%, Terlaris (Opsional)">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Gambar Produk</label>
                        <input type="file" name="image" required accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div class="p-4 border-t bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded-lg">Batal</button>
                    <button type="submit" name="add_product" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="bg-yellow-500 p-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg"><i class="fa-solid fa-edit"></i> Edit Produk</h3>
                    <button type="button" onclick="closeModal('editModal')" class="hover:text-gray-100"><i class="fa-solid fa-times"></i></button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" id="edit_name" required class="w-full border rounded-lg p-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                        <select name="category" id="edit_category" required class="w-full border rounded-lg p-2 bg-white">
                            <option value="buah_sayur">Buah & Sayur</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="kebutuhan_rumah">Kebutuhan Rumah</option>
                            <option value="makanan_ringan">Makanan Ringan</option>
                            <option value="sembako">Sembako</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="price" id="edit_price" required class="w-full border rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga Lama</label>
                            <input type="number" name="old_price" id="edit_old_price" class="w-full border rounded-lg p-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Promo Label</label>
                        <input type="text" name="promo" id="edit_promo" class="w-full border rounded-lg p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Ganti Gambar (Opsional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                        <p class="text-xs text-gray-400 mt-1">*Biarkan kosong jika tidak ingin mengganti gambar.</p>
                    </div>
                </div>

                <div class="p-4 border-t bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-gray-600 font-bold hover:bg-gray-200 rounded-lg">Batal</button>
                    <button type="submit" name="edit_product" class="px-4 py-2 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600 shadow">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- Logic Modal ---
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // --- Logic Isi Data ke Modal Edit ---
        function editProduct(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_category').value = data.category;
            document.getElementById('edit_price').value = data.price;
            document.getElementById('edit_old_price').value = data.old_price;
            document.getElementById('edit_promo').value = data.promo;
            
            openModal('editModal');
        }

        // --- SweetAlert Konfirmasi Hapus ---
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `data_produk.php?delete=${id}`;
                }
            })
        }

        // --- SweetAlert Notifikasi PHP ---
        <?php if(isset($_SESSION['swal_icon'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['swal_icon']; ?>',
                title: '<?php echo $_SESSION['swal_title']; ?>',
                text: '<?php echo $_SESSION['swal_text']; ?>',
                timer: 3000,
                showConfirmButton: false
            });
            <?php 
            // Hapus session setelah tampil agar tidak muncul terus saat refresh
            unset($_SESSION['swal_icon']);
            unset($_SESSION['swal_title']);
            unset($_SESSION['swal_text']);
            ?>
        <?php endif; ?>
    </script>

    <script>
        function toggleAdminSidebar() {
            document.getElementById('admin-sidebar').classList.toggle('-translate-x-full');
        }
    </script>
</body>
</html>