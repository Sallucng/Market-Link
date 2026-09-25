-- ==========================================================
-- MarketLink Database Dump (eGreen Basket)
-- Generated: 2026-09-24 04:40:32
-- Compatible with: MySQL 5.7+ / MariaDB / phpMyAdmin
-- ==========================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','farmer','admin') NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('1', 'admin', 'System Administrator', 'admin@marketlink.local', '+1 (555) 019-2831', 'MarketLink HQ, Suite 400', 'admin', '1', '1', NULL, '$2y$12$EX9kfqiUwwCzzAjqycajaO4g.Et1SfcIoKTsKYxyLZ3sLtgb8dpx6', NULL, '2026-09-23 13:50:20', '2026-09-23 13:50:20');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('2', 'greenvalley', 'Johnathan Green', 'farmer@marketlink.local', '+1 (555) 349-1122', 'Green Valley Farm, Route 9', 'farmer', '1', '1', NULL, '$2y$12$LZ5QrRIg.HnVib8jRRrR4exD5Jbh16xpfqVsaNPsnhcczkqL4kd6W', NULL, '2026-09-23 13:50:21', '2026-09-23 13:50:21');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('3', 'sunshineorchard', 'Elena Rodriguez', 'orchard@marketlink.local', '+1 (555) 887-4321', '142 Orchard Lane, Hill Country', 'farmer', '1', '1', NULL, '$2y$12$sBTDatvXQnucmDkg6qcwJuH9DledjXMKa46uRV8pHY4Sq5Ahp5Boq', NULL, '2026-09-23 13:50:21', '2026-09-23 13:50:21');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('4', 'newharvest', 'Marcus Vance', 'newharvest@marketlink.local', '+1 (555) 672-9900', '88 Greenway Shed, South District', 'farmer', '1', '0', NULL, '$2y$12$vU0I7LHkh4875IISEDyL/Oaw8cQfXkCKsNz2k/.yEyIlWyFvTWAaC', NULL, '2026-09-23 13:50:21', '2026-09-23 13:50:21');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('5', 'sarah_shopper', 'Sarah Shopper', 'customer@marketlink.local', '+1 (555) 234-5678', '742 Evergreen Terrace, Apt 4B', 'customer', '1', '1', NULL, '$2y$12$DjcJh.2MnvG725FZSCW7e.KupwhJQBBicL0dREA/jJ1flrvOJbVYm', NULL, '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('6', 'david_miller', 'David Miller', 'david@marketlink.local', '+1 (555) 987-6543', '12 Maplewood Drive', 'customer', '1', '1', NULL, '$2y$12$KIUOegiv39vVeWrdzYlJuOQ1tsWG61jV9x8ky2MjeNulokE0cQRBe', NULL, '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `markets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `markets`;
CREATE TABLE `markets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL DEFAULT 'Metropolis',
  `operating_days` varchar(100) NOT NULL,
  `timings` varchar(50) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `map_provider` varchar(30) NOT NULL DEFAULT 'OpenStreetMap',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `markets`
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`) VALUES ('1', 'Downtown Farmers Plaza', '100 Central Square, Downtown', 'Metropolis', 'Saturday, Sunday', '08:00 AM - 02:00 PM', '40.712776', '-74.005974', 'OpenStreetMap', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`) VALUES ('2', 'Riverside Green & Artisan Market', '450 Harbor Boulevard, Waterfront', 'Metropolis', 'Wednesday, Saturday', '09:00 AM - 03:00 PM', '40.7258', '-74.0112', 'OpenStreetMap', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`) VALUES ('3', 'Oak Valley Community Harvest Fair', '1200 Parkside Avenue, Oak Valley', 'Metropolis', 'Sunday', '07:30 AM - 01:30 PM', '40.702', '-73.992', 'OpenStreetMap', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `farmers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `farmers`;
CREATE TABLE `farmers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `market_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stall_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `operating_days` varchar(100) DEFAULT NULL,
  `pickup_time_windows` text DEFAULT NULL,
  `cutoff_hours` int(11) NOT NULL DEFAULT 2,
  `bio` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farmers_user_id_foreign` (`user_id`),
  KEY `farmers_market_id_foreign` (`market_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `farmers`
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('1', '2', '1', 'Green Valley Organic Produce', 'Johnathan Green', '+1 (555) 349-1122', 'Stall #12, North Corridor, Downtown Plaza', '40.71295', '-74.00585', 'Saturday, Sunday', '08:30 AM - 10:30 AM, 11:00 AM - 01:00 PM', '3', 'Family-owned certified sustainable produce farm cultivating heirloom tomatoes, crunchy leafy greens, and root vegetables fresh from the soil.', 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('2', '3', '2', 'Sunshine Orchards & Wildflower Apiary', 'Elena Rodriguez', '+1 (555) 887-4321', 'Pier 4 Canopy Stall #4B, Riverside', '40.72592', '-74.01105', 'Wednesday, Saturday', '09:30 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Heritage orchard specializing in crisp seasonal apples, sun-ripened berries, pure raw wildflower honey, and fresh-pressed cider.', 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('3', '4', '3', 'New Harvest Urban Greens', 'Marcus Vance', '+1 (555) 672-9900', 'Pending Stall Allocation', '40.7021', '-73.9919', 'Sunday', '08:00 AM - 10:00 AM', '2', 'Urban micro-farm producing nutrient-dense microgreens, edible flowers, and artisanal culinary herbs.', 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL UNIQUE,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `categories`
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`, `updated_at`) VALUES ('1', 'Fresh Vegetables', 'vegetables', 'Crisp, garden-picked leafy greens, roots, tomatoes, and seasonal vegetables.', 'bi-carrot', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`, `updated_at`) VALUES ('2', 'Orchard Fruits', 'fruits', 'Tree-ripened seasonal apples, berries, peaches, and citrus.', 'bi-apple', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`, `updated_at`) VALUES ('3', 'Farm Fresh Dairy & Eggs', 'dairy-eggs', 'Free-range pasture-raised eggs, artisanal cheese, and raw butter.', 'bi-egg-fried', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`, `updated_at`) VALUES ('4', 'Artisanal Baked Goods', 'baked-goods', 'Stone-milled sourdough loaves, rustic baguettes, and country pies.', 'bi-basket', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`, `updated_at`) VALUES ('5', 'Herbs & Farm Honey', 'herbs-honey', 'Aromatic kitchen herbs, teas, and unpasteurized raw honey.', 'bi-flower1', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `products`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `farmer_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(20) NOT NULL DEFAULT 'kg',
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `weekly_recurring_stock` int(11) NOT NULL DEFAULT 0,
  `is_sold_out` tinyint(1) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_farmer_id_foreign` (`farmer_id`),
  KEY `products_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `products`
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('1', '1', '1', 'Heirloom Vine Tomatoes', 'Sweet, juicy multi-colored heirloom tomatoes harvested at peak ripeness.', '4.5', 'kg', '35', '50', '0', '1', 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('2', '1', '1', 'Organic Tuscan Kale & Chard Bundle', 'Deep green, pesticide-free kale bundled fresh with rainbow chard.', '3.25', 'bunch', '20', '30', '0', '1', 'https://images.unsplash.com/photo-1524179091875-bf99a9a6fa57?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('3', '1', '1', 'Sweet Japanese Sweet Potatoes', 'Creamy, purple-skinned white-fleshed sweet potatoes freshly dug.', '3.8', 'kg', '40', '40', '0', '1', 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('4', '1', '3', 'Pasture-Raised Organic Brown Eggs', 'Rich golden yolks from foraging, free-roaming Rhode Island hens.', '6', 'dozen', '15', '25', '0', '1', 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('5', '2', '2', 'Honeycrisp Crisp Apples', 'Extra crunchy and explosive sweetness, orchard-picked yesterday.', '4.9', 'kg', '50', '60', '0', '1', 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('6', '2', '5', 'Raw Wildflower Honeycomb Jar', 'Pure unpasteurized honey bottled straight from our riverside hives.', '9.5', 'jar (500g)', '18', '20', '0', '1', 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('7', '2', '4', 'Rustic Country Sourdough Batard', 'Naturally fermented 36-hour sourdough with deep caramel blistered crust.', '6.5', 'loaf', '12', '15', '0', '1', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `farmer_id` bigint(20) UNSIGNED NOT NULL,
  `market_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `order_status` enum('placed','accepted','ready_for_pickup','completed','cancelled','declined') NOT NULL DEFAULT 'placed',
  `pickup_date` date NOT NULL,
  `pickup_time_slot` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'pay_at_pickup',
  `cutoff_time` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_farmer_id_foreign` (`farmer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `orders`
INSERT INTO `orders` (`id`, `customer_id`, `farmer_id`, `market_id`, `order_number`, `order_status`, `pickup_date`, `pickup_time_slot`, `total_amount`, `payment_method`, `cutoff_time`, `notes`, `created_at`, `updated_at`) VALUES ('1', '5', '1', '1', 'ML-0998C705', 'completed', '2026-09-20 00:00:00', '09:00 AM - 10:00 AM', '15.25', 'pay_at_pickup', '2026-09-20 06:00:00', 'Please include extra ripe tomatoes if possible.', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `orders` (`id`, `customer_id`, `farmer_id`, `market_id`, `order_number`, `order_status`, `pickup_date`, `pickup_time_slot`, `total_amount`, `payment_method`, `cutoff_time`, `notes`, `created_at`, `updated_at`) VALUES ('2', '5', '2', '2', 'ML-89BDA746', 'ready_for_pickup', '2026-09-24 00:00:00', '10:00 AM - 11:30 AM', '19.3', 'pay_at_pickup', '2026-09-24 07:30:00', 'I will be coming around 10:15 AM.', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `order_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `order_items`
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '2', '4.5', '9', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES ('2', '1', '2', '1', '3.25', '3.25', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES ('3', '1', '3', '1', '3.8', '3.8', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES ('4', '2', '5', '2', '4.9', '9.8', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES ('5', '2', '6', '1', '9.5', '9.5', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `reviews`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `farmer_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `farmer_response` text DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `reviews`
INSERT INTO `reviews` (`id`, `order_id`, `customer_id`, `farmer_id`, `product_id`, `rating`, `comment`, `farmer_response`, `responded_at`, `created_at`, `updated_at`) VALUES ('1', '1', '5', '1', '1', '5', 'The heirloom tomatoes were the sweetest I have ever tasted! Pickup at the stall was super smooth.', 'Thank you so much Sarah! Glad you loved this week’s harvest.', '2026-09-21 13:50:22', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `favorites`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` enum('farmer','product','market') NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_unique` (`customer_id`,`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'order',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `notifications`
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`, `updated_at`) VALUES ('1', '5', 'Pre-Order Ready for Pickup!', 'Your pre-order #ML-89BDA746 is packed and ready for pickup at Sunshine Orchards stall in Riverside Market.', 'order', '0', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

-- --------------------------------------------------------
-- Table structure for `announcements`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `badge_type` varchar(50) NOT NULL DEFAULT 'info',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `announcements`
INSERT INTO `announcements` (`id`, `created_by`, `title`, `content`, `badge_type`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'Welcome to MarketLink — eGreen Basket Edition!', 'Support local growers, reserve fresh harvest in advance, and pick up directly at your neighborhood market stalls. Remember: all pre-orders are settled in person at pickup.', 'success', '1', '2026-09-23 13:50:22', '2026-09-23 13:50:22');

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
