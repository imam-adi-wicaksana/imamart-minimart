<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi benar

// --- 1. PROTEKSI KEAMANAN ---
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- AMBIL DATA ADMIN DARI DATABASE ---
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin' LIMIT 1");
$admin = mysqli_fetch_assoc($query_admin);

// --- 2. LOGIC UPDATE STATUS & HAPUS ---

// A. Update Status (Via POST Form)
if (isset($_POST['update_status_order'])) {
    $id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $query = "UPDATE orders SET status='$new_status' WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['swal_icon'] = "success";
        $_SESSION['swal_title'] = "Status Diperbarui!";
        $_SESSION['swal_text'] = "Pesanan sekarang berstatus: $new_status";
    } else {
        $_SESSION['swal_icon'] = "error";
        $_SESSION['swal_title'] = "Gagal!";
        $_SESSION['swal_text'] = mysqli_error($conn);
    }
    // Redirect agar tidak resubmit saat refresh
    header("Location: pesanan_masuk.php");
    exit;
}

// B. Hapus Pesanan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM orders WHERE id='$id'")) {
        $_SESSION['swal_icon'] = "success";
        $_SESSION['swal_title'] = "Terhapus!";
        $_SESSION['swal_text'] = "Data pesanan berhasil dihapus.";
    }
    header("Location: pesanan_masuk.php");
    exit;
}

// --- 3. AMBIL DATA PESANAN (SEARCH & FILTER) ---
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

$sql = "SELECT * FROM orders WHERE 1=1";

if ($search) {
    $sql .= " AND (invoice_number LIKE '%$search%' OR customer_name LIKE '%$search%')";
}
if ($status_filter) {
    $sql .= " AND status = '$status_filter'";
}

$sql .= " ORDER BY order_date DESC"; // Urutkan dari yang terbaru
$orders = mysqli_query($conn, $sql);

