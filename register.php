<?php
session_start();
require 'koneksi.php'; // Koneksi Database

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah username/email sudah ada di database
        $check = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username' OR email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username atau Email sudah terdaftar!";
        } else {
            // Enkripsi Password agar aman
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // Default daftar sebagai user biasa

            // Insert ke Database
            $sql = "INSERT INTO users (fullname, username, email, password, role) VALUES ('$fullname', '$username', '$email', '$hashed_password', '$role')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Akun berhasil dibuat! Silakan login.";
            } else {
                $error = "Terjadi kesalahan sistem: " . mysqli_error($conn);
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
    <title>Daftar Akun - IMAMART</title>
    
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
<body class="font-lato bg-gray-50 h-screen flex items-center justify-center">

    <div class="w-full h-full flex overflow-hidden bg-white shadow-2xl">
        
        <!-- Sisi Kiri (Sama seperti Login & Lupa Password) -->
        <div class="hidden md:flex md:w-1/2 bg-blue-600 relative flex-col justify-center items-center text-white p-12">
            <div class="absolute inset-0 overflow-hidden">
                <img src="https://i.pinimg.com/1200x/b1/5b/52/b15b52ed644cb8bf9933d79ba785a2d3.jpg" class="w-full h-full object-cover opacity-20 mix-blend-multiply">
            </div>
            <div class="relative z-10 text-center">
                <img src="LOGO_IMAMART.png" alt="Logo" class="h-16 w-auto mx-auto mb-6 brightness-0 invert">
                <h1 class="text-4xl font-black mb-4">Gabung Sekarang!</h1>
                <p class="text-blue-100 text-lg mb-8">Nikmati promo eksklusif, voucher gratis ongkir, dan kemudahan belanja harian hanya untuk member IMAMART.</p>
                
                <div class="space-y-3 text-left bg-blue-700/50 p-6 rounded-xl border border-blue-500/30 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-yellow-400"></i>
                        <span class="text-sm font-bold">Voucher Pengguna Baru</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-yellow-400"></i>
                        <span class="text-sm font-bold">Gratis Ongkir Tiap Hari</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-yellow-400"></i>
                        <span class="text-sm font-bold">Poin Reward Belanja</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan (Form) -->
        <div class="w-full md:w-1/2 flex flex-col justify-center px-8 md:px-16 py-12 relative overflow-y-auto">
            <div class="max-w-md mx-auto w-full">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Buat Akun Baru</h2>
                <p class="text-gray-500 mb-8 text-sm">Lengkapi data diri Anda untuk memulai.</p>

                <?php if($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm flex items-center animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>

                <?php if($success): ?>
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Registrasi Berhasil!</h2>
                    <p class="text-gray-500 mb-8 text-sm"><?php echo $success; ?></p>
                    <a href="login.php" class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">Login Sekarang</a>
                </div>
                <?php else: ?>

                <form action="" method="POST">
                    
                    <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="fullname" placeholder="Contoh: Budi Santoso" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Username</label>
                            <input type="text" name="username" placeholder="Username" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Email</label>
                            <input type="email" name="email" placeholder="email@anda.com" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Password</label>
                            <div class="relative">
                                <input type="password" id="input-password" name="password" placeholder="Minimal 6 karakter" required
                                       class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                                <button type="button" onclick="togglePassword('input-password', 'eye-icon-pass')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition">
                                    <i id="eye-icon-pass" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" id="input-confirm-password" name="confirm_password" placeholder="Ulangi password" required
                                       class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                                <button type="button" onclick="togglePassword('input-confirm-password', 'eye-icon-confirm')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition">
                                    <i id="eye-icon-confirm" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center text-sm text-gray-500">
                            <input type="checkbox" required class="mr-2 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            Saya menyetujui <a href="#" class="text-blue-600 font-bold hover:underline ml-1">Syarat & Ketentuan</a>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                        Daftar Sekarang
                    </button>
                </form>

                <p class="text-center text-sm text-gray-600 mt-4">
                    Sudah punya akun? <a href="login.php" class="text-blue-600 font-bold hover:underline">Masuk di sini</a>
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