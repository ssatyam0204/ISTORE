-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 01:29 PM
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
-- Database: `istore`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_line` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `state` varchar(100) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_line`, `city`, `pincode`, `state`, `is_default`) VALUES
(7, 1, 'subashnagr', 'maharashtra', '40093', 'mumbai', 0),
(9, 1, 'subashnagr', 'maharashtra', '40093', 'mumbai', 0),
(10, 1, 'subashnagr', 'maharashtra', '40093', 'mumbai', 0),
(12, 2, 'subashnagr', 'maharashtra', '400093', 'mumbai', 0),
(13, 2, 'subashnagr', 'maharashtra', '400093', 'mumbai', 0),
(14, 2, 'subashnagr', 'maharashtra', '400093', 'mumbai', 0),
(15, 1, 'subashnagar', 'maharashtra', '400093', 'mumbai', 0),
(16, 1, 'subashnagar', 'maharashtra', '400093', 'mumbai', 0),
(17, 1, 'subashnagar', 'maharashtra', '400093', 'mumbai', 0);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`, `reset_token`, `token_expiry`) VALUES
(1, 'admin', 'your mail', '$2y$10$Yg96jy76LZ4As0i5S2SzP.XjWFfPHI2tKaIo.CYgg3NT1lB2pPXdq', '2025-09-02 10:54:24', '142787', '2025-09-07 16:34:16');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(2, 'iPhone', '2025-09-08 10:26:47'),
(3, 'iPad', '2025-09-08 10:27:17'),
(4, 'Mac', '2025-09-08 10:27:22'),
(5, 'AirPods', '2025-09-08 10:28:08'),
(6, 'Accessories', '2025-09-08 10:28:36'),
(7, 'Watch', '2025-09-08 10:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Open','Closed') NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `coupon_code`, `discount_type`, `discount_value`, `expiry_date`, `is_active`) VALUES
(2, 'RISHABH2007', 'fixed', 3500.00, '2025-09-07', 1),
(3, '11233333', 'percentage', 50.00, '2025-09-07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `address_id`, `total_amount`, `payment_method`, `payment_id`, `order_status`, `order_date`) VALUES
(1, 1, 16, 57599.00, 'Razorpay', 'pay_RRmwiGi7A0jWpi', 'Delivered', '2025-10-10 13:35:02'),
(2, 1, 17, 57599.00, 'Razorpay', 'pay_RRmxSEMSyUwj6t', 'Pending', '2025-10-10 13:35:45');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(7, 7, 8, 2, 100000.00),
(8, 8, 8, 1, 100000.00),
(9, 9, 8, 1, 100000.00),
(10, 1, 8, 1, 100000.00),
(11, 1, 7, 1, 57549.00),
(12, 2, 7, 1, 57549.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'placeholder.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category_id`, `stock`, `image`, `created_at`) VALUES
(2, 'iPhone 16', 'iPhone 16 128 GB: 5G Mobile Phone with Camera Control, A18 Chip and a Big Boost in Battery Life. Works with AirPods; Black(in box :- a USB‑C Charge Cable, SIM ejector tool (non-U.S. models only) and some documentation)', 70000.00, 2, 25, '1757328785_1.jpg', '2025-09-08 10:53:05'),
(3, 'iPhone 16', 'iPhone 16 256 GB: 5G Mobile Phone with Camera Control, A18 Chip and a Big Boost in Battery Life. Works with AirPods; Teal(in box :- a USB‑C Charge Cable, SIM ejector tool (non-U.S. models only) and some documentation)\r\n', 90000.00, 2, 25, '1757329047_2.jpg', '2025-09-08 10:57:27'),
(4, 'iPhone 16', 'iPhone 16 512 GB: 5G Mobile Phone with Camera Control, A18 Chip and a Big Boost in Battery Life. Works with AirPods; White(in box :- a USB‑C Charge Cable, SIM ejector tool (non-U.S. models only) and some documentation)', 1000000.00, 2, 25, '1757329131_3.jpg', '2025-09-08 10:58:51'),
(5, 'iPad Pro 11', 'Apple iPad Pro 11″ (M4): Ultra Retina XDR Display, 256GB, Landscape 12MP Front Camera / 12MP Back Camera, LiDAR Scanner, Wi-Fi 6E, Face ID, All-Day Battery...(in box :-a USB-C charge cable, and a power adapter and some documents)\r\n', 95999.00, 3, 50, '1758341081_4.jpg', '2025-09-20 04:04:41'),
(6, 'iPad 11', 'Apple iPad 11″: A16 chip, 27.69 cm (11″) Model, Liquid Retina Display, 128GB, Wi-Fi 6, 12MP Front/12MP Back Camera, Touch ID, All-Day Battery Life — Blue(in box :-a USB-C charge cable, and a power adapter and some documents)\r\n\r\n', 33999.00, 3, 20, '1758341190_5.jpg', '2025-09-20 04:06:30'),
(7, 'iPad Air 11', 'Apple iPad Air 11″ with M3 chip: Built for Apple Intelligence, Liquid Retina Display, 128GB, 12MP Front/Back Camera, Wi-Fi 6E, Touch ID, All-Day Battery Life — Space Gray(in box :-a USB-C charge cable, and a power adapter and some documents)\r\n\r\n', 57549.00, 3, 7, '1758341283_6.jpg', '2025-09-20 04:08:03'),
(8, 'iPhone 17 Pro Max', 'iPhone 17 Pro Max 512 GB: 17.42 cm (6.9″) Display with Promotion, A19 Pro Chip, Best Battery Life in Any iPhone Ever, Pro Fusion Camera System, Center Stage Front Camera; Cosmic Orange(in box :- a USB‑C Charge Cable, SIM ejector tool (non-U.S. models only) and some documentation)\r\n', 100000.00, 2, 6, '1758341460_7.jpg', '2025-09-20 04:11:00'),
(9, 'Apple 2025 MacBook Air', 'Apple 2025 MacBook Air (13-inch, Apple M4 chip with 10-core CPU and 8-core GPU, 16GB Unified Memory, 256GB) - Sky Blue\r\nWhat is in the box?\r\n13-inch MacBook Air\r\nUSB-C to MagSafe 3 Cable (2m)\r\nUSB-C Power Adapter', 99900.00, 4, 50, '1760264313_m1.jpg', '2025-10-12 10:18:33'),
(10, 'Apple 2024 MacBook Pro', '\r\nApple 2024 MacBook Pro Laptop with M4 chip with 10‑core CPU and 10‑core GPU: Built for Apple Intelligence, (14.2″) Liquid Retina XDR Display, 16GB Unified...\r\n\r\nWhat is in the box?\r\n14-inch MacBook Pro 1N\r\nUSB-C to MagSafe 3 Cable (2m) 1N\r\nUSB-C Power Adapter', 159990.00, 4, 10, '1760264473_m2.jpg', '2025-10-12 10:21:13'),
(11, 'Apple AirPods Pro 3', 'Apple AirPods Pro 3 Wireless Earbuds, Active Noise Cancellation, Live Translation, Heart Rate Sensing, Bluetooth Headphones, Spatial Audio, High-Fidelity Sound, USB-C Charging\r\n\r\nWhat is in the box?\r\nAirPods Pro 3\r\nMagSafe Charging Case (USB-C) with speaker and lanyard loop\r\nSilicone ear tips (five sizes: XXS, XS, S, M, L)\r\nAirPods Pro 3 do not include a USB-C Charge Cable or power adapter.', 25900.00, 5, 15, '1760264698_a1.jpg', '2025-10-12 10:24:58'),
(12, 'Apple AirPods 4', 'Apple AirPods 4 Wireless Earbuds, Bluetooth Headphones, Personalised Spatial Audio, Sweat and Water Resistant, USB-C Charging Case, H2 Chip, Up to 30 Hours...\r\n\r\nWhat is in the box?\r\nAirPods 4\r\nCharging Case (USB‑C)\r\nDocumentation\r\n(USB-C Charge Cable sold separately)', 11999.00, 5, 30, '1760264792_a2.jpg', '2025-10-12 10:26:32'),
(13, ' Apple Watch Series 11', 'Apple Watch Series 11 GPS 46mm Jet Black Aluminium Case with Black Sport Band - M/L\r\n\r\nWhat is in the box?\r\nCase\r\nBand\r\n1m Magnetic Charging Cable', 49399.00, 7, 20, '1760264929_w1.jpg', '2025-10-12 10:28:49'),
(14, 'Apple Watch SE 3', 'Apple Watch SE 3 GPS 40mm Midnight Aluminium Case with Midnight Sport Band - M/L\r\n\r\nWhat is in the box?\r\nCase\r\nBand\r\n1m Magnetic Charging Cable', 25900.00, 7, 10, '1760265019_w2.jpg', '2025-10-12 10:30:19'),
(15, 'DailyObjects POP 45W Wall Charger Adapter', 'DailyObjects POP 45W Wall Charger Adapter with Foldable Pins|USB-C Dual Port Power Output|Fast Charging|GaN5|Slim|Support iPhone 12 13 14 15 16 pro Max, Android,ipad,Samsung,oneplus|Lightweight-Blue\r\n\r\nWhat is in the box?\r\nUser Manual', 2499.00, 6, 52, '1760265157_x1.jpg', '2025-10-12 10:32:37'),
(16, 'New Apple AirTag', 'helps you find personal items like keys, bags, and wallets using the Find My network', 2799.00, 6, 21, '1760265286_x2.jpg', '2025-10-12 10:34:46'),
(17, 'Comfyable Laptop Sleeve', 'Comfyable Laptop Sleeve Compatible with 16 Inch MacBook Pro M4 M3 M2 & M1 2021-2019 & 15 Inch MacBook Air (Loose Fit) M4 2025 M3 M2, Slim Protective PU Leather Bag Waterproof Case for Mac, Brown\r\n', 2203.00, 6, 43, '1760265357_x3.jpg', '2025-10-12 10:35:57'),
(18, 'amazon basics Power Bank ', 'amazon basics 10000mAh 20W Fast Charging Power Bank and 15W Wireless Output | Type C Power Delivery (Input & Output) Quick Charge| Two Way Fast Charge | Made in India (Black)\r\n', 1249.00, 6, 21, '1760265448_x4.jpg', '2025-10-12 10:37:28');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `review_text`, `image`, `is_approved`, `created_at`) VALUES
(5, 8, 2, 4, 'Good product', '', 1, '2025-10-07 05:46:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `mobile`, `address`, `is_blocked`, `created_at`, `reset_token`, `token_expiry`) VALUES
(1, 'Satyam', 's.satyam0204@gmail.com', '$2y$10$siBPYG5uVfn29d/X.aHyyOydyOB0PUUe/ASgpaBqoJzngoSmERMhe', NULL, NULL, 0, '2025-10-10 13:02:33', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_code` (`coupon_code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product` (`user_id`,`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