// Helper function untuk warna status badge
function getStatusColor($status) {
    switch ($status) {
        case 'Lunas': return 'bg-green-100 text-green-700 border-green-200';
        case 'Pending': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case 'Dikirim': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'Batal': return 'bg-red-100 text-red-700 border-red-200';
        default: return 'bg-gray-100 text-gray-700';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - IMAMART</title>
    
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
            <a href="pesanan_masuk.php" class="flex items-center gap-3 px-4 py-3 bg-blue-700 rounded-xl text-white shadow-lg font-bold transition">
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
                <h2 class="text-xl font-bold text-gray-800">Daftar Pesanan</h2>
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
            
            <form action="" method="GET" class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex gap-2 w-full sm:w-auto">
                    <select name="status_filter" onchange="this.form.submit()" class="border rounded-lg px-4 py-2 text-sm focus:ring-blue-500 cursor-pointer bg-white shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="Pending" <?php echo ($status_filter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Lunas" <?php echo ($status_filter == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                        <option value="Dikirim" <?php echo ($status_filter == 'Dikirim') ? 'selected' : ''; ?>>Dikirim</option>
                        <option value="Batal" <?php echo ($status_filter == 'Batal') ? 'selected' : ''; ?>>Batal</option>
                    </select>
                </div>
                
                <div class="relative w-full sm:w-72">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Invoice / Nama Customer..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm shadow-sm">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
            </form>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 font-bold">Invoice / Tanggal</th>
                                <th class="p-4 font-bold">Customer</th>
                                <th class="p-4 font-bold">Total & Metode</th>
                                <th class="p-4 font-bold">Status Pesanan</th>
                                <th class="p-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if(mysqli_num_rows($orders) == 0): ?>
                                <tr><td colspan="5" class="p-8 text-center text-gray-400">Belum ada pesanan masuk.</td></tr>
                            <?php else: ?>
                                <?php while($row = mysqli_fetch_assoc($orders)): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    
                                    <td class="p-4">
                                        <p class="font-bold text-blue-600 font-mono"><?php echo $row['invoice_number']; ?></p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="fa-regular fa-calendar mr-1"></i>
                                            <?php echo date('d M Y, H:i', strtotime($row['order_date'])); ?>
                                        </p>
                                    </td>
                                    
                                    <td class="p-4">
                                        <div class="font-bold text-gray-800"><?php echo $row['customer_name']; ?></div>
                                        <div class="text-xs text-gray-500">ID User: <?php echo $row['user_id']; ?></div>
                                    </td>
                                    
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Rp <?php echo number_format($row['total_amount'], 0, ',', '.'); ?></p>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200 inline-block mt-1">
                                            <?php echo $row['payment_method']; ?>
                                        </span>
                                    </td>
                                    
                                    <td class="p-4">
                                        <form action="" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="update_status_order" value="1">
                                            
                                            <select name="status" onchange="this.form.submit()" 
                                                class="text-xs font-bold uppercase py-1 px-3 rounded-full border-2 cursor-pointer focus:outline-none transition <?php echo getStatusColor($row['status']); ?>">
                                                <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Lunas" <?php echo ($row['status'] == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                                                <option value="Dikirim" <?php echo ($row['status'] == 'Dikirim') ? 'selected' : ''; ?>>Dikirim</option>
                                                <option value="Batal" <?php echo ($row['status'] == 'Batal') ? 'selected' : ''; ?>>Batal</option>
                                            </select>
                                        </form>
                                    </td>
                                    
                                    <td class="p-4 text-right">
                                        <button onclick='showDetail(<?php echo json_encode($row); ?>)' class="text-blue-500 hover:text-blue-700 px-2 transition" title="Lihat Detail">
                                            <i class="fa-solid fa-eye text-lg"></i>
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

    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform transition-all scale-100 overflow-hidden">
            <div class="bg-blue-600 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-receipt mr-2"></i> Detail Pesanan</h3>
                <button onclick="closeModal('detailModal')" class="hover:text-gray-200"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="text-center mb-6">
                    <p class="text-gray-500 text-sm">Invoice Number</p>
                    <h2 id="modal_invoice" class="text-2xl font-mono font-bold text-blue-600">INV-00000</h2>
                    <span id="modal_status" class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-700">STATUS</span>
                </div>

                <div class="space-y-3 text-sm border-t border-b border-gray-100 py-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama Pelanggan</span>
                        <span class="font-bold text-gray-800" id="modal_customer">Nama</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tanggal Order</span>
                        <span class="font-bold text-gray-800" id="modal_date">Date</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metode Bayar</span>
                        <span class="font-bold text-gray-800" id="modal_payment">Payment</span>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <span class="font-bold text-gray-600 text-lg">Total Bayar</span>
                    <span class="font-black text-blue-600 text-2xl" id="modal_total">Rp 0</span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 text-right">
                <button onclick="closeModal('detailModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Tutup</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Modal Logic
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function showDetail(data) {
            document.getElementById('modal_invoice').innerText = data.invoice_number;
            document.getElementById('modal_customer').innerText = data.customer_name;
            document.getElementById('modal_payment').innerText = data.payment_method;
            document.getElementById('modal_date').innerText = new Date(data.order_date).toLocaleString('id-ID');
            document.getElementById('modal_total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.total_amount);
            
            // Set Status Badge di Modal
            const statusEl = document.getElementById('modal_status');
            statusEl.innerText = data.status;
            statusEl.className = 'inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold ';
            
            if(data.status === 'Lunas') statusEl.classList.add('bg-green-100', 'text-green-700');
            else if(data.status === 'Pending') statusEl.classList.add('bg-yellow-100', 'text-yellow-700');
            else if(data.status === 'Dikirim') statusEl.classList.add('bg-blue-100', 'text-blue-700');
            else statusEl.classList.add('bg-red-100', 'text-red-700');

            document.getElementById('detailModal').classList.remove('hidden');
        }

        // Konfirmasi Hapus
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Pesanan?',
                text: "Data pesanan akan hilang permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `pesanan_masuk.php?delete=${id}`;
                }
            })
        }

        // Notifikasi PHP SweetAlert
        <?php if(isset($_SESSION['swal_icon'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['swal_icon']; ?>',
                title: '<?php echo $_SESSION['swal_title']; ?>',
                text: '<?php echo $_SESSION['swal_text']; ?>',
                timer: 2000,
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