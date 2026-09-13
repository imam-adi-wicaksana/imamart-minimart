<?php
session_start();
require 'koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- AMBIL DATA ADMIN DARI DATABASE ---
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin' LIMIT 1");
$admin = mysqli_fetch_assoc($query_admin);

// --- LOGIC CRUD ---

// A. TAMBAH
if (isset($_POST['add_voucher'])) {
    $nama_voucher = strtoupper(mysqli_real_escape_string($conn, $_POST['nama_voucher'])); // Auto Uppercase
    $besar_diskon = (int)$_POST['besar_diskon']; // Pastikan Angka
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $color_class = $_POST['color_class'];

    // Cek Duplikat
    $cek = mysqli_query($conn, "SELECT id FROM vouchers WHERE nama_voucher = '$nama_voucher'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['swal_icon'] = "error"; $_SESSION['swal_title'] = "Gagal"; $_SESSION['swal_text'] = "Nama Voucher sudah ada!";
    } else {
        $query = "INSERT INTO vouchers (nama_voucher, besar_diskon, deskripsi, color_class) VALUES ('$nama_voucher', '$besar_diskon', '$deskripsi', '$color_class')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['swal_icon'] = "success"; $_SESSION['swal_title'] = "Berhasil"; $_SESSION['swal_text'] = "Voucher ditambahkan.";
        } else {
            $_SESSION['swal_icon'] = "error"; $_SESSION['swal_title'] = "Error"; $_SESSION['swal_text'] = mysqli_error($conn);
        }
    }
    header("Location: voucher_admin.php"); exit;
}

// B. EDIT
if (isset($_POST['edit_voucher'])) {
    $id = $_POST['id'];
    $nama_voucher = strtoupper(mysqli_real_escape_string($conn, $_POST['nama_voucher']));
    $besar_diskon = (int)$_POST['besar_diskon'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $color_class = $_POST['color_class'];

    $query = "UPDATE vouchers SET nama_voucher='$nama_voucher', besar_diskon='$besar_diskon', deskripsi='$deskripsi', color_class='$color_class' WHERE id='$id'";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['swal_icon'] = "success"; $_SESSION['swal_title'] = "Update"; $_SESSION['swal_text'] = "Data berhasil diubah.";
    }
    header("Location: voucher_admin.php"); exit;
}

// C. HAPUS
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM vouchers WHERE id='$id'");
    $_SESSION['swal_icon'] = "success"; $_SESSION['swal_title'] = "Dihapus"; $_SESSION['swal_text'] = "Voucher dihapus.";
    header("Location: voucher_admin.php"); exit;
}

$vouchers = mysqli_query($conn, "SELECT * FROM vouchers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Voucher - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Lato', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

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
            <a href="voucher_admin.php" class="flex items-center gap-3 px-4 py-3 bg-blue-700 rounded-xl text-white shadow-lg font-bold transition">
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
                <h2 class="text-xl font-bold text-gray-800">Overview Dashboard</h2>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800"><?php echo $admin ? $admin['fullname'] : 'Admin Utama'; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?php echo $admin ? $admin['role'] : 'admin'; ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-lg border-2 border-blue-200">
                    <?php echo $admin ? strtoupper(substr($admin['fullname'], 0, 1)) : 'A'; ?>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-6">
                <button onclick="openModal('addModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($row = mysqli_fetch_assoc($vouchers)): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="<?php echo $row['color_class']; ?> p-4 text-white text-center">
                        <h3 class="font-black text-xl">Rp <?php echo number_format($row['besar_diskon'],0,',','.'); ?></h3>
                        <p class="text-xs font-bold opacity-80 uppercase">Potongan Harga</p>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded text-sm"><?php echo $row['nama_voucher']; ?></span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 h-10 overflow-hidden"><?php echo $row['deskripsi']; ?></p>
                        <div class="flex justify-end gap-2 text-sm border-t pt-2">
                            <button onclick='editVoucher(<?php echo json_encode($row); ?>)' class="text-blue-600 font-bold hover:underline">Edit</button>
                            <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="text-red-600 font-bold hover:underline">Hapus</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md p-6">
            <h3 class="font-bold text-lg mb-4">Tambah Voucher</h3>
            <form method="POST">
                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Voucher (Kode)</label>
                <input type="text" name="nama_voucher" required class="w-full border rounded p-2 mb-3 uppercase" placeholder="CONTOH: MERDEKA45">
                
                <label class="block text-xs font-bold text-gray-600 mb-1">Besar Diskon (Rupiah)</label>
                <input type="number" name="besar_diskon" required class="w-full border rounded p-2 mb-3" placeholder="Contoh: 5000">
                
                <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                <textarea name="deskripsi" required class="w-full border rounded p-2 mb-3"></textarea>
                
                <label class="block text-xs font-bold text-gray-600 mb-2">Warna</label>
                <select name="color_class" class="w-full border rounded p-2 mb-4">
                    <option value="bg-blue-500">Biru</option>
                    <option value="bg-green-500">Hijau</option>
                    <option value="bg-red-600">Merah</option>
                    <option value="bg-yellow-500">Kuning</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('addModal')" class="text-gray-500 px-4 py-2">Batal</button>
                    <button type="submit" name="add_voucher" class="bg-blue-600 text-white px-4 py-2 rounded font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md p-6">
            <h3 class="font-bold text-lg mb-4">Edit Voucher</h3>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                
                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Voucher (Kode)</label>
                <input type="text" name="nama_voucher" id="edit_nama" required class="w-full border rounded p-2 mb-3 uppercase">
                
                <label class="block text-xs font-bold text-gray-600 mb-1">Besar Diskon (Rupiah)</label>
                <input type="number" name="besar_diskon" id="edit_diskon" required class="w-full border rounded p-2 mb-3">
                
                <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="edit_desc" required class="w-full border rounded p-2 mb-3"></textarea>
                
                <label class="block text-xs font-bold text-gray-600 mb-2">Warna</label>
                <select name="color_class" id="edit_color" class="w-full border rounded p-2 mb-4">
                    <option value="bg-blue-500">Biru</option>
                    <option value="bg-green-500">Hijau</option>
                    <option value="bg-red-600">Merah</option>
                    <option value="bg-yellow-500">Kuning</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('editModal')" class="text-gray-500 px-4 py-2">Batal</button>
                    <button type="submit" name="edit_voucher" class="bg-yellow-500 text-white px-4 py-2 rounded font-bold">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        
        function editVoucher(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_nama').value = data.nama_voucher;
            document.getElementById('edit_diskon').value = data.besar_diskon;
            document.getElementById('edit_desc').value = data.deskripsi;
            document.getElementById('edit_color').value = data.color_class;
            openModal('editModal');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = `voucher_admin.php?delete=${id}`;
            });
        }
        
        <?php if(isset($_SESSION['swal_icon'])): ?>
            Swal.fire({ icon: '<?php echo $_SESSION['swal_icon']; ?>', title: '<?php echo $_SESSION['swal_title']; ?>', text: '<?php echo $_SESSION['swal_text']; ?>', timer: 2000, showConfirmButton: false });
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