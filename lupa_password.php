<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi database aktif

$error = "";
$success = "";
$step = 1; // Default menampilkan form pencarian akun
$user_id = null;
$username_found = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- LANGKAH 1: CARI AKUN (Berdasarkan Username / Email) ---
    if (isset($_POST['cari_akun'])) {
        $input = mysqli_real_escape_string($conn, $_POST['username_email']);
        
        // Cari di database
        $query = "SELECT id, username, fullname FROM users WHERE username = '$input' OR email = '$input'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $user_id = $row['id'];
            $username_found = $row['username'];
            $step = 2; // Pindah ke form ganti password
        } else {
            $error = "Akun tidak ditemukan! Pastikan username atau email sudah benar.";
        }
    }

    // --- LANGKAH 2: UPDATE PASSWORD BARU ---
    if (isset($_POST['reset_password'])) {
        $user_id = $_POST['user_id'];
        $username_found = $_POST['username_found']; // Simpan state username
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error = "Konfirmasi password tidak cocok!";
            $step = 2; // Tetap di form ganti password
        } elseif (strlen($new_password) < 6) {
            $error = "Password minimal 6 karakter!";
            $step = 2; // Tetap di form ganti password
        } else {
            // Enkripsi password baru
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update ke database
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
            if (mysqli_query($conn, $update_query)) {
                $success = "Password berhasil diubah! Silakan login menggunakan password baru Anda.";
                $step = 3; // Pindah ke tampilan sukses
            } else {
                $error = "Terjadi kesalahan sistem: " . mysqli_error($conn);
                $step = 2;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - IMAMART</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { lato: ['Lato', 'sans-serif'] } } } }
    </script>
</head>
<body class="font-lato bg-gray-50 h-screen flex items-center justify-center">

    <div class="w-full h-full flex overflow-hidden bg-white shadow-2xl">
        
        <!-- Sisi Kiri (Sama seperti Login) -->
        <div class="hidden md:flex md:w-1/2 bg-blue-600 relative flex-col justify-center items-center text-white p-12">
            <div class="absolute inset-0 overflow-hidden">
                <img src="https://i.pinimg.com/1200x/b1/5b/52/b15b52ed644cb8bf9933d79ba785a2d3.jpg" class="w-full h-full object-cover opacity-20 mix-blend-multiply">
            </div>
            <div class="relative z-10 text-center">
                <img src="LOGO_IMAMART.png" alt="Logo" class="h-16 w-auto mx-auto mb-6 brightness-0 invert">
                <h1 class="text-4xl font-black mb-4">Lupa Password?</h1>
                <p class="text-blue-100 text-lg">Jangan khawatir, kami akan membantu Anda memulihkan akses ke akun IMAMART Anda.</p>
            </div>
        </div>

        <!-- Sisi Kanan (Form) -->
        <div class="w-full md:w-1/2 flex flex-col justify-center px-8 md:px-16 py-12 relative">
            <div class="max-w-md mx-auto w-full">
                
                <?php if ($step == 1): ?>
                <!-- ================= LANGKAH 1: FORM CARI AKUN ================= -->
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Cari Akun</h2>
                <p class="text-gray-500 mb-8 text-sm">Masukkan Email atau Username Anda yang terdaftar.</p>

                <?php if($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm flex items-center animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Username / Email</label>
                        <input type="text" name="username_email" placeholder="Masukkan Username atau Email" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <button type="submit" name="cari_akun" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">Cari Akun Saya</button>
                </form>

                <?php elseif ($step == 2): ?>
                <!-- ================= LANGKAH 2: FORM PASSWORD BARU ================= -->
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Buat Password Baru</h2>
                <p class="text-gray-500 mb-8 text-sm">Akun ditemukan: <span class="font-bold text-blue-600">@<?php echo $username_found; ?></span></p>

                <?php if($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm flex items-center animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="username_found" value="<?php echo $username_found; ?>">
                    
                    <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="input-new-password" name="new_password" placeholder="Minimal 6 karakter" required class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                            <button type="button" onclick="togglePassword('input-new-password', 'eye-icon-new')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition">
                                <i id="eye-icon-new" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="input-confirm-password" name="confirm_password" placeholder="Ulangi password baru" required class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                            <button type="button" onclick="togglePassword('input-confirm-password', 'eye-icon-confirm')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition">
                                <i id="eye-icon-confirm" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" name="reset_password" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">Ubah Password</button>
                </form>

                <?php elseif ($step == 3): ?>
                <!-- ================= LANGKAH 3: SUKSES ================= -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Berhasil!</h2>
                    <p class="text-gray-500 mb-8 text-sm"><?php echo $success; ?></p>
                    <a href="login.php" class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">Kembali ke Halaman Login</a>
                </div>
                <?php endif; ?>

                <!-- Tombol Navigasi Bawah -->
                <?php if($step != 3): ?>
                <p class="text-center text-sm text-gray-600 mt-8">
                    Ingat password Anda? <a href="login.php" class="text-blue-600 font-bold hover:underline">Kembali ke Login</a>
                </p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>