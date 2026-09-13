<?php
session_start();
require 'koneksi.php'; // Pastikan file koneksi sudah benar

// --- 1. PROTEKSI KEAMANAN ---
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- AMBIL DATA ADMIN DARI DATABASE ---
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin' LIMIT 1");
$admin = mysqli_fetch_assoc($query_admin);

// --- 2. LOGIC CRUD ---

// A. TAMBAH PELANGGAN BARU
if (isset($_POST['add_user'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi Password
    $role = 'user'; // Paksa role jadi user

    // Cek duplikat username/email
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['swal_icon'] = "error";
        $_SESSION['swal_title'] = "Gagal!";
        $_SESSION['swal_text'] = "Username atau Email sudah terdaftar.";
    } else {
        $query = "INSERT INTO users (fullname, username, email, password, role) VALUES ('$fullname', '$username', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['swal_icon'] = "success";
            $_SESSION['swal_title'] = "Berhasil!";
            $_SESSION['swal_text'] = "Pelanggan baru ditambahkan.";
        } else {
            $_SESSION['swal_icon'] = "error";
            $_SESSION['swal_title'] = "Error";
            $_SESSION['swal_text'] = mysqli_error($conn);
        }
    }
    header("Location: pelanggan.php");
    exit;
}

// B. EDIT PELANGGAN
if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Logika Password: Jika kosong, pakai password lama. Jika diisi, update password baru.
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET fullname='$fullname', username='$username', email='$email', password='$password' WHERE id='$id'";
    } else {
        $query = "UPDATE users SET fullname='$fullname', username='$username', email='$email' WHERE id='$id'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['swal_icon'] = "success";
        $_SESSION['swal_title'] = "Diperbarui!";
        $_SESSION['swal_text'] = "Data pelanggan berhasil diupdate.";
    } else {
        $_SESSION['swal_icon'] = "error";
        $_SESSION['swal_title'] = "Gagal";
        $_SESSION['swal_text'] = mysqli_error($conn);
    }
    header("Location: pelanggan.php");
    exit;
}

// C. HAPUS PELANGGAN
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM users WHERE id='$id'")) {
        $_SESSION['swal_icon'] = "success";
        $_SESSION['swal_title'] = "Terhapus!";
        $_SESSION['swal_text'] = "Data pelanggan berhasil dihapus.";
    }
    header("Location: pelanggan.php");
    exit;
}

// --- 3. AMBIL DATA (READ) ---
// Hanya ambil yang role-nya 'user'
$query_users = "SELECT * FROM users WHERE role='user' ORDER BY created_at DESC";
$users = mysqli_query($conn, $query_users);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - IMAMART</title>
    
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
            
            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-700 rounded-xl text-white shadow-lg font-bold transition">
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
                <h2 class="text-xl font-bold text-gray-800">Data Pelanggan</h2>
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
                    <i class="fa-solid fa-user-plus"></i> Tambah Pelanggan
                </button>
                
                <div class="relative w-full sm:w-72">
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari Nama / Email..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm shadow-sm">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="userTable">
                        <thead>
                            <tr class="bg-gray-100 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 font-bold">Nama Lengkap</th>
                                <th class="p-4 font-bold">Kontak (Username / Email)</th>
                                <th class="p-4 font-bold">Terdaftar Sejak</th>
                                <th class="p-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if(mysqli_num_rows($users) == 0): ?>
                                <tr><td colspan="4" class="p-8 text-center text-gray-400">Belum ada data pelanggan.</td></tr>
                            <?php else: ?>
                                <?php while($row = mysqli_fetch_assoc($users)): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold">
                                                <?php echo strtoupper(substr($row['fullname'], 0, 1)); ?>
                                            </div>
                                            <span class="font-bold text-gray-800"><?php echo $row['fullname']; ?></span>
                                        </div>
                                    </td>
                                    
                                    <td class="p-4">
                                        <p class="font-bold text-gray-700">@<?php echo $row['username']; ?></p>
                                        <p class="text-xs text-gray-500"><i class="fa-solid fa-envelope mr-1"></i> <?php echo $row['email']; ?></p>
                                    </td>
                                    
                                    <td class="p-4 text-gray-500 text-xs">
                                        <?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                    
                                    <td class="p-4 text-right">
                                        <button onclick='editUser(<?php echo json_encode($row); ?>)' class="text-blue-500 hover:text-blue-700 px-2 transition" title="Edit">
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
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <form action="" method="POST">
                <div class="bg-blue-600 p-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Tambah Pelanggan Baru</h3>
                    <button type="button" onclick="closeModal('addModal')" class="hover:text-gray-200"><i class="fa-solid fa-times"></i></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="fullname" required class="w-full border rounded-lg p-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" required class="w-full border rounded-lg p-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full border rounded-lg p-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required class="w-full border rounded-lg p-2 focus:ring-blue-500 text-sm" placeholder="******">
                    </div>
                </div>
                <div class="p-4 bg-gray-50 text-right">
                    <button type="button" onclick="closeModal('addModal')" class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-200 rounded-lg mr-2">Batal</button>
                    <button type="submit" name="add_user" class="bg-blue-600 text-white font-bold px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <form action="" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="bg-yellow-500 p-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Edit Data Pelanggan</h3>
                    <button type="button" onclick="closeModal('editModal')" class="hover:text-gray-100"><i class="fa-solid fa-times"></i></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="fullname" id="edit_fullname" required class="w-full border rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="edit_username" required class="w-full border rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="edit_email" required class="w-full border rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="w-full border rounded-lg p-2 text-sm bg-gray-50" placeholder="Biarkan kosong jika tidak diubah">
                    </div>
                </div>
                <div class="p-4 bg-gray-50 text-right">
                    <button type="button" onclick="closeModal('editModal')" class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-200 rounded-lg mr-2">Batal</button>
                    <button type="submit" name="edit_user" class="bg-yellow-500 text-white font-bold px-4 py-2 rounded-lg hover:bg-yellow-600">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        function editUser(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_fullname').value = data.fullname;
            document.getElementById('edit_username').value = data.username;
            document.getElementById('edit_email').value = data.email;
            openModal('editModal');
        }

        // Search JavaScript Simple
        function searchTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toUpperCase();
            let table = document.getElementById("userTable");
            let tr = table.getElementsByTagName("tr");
            for (let i = 1; i < tr.length; i++) {
                let tdName = tr[i].getElementsByTagName("td")[0];
                let tdContact = tr[i].getElementsByTagName("td")[1];
                if (tdName || tdContact) {
                    let txtValue1 = tdName.textContent || tdName.innerText;
                    let txtValue2 = tdContact.textContent || tdContact.innerText;
                    if (txtValue1.toUpperCase().indexOf(filter) > -1 || txtValue2.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Pelanggan?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `pelanggan.php?delete=${id}`;
                }
            })
        }

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