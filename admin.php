<?php
session_start();
require 'koneksi.php'; 

// --- 1. PROTEKSI KEAMANAN ---
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- 2. AMBIL DATA STATISTIK KARTU (UTAMA) ---
$stats = [];
$query_income = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status != 'Batal'");
$data_income = mysqli_fetch_assoc($query_income);
$stats['pendapatan'] = $data_income['total'] ?? 0;

$query_order = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
$stats['total_order'] = mysqli_fetch_assoc($query_order)['total'];

$query_user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
$stats['pelanggan'] = mysqli_fetch_assoc($query_user)['total'];

$query_product = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$stats['produk_aktif'] = mysqli_fetch_assoc($query_product)['total'];

// --- AMBIL DATA ADMIN DARI DATABASE ---
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin' LIMIT 1");
$admin = mysqli_fetch_assoc($query_admin);


// --- 3. DATA GRAFIK 1 & 2 (TIME SERIES) ---
$chart_labels = []; 
$chart_income = []; 
$chart_orders = []; 

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime($date));
    
    $q_inc = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE DATE(order_date) = '$date' AND status != 'Batal'");
    $row_inc = mysqli_fetch_assoc($q_inc);
    
    $q_ord = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(order_date) = '$date'");
    $row_ord = mysqli_fetch_assoc($q_ord);

    $chart_labels[] = $label;
    $chart_income[] = $row_inc['total'] ?? 0;
    $chart_orders[] = $row_ord['total'] ?? 0;
}

// --- 4. DATA GRAFIK 3: METODE PEMBAYARAN (PIE CHART) ---
$pay_labels = [];
$pay_data = [];
$q_pay = mysqli_query($conn, "SELECT payment_method, COUNT(*) as total FROM orders GROUP BY payment_method");
while($row = mysqli_fetch_assoc($q_pay)) {
    $pay_labels[] = strtoupper($row['payment_method']); // Biar huruf besar semua
    $pay_data[] = $row['total'];
}

// --- 5. DATA GRAFIK 4: STATUS PESANAN (DOUGHNUT CHART) ---
// Kita inisialisasi urutan biar warnanya konsisten
$status_counts = ['Pending' => 0, 'Lunas' => 0, 'Dikirim' => 0, 'Batal' => 0];
$q_stat = mysqli_query($conn, "SELECT status, COUNT(*) as total FROM orders GROUP BY status");
while($row = mysqli_fetch_assoc($q_stat)) {
    $status_counts[$row['status']] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - IMAMART</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            <a href="admin.php" class="flex items-center gap-3 px-4 py-3 bg-blue-700 rounded-xl text-white shadow-lg font-bold transition">
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
                <h2 class="text-xl font-bold text-gray-800">Analisis Toko</h2>
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-2xl"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div>
                        <p class="text-gray-500 text-sm font-bold">Total Pendapatan</p>
                        <h3 class="text-2xl font-black text-gray-800">Rp <?php echo number_format($stats['pendapatan'], 0, ',', '.'); ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl"><i class="fa-solid fa-cart-flatbed"></i></div>
                    <div>
                        <p class="text-gray-500 text-sm font-bold">Total Pesanan</p>
                        <h3 class="text-2xl font-black text-gray-800"><?php echo $stats['total_order']; ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-2xl"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <p class="text-gray-500 text-sm font-bold">Pelanggan</p>
                        <h3 class="text-2xl font-black text-gray-800"><?php echo $stats['pelanggan']; ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-2xl"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <p class="text-gray-500 text-sm font-bold">Produk Aktif</p>
                        <h3 class="text-2xl font-black text-gray-800"><?php echo $stats['produk_aktif']; ?></h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Analisis Pendapatan</h3>
                            <p class="text-xs text-gray-500">Grafik pemasukan 7 hari terakhir</p>
                        </div>
                        <div class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-calendar-days mr-1"></i> Minggu Ini</div>
                    </div>
                    <div class="relative h-72">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                     <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Traffic Order</h3>
                            <p class="text-xs text-gray-500">Jumlah pesanan masuk</p>
                        </div>
                    </div>
                    <div class="relative h-72">
                         <canvas id="orderChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Metode Pembayaran</h3>
                    <p class="text-xs text-gray-500 mb-6">Preferensi pembayaran pelanggan</p>
                    <div class="h-64 flex justify-center">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Status Pesanan</h3>
                    <p class="text-xs text-gray-500 mb-6">Distribusi status transaksi keseluruhan</p>
                    <div class="h-64 flex justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

            </div>

            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden flex items-center justify-between">
                <div class="relative z-10">
                    <h3 class="font-bold text-xl mb-2">Kelola Produk & Stok</h3>
                    <p class="text-blue-100 text-sm mb-4 max-w-lg">Pastikan stok produk selalu tersedia untuk pelanggan setia Anda.</p>
                    <a href="data_produk.php" class="inline-block bg-white text-blue-800 px-6 py-2.5 rounded-lg font-bold text-sm shadow hover:bg-gray-100 transition">
                        <i class="fa-solid fa-boxes-stacked mr-2"></i> Atur Produk
                    </a>
                </div>
                <i class="fa-solid fa-shop absolute -right-6 -bottom-8 text-[10rem] text-white opacity-10"></i>
            </div>

        </div>
    </main>

    <script>
        // --- DATA CHART 1 & 2 (TIME SERIES) ---
        const labels = <?php echo json_encode($chart_labels); ?>;
        const incomeData = <?php echo json_encode($chart_income); ?>;
        const orderData = <?php echo json_encode($chart_orders); ?>;

        new Chart(document.getElementById('incomeChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: incomeData,
                    borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3, tension: 0.4, fill: true, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#2563eb'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] }, ticks: { callback: (val) => 'Rp ' + val.toLocaleString('id-ID') } },
                    x: { grid: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('orderChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'Order', data: orderData, backgroundColor: '#10b981', borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { borderDash: [2, 4] } }, x: { grid: { display: false } } }
            }
        });

        // --- DATA CHART 3: PAYMENT METHOD (PIE) ---
        const payLabels = <?php echo json_encode($pay_labels); ?>;
        const payData = <?php echo json_encode($pay_data); ?>;

        new Chart(document.getElementById('paymentChart'), {
            type: 'pie',
            data: {
                labels: payLabels,
                datasets: [{
                    data: payData,
                    backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444', '#a855f7'], // Hijau, Biru, Kuning, Merah, Ungu
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 10 } } } }
            }
        });

        // --- DATA CHART 4: STATUS ORDER (DOUGHNUT) ---
        const statusData = [
            <?php echo $status_counts['Pending']; ?>,
            <?php echo $status_counts['Lunas']; ?>,
            <?php echo $status_counts['Dikirim']; ?>,
            <?php echo $status_counts['Batal']; ?>
        ];

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Lunas', 'Dikirim', 'Batal'],
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#facc15', '#22c55e', '#3b82f6', '#ef4444'], // Kuning, Hijau, Biru, Merah
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '70%', // Lubang tengah
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 10 } } } }
            }
        });
    </script>
    <script>
        function toggleAdminSidebar() {
            document.getElementById('admin-sidebar').classList.toggle('-translate-x-full');
        }
    </script>
</body>
</html>