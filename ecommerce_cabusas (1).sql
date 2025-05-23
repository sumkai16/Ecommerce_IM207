-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 04:11 PM
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
-- Database: `ecommerce_cabusas`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `session_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(27, 1, '683056589dc58', 1, 1, '2025-05-23 22:00:55', '2025-05-23 22:00:55'),
(28, 1, '683056589dc58', 2, 2, '2025-05-23 22:01:04', '2025-05-23 22:01:04'),
(29, 1, '683056589dc58', 3, 1, '2025-05-23 22:02:50', '2025-05-23 22:02:50'),
(30, 1, '683056589dc58', 4, 2, '2025-05-23 22:03:54', '2025-05-23 22:03:54');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `guest_address` text DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `email` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'COD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `guest_name`, `guest_phone`, `guest_address`, `total`, `created_at`, `updated_at`, `status_id`, `email`, `notes`, `shipping_fee`, `payment_method`) VALUES
(21, 1, 'Axcee Cabusas', '9914082061', 'Tunghaan Cebu', 499.00, '2025-05-23 20:39:58', '2025-05-23 20:39:58', 1, '', '', 0.00, 'COD'),
(22, 2, 'Axcee Cabusas', '9914082061', 'Tunghaan Cebu', 499.00, '2025-05-23 20:50:08', '2025-05-23 20:50:08', 1, 'axcee1@gmail.com', '', 0.00, 'COD'),
(23, 1, 'Axcee Cabusas', '9914082061', 'Tunghaan Cebu', 499.00, '2025-05-23 22:07:49', '2025-05-23 22:07:49', 1, 'axcee@gmail.com', '', 0.00, 'COD');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(21, 21, 2, 1, 499.00, 499.00),
(22, 22, 2, 1, 499.00, 499.00),
(23, 23, 2, 1, 499.00, 499.00);

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

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `name`) VALUES
(4, 'Delivered'),
(3, 'Out for Delivery'),
(1, 'Pending'),
(2, 'Processing');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `slug`, `image_path`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Wireless Bluetooth Headphones', 'Noise-cancelling over-ear headphones with 20-hour battery life.', 1599.00, 'wireless-bluetooth-headphones', 'uploads/Wireless Bluetooth Headphones.avif', 1, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(2, 'Smartphone Tripod Stand', 'Adjustable and portable tripod for smartphones and cameras.', 499.00, 'smartphone-tripod-stand', 'uploads/Smartphone Tripod Stand.jpg', 1, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(3, 'Men\'s Oversized T-Shirt', 'Soft cotton streetwear shirt with minimalist design.', 399.00, 'mens-oversized-tshirt', 'uploads/Men\'s Oversized T-Shirt.webp', 2, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(4, 'Ladies High-Waist Denim Pants', 'Stylish high-rise jeans for everyday comfort and fashion.', 799.00, 'ladies-high-waist-denim', 'uploads/Ladies High-Waist Denim Pants.webp', 2, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(5, 'LED Desk Lamp', 'Touch control dimmable LED lamp with USB charging port.', 649.00, 'led-desk-lamp', 'uploads/LED Desk Lamp.webp', 3, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(6, 'Memory Foam Pillow', 'Ergonomic neck support pillow for quality sleep.', 899.00, 'memory-foam-pillow', 'uploads/Memory Foam Pillow.webp', 3, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(7, 'Aloe Vera Soothing Gel', '100% natural aloe vera gel for skin hydration.', 250.00, 'aloe-vera-gel', 'uploads/Aloe Vera Soothing Gel.webp', 4, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(8, 'Electric Facial Cleanser', 'Waterproof facial cleansing brush for daily skincare.', 1150.00, 'electric-facial-cleanser', 'uploads/Electric Facial Cleanser.webp', 4, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(9, 'Adjustable Dumbbells Set', 'Pair of dumbbells with weight plates up to 20kg.', 2499.00, 'adjustable-dumbbells-set', 'uploads/Adjustable Dumbbells Set.webp', 5, '2025-05-23 18:28:16', '2025-05-23 18:28:16'),
(10, 'Running Shoes (Unisex)', 'Breathable and lightweight shoes for runners.', 1399.00, 'running-shoes-unisex', 'uploads/Running Shoes (Unisex).webp', 5, '2025-05-23 18:28:16', '2025-05-23 18:28:16');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`) VALUES
(7, 'Automotive'),
(6, 'Baby & Kids'),
(4, 'Beauty & Health'),
(10, 'Books & Stationery'),
(1, 'Electronics'),
(2, 'Fashion'),
(13, 'Furniture'),
(8, 'Groceries'),
(3, 'Home & Living'),
(14, 'Jewelry & Watches'),
(12, 'Mobile Accessories'),
(9, 'Pet Supplies'),
(5, 'Sports & Outdoors'),
(15, 'Tools & Hardware'),
(11, 'Toys & Games');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'axcee', 'axcee@gmail.com', '$2y$10$4c3b71OknR.57PCN/nFFyerXlEpMKfkvx2QeMBac/St7vNpX.r9IW', '2025-05-23 18:23:22', '2025-05-23 20:50:50', 1),
(2, 'axcee1', 'axcee1@gmail.com', '$2y$10$XFegcudsCrzSzt0ozps5F.TJN16U9oQ61t1YJVF410/BclEHiq5MS', '2025-05-23 20:49:37', '2025-05-23 20:49:37', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `address`, `phone`, `birthdate`) VALUES
(1, NULL, NULL, NULL),
(2, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `fk_role` (`role_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `order_statuses` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

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
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
