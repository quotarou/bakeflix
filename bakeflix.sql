-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2026 at 07:33 AM
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
-- Database: `bakeflix`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`) VALUES
(1, 'admin', '$6$bakeflixsalt$PcOvRC8fwWGHZA2LspAOJNE67GbLQaFPGX40MTuyTG0UddqjG76B7I4lMLq0Xje1kQc/gRzxwQlBrd.NTdz.N0');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `slug`, `name`, `icon`) VALUES
(1, 'flours-sugars', 'Flours & Sugars', '🌾'),
(2, 'tools-pans', 'Baking Tools & Pans', '🥣'),
(3, 'decorating', 'Decorating Supplies', '🎂'),
(4, 'extracts', 'Extracts & Flavors', '🍦'),
(5, 'packaging', 'Packaging', '📦');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `billing_address` text NOT NULL,
  `postal_code` char(6) NOT NULL,
  `payment_method` varchar(30) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Paid',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `sku` varchar(40) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `allergens` text DEFAULT NULL,
  `storage` text DEFAULT NULL,
  `icon` varchar(10) NOT NULL,
  `added_at` date NOT NULL,
  `discontinued` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `brand`, `unit`, `price`, `stock`, `sku`, `description`, `ingredients`, `allergens`, `storage`, `icon`, `added_at`, `discontinued`, `image`) VALUES
(1, 1, 'All-Purpose Flour', 'Golden Mill', '1 kg', 60.00, 40, 'FLR-AP-1KG', 'Fine all-purpose flour for cakes, cookies and breads.', 'Refined wheat flour (maida).', 'Contains gluten (wheat). May contain traces of soy.', 'Store in a cool, dry place in an airtight container. Best before 6 months from packing.', '🌾', '2026-10-01', 0, 'all-purpose-flour.jpg'),
(2, 1, 'Caster Sugar', 'SweetCrystal', '500 g', 55.00, 25, 'SUG-CST-500G', 'Superfine sugar that dissolves quickly in batters.', 'Refined sugar.', 'None known.', 'Keep dry and tightly sealed. Shelf life 24 months.', '🍚', '2026-10-05', 0, 'caster-sugar.jpg'),
(3, 1, 'Cocoa Powder', 'ChocoPure', '200 g', 220.00, 0, 'COC-PWD-200G', 'Unsweetened dark cocoa powder.', 'Alkalised cocoa powder.', 'May contain traces of milk and nuts.', 'Store in a cool, dry place away from strong odours. Shelf life 18 months.', '🍫', '2026-09-20', 0, 'cocoa-powder.jpg'),
(4, 2, 'Round Cake Pan', 'BakePro', '8 inch', 350.00, 12, 'PAN-RND-8IN', 'Non-stick aluminium round cake pan.', 'Aluminium body with non-stick coating.', 'Not applicable.', 'Hand wash and dry fully before storing. No expiry.', '🥧', '2026-10-10', 0, 'round-cake-pan.jpg'),
(5, 2, 'Silicone Spatula Set', 'KitchenKing', '3 pieces', 299.00, 30, 'SPT-SIL-3PC', 'Heat-resistant spatulas in three sizes.', 'Food-grade silicone with stainless steel core.', 'Not applicable.', 'Dishwasher safe. Heat resistant up to 220 C. No expiry.', '🥄', '2026-09-15', 0, 'silicone-spatula-set.jpg'),
(6, 2, 'Rolling Pin', 'BakePro', '1 piece', 250.00, 8, 'RLP-WD-1PC', 'Smooth hardwood rolling pin.', 'Solid beechwood with food-safe finish.', 'Not applicable.', 'Wipe clean, do not soak. Keep dry. No expiry.', '🪵', '2026-10-20', 0, 'rolling-pin.jpg'),
(7, 3, 'Rainbow Sprinkles', 'SprinkleJoy', '100 g', 120.00, 50, 'SPR-RNB-100G', 'Colourful sprinkles for cakes and cupcakes.', 'Sugar, starch, vegetable oil, food colours, glazing agent.', 'May contain traces of milk and soy.', 'Store below 25 C away from sunlight. Shelf life 12 months.', '🌈', '2026-10-12', 0, 'rainbow-sprinkles.jpg'),
(8, 3, 'Piping Bag Set', 'CakeCraft', '12 pieces', 180.00, 18, 'PIP-SET-12PC', 'Reusable piping bags with 6 nozzles.', 'Food-grade reusable polyester bags and stainless steel nozzles.', 'Not applicable.', 'Wash after use and air dry. No expiry.', '🎨', '2026-09-28', 0, 'piping-bag-set.jpg'),
(9, 4, 'Vanilla Extract', 'Aroma Co.', '50 ml', 199.00, 22, 'EXT-VAN-50ML', 'Pure vanilla extract for rich flavour.', 'Water, alcohol, vanilla bean extract.', 'None known.', 'Store in a cool, dark place with the cap closed tightly. Shelf life 36 months.', '🌼', '2026-10-02', 0, 'vanilla-extract.jpg'),
(10, 4, 'Strawberry Essence', 'Aroma Co.', '30 ml', 90.00, 0, 'EXT-STR-30ML', 'Fruity essence for icings and cakes.', 'Water, propylene glycol, strawberry flavouring.', 'None known.', 'Store below 25 C. Shelf life 18 months.', '🍓', '2026-08-30', 0, 'strawberry-essence.jpg'),
(11, 5, 'Cake Box (Pack of 10)', 'PackIt', '10 boxes', 450.00, 15, 'PKG-CBX-10PK', 'Sturdy white boxes for 1 kg cakes.', 'Food-grade white paperboard.', 'Not applicable.', 'Keep flat in a dry place. No expiry.', '📦', '2026-10-18', 0, 'cake-box.jpg'),
(12, 5, 'Cupcake Liners', 'PackIt', '100 pieces', 80.00, 60, 'PKG-CLN-100', 'Greaseproof paper liners.', 'Greaseproof paper.', 'Not applicable.', 'Keep dry. No expiry.', '🧁', '2026-10-25', 0, 'cupcake-liners.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `serviceable_areas`
--

CREATE TABLE `serviceable_areas` (
  `postal_code` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `serviceable_areas`
--

INSERT INTO `serviceable_areas` (`postal_code`) VALUES
('110001'),
('400001'),
('560001'),
('600001'),
('700001'),
('700002'),
('700003'),
('700004'),
('700005'),
('700006'),
('700007'),
('700008'),
('700009'),
('700010'),
('700019'),
('700020'),
('700029'),
('700034'),
('700054'),
('700091');

--
-- Indexes for dumped tables
--

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
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `serviceable_areas`
--
ALTER TABLE `serviceable_areas`
  ADD PRIMARY KEY (`postal_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
