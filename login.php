<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi database aktif

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil input dari form
    $username = $_POST['username']; // Jangan di-escape dulu untuk cek hardcode
    $password = $_POST['password'];

    // ------------------------------------------------------------------
    // SKENARIO 1: KHUSUS ADMIN (HARDCODED / BACKDOOR)
    // Ini menjamin "admin" & "admin123" SELALU BISA MASUK tanpa tergantung database
    // ------------------------------------------------------------------
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user'] = 'Administrator';
        $_SESSION['role'] = 'admin'; // PENTING: Role harus 'admin' sesuai admin.php
        $_SESSION['user_id'] = 0;    // ID 0 untuk admin pusat
        
        header("Location: admin.php"); // Langsung lempar ke admin.php
        exit;
    }

    // ------------------------------------------------------------------
    // SKENARIO 2: USER BIASA (CEK DATABASE)
    // ------------------------------------------------------------------
    
    // Baru kita escape string untuk keamanan database
    $safe_username = mysqli_real_escape_string($conn, $username);

    // Query cari user
    $query = "SELECT * FROM users WHERE username = '$safe_username' OR email = '$safe_username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi Password Database (Hash)
        if (password_verify($password, $row['password'])) {
            
            // Set Session User
            $_SESSION['user'] = $row['username'];
            $_SESSION['role'] = $row['role']; 
            $_SESSION['user_id'] = $row['id'];

            // Cek Role (Jika ada admin yang terdaftar di database juga)
            if ($row['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: home.php");
            }
            exit;

        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Akun tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IMAMART</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { lato: ['Lato', 'sans-serif'] } } } }
    </script>
</head>
<body class="font-lato bg-gray-50 h-screen flex items-center justify-center">

    <div class="w-full h-full flex overflow-hidden bg-white shadow-2xl">
        
        <div class="hidden md:flex md:w-1/2 bg-blue-600 relative flex-col justify-center items-center text-white p-12">
            <div class="absolute inset-0 overflow-hidden">
                <img src="https://i.pinimg.com/1200x/b1/5b/52/b15b52ed644cb8bf9933d79ba785a2d3.jpg" class="w-full h-full object-cover opacity-20 mix-blend-multiply">
            </div>
            <div class="relative z-10 text-center">
                <img src="LOGO_IMAMART.png" alt="Logo" class="h-16 w-auto mx-auto mb-6 brightness-0 invert">
                <h1 class="text-4xl font-black mb-4">Selamat Datang!</h1>
                <p class="text-blue-100 text-lg">Masuk sebagai Admin untuk mengelola toko, atau User untuk belanja.</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex flex-col justify-center px-8 md:px-16 py-12 relative">
            <div class="max-w-md mx-auto w-full">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Login</h2>
                <p class="text-gray-500 mb-8 text-sm">Silakan masukkan akun Anda.</p>

                <?php if($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm flex items-center animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>

                <form action="" method="POST">
                    
                    <!-- PERBAIKAN 1: Label dan Placeholder diganti Username / Email -->
                    <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Username / Email</label>
                        <input type="text" name="username" placeholder="Masukkan username atau email" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2 ml-1">
                            <label class="block text-gray-700 text-sm font-bold">Password</label>
                            <a href="lupa_password.php" class="text-xs text-blue-600 font-bold hover:underline">Lupa Password?</a>
                        </div>
                        <div class="relative">
                            <input type="password" id="input-password" name="password" placeholder="********" required class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 transition">
                            
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition">
                                <i id="eye-icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">Masuk</button>
                </form>
                
                <div class="mt-6 text-center text-xs text-gray-400 border p-2 rounded bg-gray-50">
                    <p>Untuk masuk ke halaman admin, silahkan masukkan username "admin" dan password "admin123"</p>
                </div>
                
                <p class="text-center text-sm text-gray-600 mt-4">
                    Belum punya akun? <a href="register.php" class="text-blue-600 font-bold hover:underline">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('input-password');
            const eyeIcon = document.getElementById('eye-icon');
            
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