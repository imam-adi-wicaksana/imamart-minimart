-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 11:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_imamart`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `total_amount` decimal(10,0) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('Pending','Lunas','Dikirim','Batal') DEFAULT 'Pending',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `invoice_number`, `user_id`, `customer_name`, `total_amount`, `payment_method`, `status`, `order_date`) VALUES
(1, 'INV-1768016018', 1, 'adwmam29', 18000, 'TUNAI', 'Lunas', '2026-01-10 10:33:38'),
(2, 'INV-1768085370', 3, 'goatmessi', 24000, 'TUNAI', 'Lunas', '2026-01-11 05:49:30'),
(3, 'INV-1768085659', 1, 'adwmam29', 7300, 'TUNAI', 'Dikirim', '2026-01-11 05:54:19'),
(4, 'INV-1768188829', 1, 'adwmam29', 3000, 'TUNAI', 'Lunas', '2026-01-12 10:33:49'),
(5, 'INV-1768189145', 1, 'adwmam29', 38000, 'TUNAI', 'Pending', '2026-01-12 10:39:05'),
(6, 'INV-1768215022', 1, 'adwmam29', 21000, 'TUNAI', 'Pending', '2026-01-12 17:50:22'),
(7, 'INV-1768215040', 1, 'adwmam29', 15000, 'banking', 'Lunas', '2026-01-12 17:50:40'),
(8, 'INV-1770353035', 1, 'adwmam29', 11000, 'Banking', 'Lunas', '2026-02-06 11:43:55');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `old_price` decimal(10,0) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `promo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `old_price`, `image`, `promo`, `created_at`) VALUES
(1, 'Apel Jawa Timur', 'buah_sayur', 8000, 12000, '1768015640_apel.jpg', 'Diskon Hari Raya', '2026-01-10 03:27:20'),
(2, 'Good Time Cookies', 'makanan', 7000, NULL, '1768015679_good time cookies.jpg', '', '2026-01-10 03:27:59'),
(3, 'Air Putih Le Minerale', 'minuman', 3000, NULL, '1768015738_le minerale.jpg', '', '2026-01-10 03:28:58'),
(4, 'Minyak Goreng Minyakita', 'sembako', 14000, NULL, '1768015759_minyak goreng minyakita.jpeg', '', '2026-01-10 03:29:19'),
(5, 'Nastar Khas Magelang', 'makanan', 15000, NULL, '1768015804_nastar.jpg', '', '2026-01-10 03:30:04'),
(6, 'Tisu Nice', 'kebutuhan_rumah', 8000, NULL, '1768015840_Nice Tissue.jpg', '', '2026-01-10 03:30:40'),
(7, 'Roti Tawar Borobudur', 'makanan', 7000, 5000, '1768015870_roti tawar borobudur.jpg', 'Diskon Pabrik', '2026-01-10 03:31:10'),
(8, 'Sabun Cuci Piring Sunlight', 'kebutuhan_rumah', 6000, NULL, '1768015893_sunlight cuci piring.jpg', '', '2026-01-10 03:31:33'),
(9, 'Wortel', 'buah_sayur', 4000, NULL, '1768015914_wortel.jpg', '', '2026-01-10 03:31:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Imam Adi Wicaksana', 'adwmam29', 'imamadiwicaksana29@gmail.com', '$2y$10$n4oradGdQhsPiFuQgtjen.4RN2jLdADAtJtQZ9x3mKVhTMyJSHiOG', 'user', '2026-01-10 03:32:18'),
(2, 'Administrator Utama', 'admin', 'admin@imamart.com', '$2y$10$H7o/..hash_untuk_admin123_..', 'admin', '2026-01-10 03:48:14'),
(3, 'Leo Messi', 'goatmessi', 'goat@gmail.com', '$2y$10$G/3eEcAr.S38WQ4bBffsWORzPl3TZ5Q1HyJhvU7pJ0H67HrthQcC2', 'user', '2026-01-10 22:48:51'),
(4, 'Mikachaw', 'mikha', 'mikha@gmail.com', '$2y$10$aAjiuP42OOo3YhlaPfafQeMcpvTUEsChkjUuqOqeJfnZ/vRUCUFx.', 'user', '2026-01-12 10:52:50');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `nama_voucher` varchar(50) NOT NULL,
  `besar_diskon` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `color_class` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `nama_voucher`, `besar_diskon`, `deskripsi`, `color_class`) VALUES
(1, 'DISKON5000', 5000, 'Potongan harga Rp 5.000 untuk semua produk.', 'bg-blue-500'),
(2, 'HEMAT10K', 10000, 'Belanja makin hemat dengan potongan Rp 10.000.', 'bg-green-500'),
(3, 'LEBARAN2026', 15000, 'Lebaran penuh dengan kemenangan dengan diskon sebesar Rp 15.000 :)', 'bg-red-600');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_voucher` (`nama_voucher`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
