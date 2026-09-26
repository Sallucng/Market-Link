-- ==========================================================
-- MarketLink Database Dump (eGreen Basket)
-- Generated: 2026-09-26 05:37:42
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
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('4', 'newharvest', 'Marcus Vance', 'newharvest@marketlink.local', '+1 (555) 672-9900', '88 Greenway Shed, South District', 'farmer', '1', '1', NULL, '$2y$12$vU0I7LHkh4875IISEDyL/Oaw8cQfXkCKsNz2k/.yEyIlWyFvTWAaC', NULL, '2026-09-23 13:50:21', '2026-09-24 13:41:02');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('5', 'sarah_shopper', 'Sarah Shopper', 'customer@marketlink.local', '+1 (555) 234-5678', '742 Evergreen Terrace, Apt 4B', 'customer', '1', '1', NULL, '$2y$12$DjcJh.2MnvG725FZSCW7e.KupwhJQBBicL0dREA/jJ1flrvOJbVYm', NULL, '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('6', 'david_miller', 'David Miller', 'david@marketlink.local', '+1 (555) 987-6543', '12 Maplewood Drive', 'customer', '1', '1', NULL, '$2y$12$KIUOegiv39vVeWrdzYlJuOQ1tsWG61jV9x8ky2MjeNulokE0cQRBe', NULL, '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('7', 'highland_growers', 'Clara Hensley', 'highland_growers@marketlink.local', '+1 (555) 207-4300', '850 Highland Avenue, North Ridge', 'farmer', '1', '1', NULL, '$2y$12$PB4JCByXBzCYk2WsokjY4eRu0d4r1.vWzzKfMn6pX26Ir03ecTwuq', NULL, '2026-09-25 09:48:58', '2026-09-25 09:48:58');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('8', 'sunset_orchards', 'Mateo Silva', 'sunset_orchards@marketlink.local', '+1 (555) 450-1949', '220 Marina Promenade, Sunset Bay', 'farmer', '1', '1', NULL, '$2y$12$z1W2IF.Lon1Yu3ukLesrUuh6eNzNprSJZxneN.NlwlpoSiK5uC0JW', NULL, '2026-09-25 09:48:59', '2026-09-25 09:48:59');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('9', 'midtown_cheese_co', 'Beatrice Dupont', 'midtown_cheese_co@marketlink.local', '+1 (555) 827-6592', '610 Lexington Way, Historic District', 'farmer', '1', '1', NULL, '$2y$12$6suXZQY8wPI4KyoSFddRJuXGTo.WXnVbEHbFyq3wMbLhaDlqXAfOC', NULL, '2026-09-25 09:48:59', '2026-09-25 09:48:59');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('10', 'cedar_creek_roots', 'Lucas Sterling', 'cedar_creek_roots@marketlink.local', '+1 (555) 345-5515', '140 Cedar Valley Road', 'farmer', '1', '1', NULL, '$2y$12$GSW1u1QCzE7bhlaUC59NmuPw/EZGyeAxXerrZuOLYbUHVpvPgBdzq', NULL, '2026-09-25 09:49:00', '2026-09-25 09:49:00');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('11', 'pinecrest_apiaries', 'Hannah Lindqvist', 'pinecrest_apiaries@marketlink.local', '+1 (555) 694-1708', '90 Forestview Boulevard', 'farmer', '1', '1', NULL, '$2y$12$NVZSRJBdQRjUHXofcTNCYOabMf880gpp7SeNDQuh7Z8hh090EOs..', NULL, '2026-09-25 09:49:00', '2026-09-25 09:49:00');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('12', 'eastside_greens', 'Tariq Al-Mansoor', 'eastside_greens@marketlink.local', '+1 (555) 610-6922', '340 East Boulevard, Green Commons', 'farmer', '1', '1', NULL, '$2y$12$gOaX2.07hPhfnDIVlwU2kO1Q.mn0LC8IlUAqfJbUbu3neE1XQTT86', NULL, '2026-09-25 09:49:01', '2026-09-25 09:49:01');
INSERT INTO `users` (`id`, `username`, `name`, `email`, `contact_number`, `address`, `role`, `is_active`, `is_approved`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('13', 'golden_ridge_farms', 'Evelyn Brooks', 'golden_ridge_farms@marketlink.local', '+1 (555) 619-5055', '1050 Foothill Parkway', 'farmer', '1', '1', NULL, '$2y$12$ltndkR9CJ7fldiNxzgieKOHrW4pWW03lvYMN7SKJzDq53X2uY.qlu', NULL, '2026-09-25 09:49:01', '2026-09-25 09:49:01');

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
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('1', 'Downtown Farmers Plaza', '100 Central Square, Downtown', 'Metropolis', 'Saturday, Sunday', '08:00 AM - 02:00 PM', '40.712776', '-74.005974', 'OpenStreetMap', '2026-09-23 13:50:22', '2026-09-25 09:48:58', 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80', 'Metropolis\'s premier weekend farmers market gathering over 20 regional growers, artisanal bakers, and organic family farms.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('2', 'Riverside Green and Artisan Market', '450 Harbor Boulevard, Waterfront', 'Metropolis', 'Wednesday, Saturday', '09:00 AM - 03:00 PM', '40.7258', '-74.0112', 'OpenStreetMap', '2026-09-23 13:50:22', '2026-09-25 09:48:58', 'https://images.unsplash.com/photo-1533900298318-6b8da08a523e?auto=format&fit=crop&w=800&q=80', 'Scenic waterfront open-air pavilion showcasing fresh coastal orchards, apiaries, heirloom berries, and organic seasonal harvests.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('3', 'Oak Valley Community Harvest Fair', '1200 Parkside Avenue, Oak Valley', 'Metropolis', 'Sunday', '07:30 AM - 01:30 PM', '40.702', '-73.992', 'OpenStreetMap', '2026-09-23 13:50:22', '2026-09-25 09:48:58', 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80', 'A family-friendly Sunday harvest celebration in the heart of Oak Valley parkland with hydroponic greens, heritage poultry, and fresh dairy.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('4', 'Highland Park Organic Exchange', '850 Highland Avenue, North Ridge', 'Metropolis', 'Saturday', '08:00 AM - 01:30 PM', '40.738', '-73.985', 'OpenStreetMap', '2026-09-25 09:48:58', '2026-09-25 09:48:58', 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=800&q=80', 'Dedicated 100% certified organic marketplace featuring cold-pressed juices, wild-foraged mushrooms, and sustainable regenerative root vegetables.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('5', 'Sunset Bay Harbor Market', '220 Marina Promenade, Sunset Bay', 'Waterfront', 'Friday, Saturday', '09:00 AM - 02:00 PM', '40.718', '-74.02', 'OpenStreetMap', '2026-09-25 09:48:59', '2026-09-25 09:48:59', 'https://images.unsplash.com/photo-1506484381205-f7945653044d?auto=format&fit=crop&w=800&q=80', 'Vibrant coastal market where maritime growers bring greenhouse greens, sea-salt roasted nuts, sourdough loaves, and freshly harvested tree fruits.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('6', 'Midtown Heritage Growers Shed', '610 Lexington Way, Historic District', 'Midtown', 'Thursday, Sunday', '08:30 AM - 02:30 PM', '40.75', '-73.975', 'OpenStreetMap', '2026-09-25 09:48:59', '2026-09-25 09:48:59', 'https://images.unsplash.com/photo-1516594798947-e65505dbb29d?auto=format&fit=crop&w=800&q=80', 'Historic covered shed hosting multi-generational family growers with antique apple varieties, artisan farmhouse cheeses, and seasonal preserves.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('7', 'Cedar Creek Agri-Market', '140 Cedar Valley Road', 'Cedar Valley', 'Tuesday, Saturday', '08:00 AM - 01:00 PM', '40.705', '-74.015', 'OpenStreetMap', '2026-09-25 09:48:59', '2026-09-25 09:48:59', 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=800&q=80', 'Nestled alongside Cedar Creek, this rustic market offers sun-grown stone fruits, free-range eggs, pasture-raised beef, and fragrant kitchen herbs.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('8', 'Pinecrest Eco Farmers Forum', '90 Forestview Boulevard', 'Pinecrest', 'Saturday, Sunday', '07:30 AM - 01:00 PM', '40.73', '-73.965', 'OpenStreetMap', '2026-09-25 09:49:00', '2026-09-25 09:49:00', 'https://images.unsplash.com/photo-1573246123716-6b1782bfc499?auto=format&fit=crop&w=800&q=80', 'Eco-forward zero-waste community market where local agroecologists share organic microgreens, cold-stored winter squash, and raw clover honey.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('9', 'Eastside Community Fresh Market', '340 East Boulevard, Green Commons', 'East Metropolis', 'Wednesday, Sunday', '09:00 AM - 03:00 PM', '40.715', '-73.96', 'OpenStreetMap', '2026-09-25 09:49:00', '2026-09-25 09:49:00', 'https://images.unsplash.com/photo-1526399232581-2ab5608b6336?auto=format&fit=crop&w=800&q=80', 'A bustling neighborhood hub with multicultural street produce, heirloom brassicas, heirloom tomatoes, and freshly pressed cider.');
INSERT INTO `markets` (`id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`, `image_url`, `description`) VALUES ('10', 'Golden Ridge Harvest Commons', '1050 Foothill Parkway', 'Golden Ridge', 'Sunday', '08:00 AM - 02:00 PM', '40.742', '-74.03', 'OpenStreetMap', '2026-09-25 09:49:01', '2026-09-25 09:49:01', 'https://images.unsplash.com/photo-1471193945509-9ad0617afabf?auto=format&fit=crop&w=800&q=80', 'Picturesque valley overlook market featuring organic root vegetables, field pumpkins, sun-dried fruit, and fresh farm pantry staples.');

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
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('4', '7', '4', 'Highland Valley Micro-Farm', 'Clara Hensley', '+1 (555) 207-4300', 'Stall #9, Highland Park Organic Exchange', '40.7382', '-73.9848', 'Saturday', '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Regenerative grower focused on antioxidant-rich rainbow chard, purple carrots, and organic brassicas.', 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 09:48:59');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('5', '8', '5', 'Sunset Bay Citrus & Berries', 'Mateo Silva', '+1 (555) 450-1949', 'Stall #12, Sunset Bay Harbor Market', '40.7182', '-74.0198', 'Friday, Saturday', '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Coastal orchard yielding sweet golden raspberries, Meyer lemons, and cold-pressed citrus preserves.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 09:48:59');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('6', '9', '6', 'Heritage Pastures Dairy & Pantry', 'Beatrice Dupont', '+1 (555) 827-6592', 'Stall #15, Midtown Heritage Growers Shed', '40.7502', '-73.9748', 'Thursday, Sunday', '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Artisanal grass-fed goat cheese, cultured pasture butter, and farmstead organic yogurt.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 09:48:59');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('7', '10', '7', 'Cedar Creek Root & Herb Co.', 'Lucas Sterling', '+1 (555) 345-5515', 'Stall #21, Cedar Creek Agri-Market', '40.7052', '-74.0148', 'Tuesday, Saturday', '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Hand-harvested culinary herbs, fresh ginger, horseradish, and heritage heirloom garlic varieties.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:00', '2026-09-25 09:49:00');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('8', '11', '8', 'Pinecrest Forest Honey & Wax', 'Hannah Lindqvist', '+1 (555) 694-1708', 'Stall #3, Pinecrest Eco Farmers Forum', '40.7302', '-73.9648', 'Saturday, Sunday', '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Treatment-free sustainable beekeeping delivering raw basswood honey, bee pollen, and beeswax food wraps.', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:00', '2026-09-25 09:49:00');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('9', '12', '9', 'Eastside Urban Aquaponics', 'Tariq Al-Mansoor', '+1 (555) 610-6922', 'Stall #18, Eastside Community Fresh Market', '40.7152', '-73.9598', 'Wednesday, Sunday', '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Hyper-fresh living butterhead lettuce, micro basil, and crisp watercress harvested morning of market.', 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:01', '2026-09-25 09:49:01');
INSERT INTO `farmers` (`id`, `user_id`, `market_id`, `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows`, `cutoff_hours`, `bio`, `image_url`, `created_at`, `updated_at`) VALUES ('10', '13', '10', 'Golden Ridge Heritage Farm', 'Evelyn Brooks', '+1 (555) 619-5055', 'Stall #6, Golden Ridge Harvest Commons', '40.7422', '-74.0298', 'Sunday', '09:00 AM - 11:30 AM, 12:00 PM - 02:00 PM', '2', 'Dry-farmed heirloom winter squash, pie pumpkins, crisp Asian pears, and freshly roasted squash seeds.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:01', '2026-09-25 09:49:01');

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
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('1', '1', '1', 'Heirloom Vine Tomatoes', 'Sweet, juicy multi-colored heirloom tomatoes harvested at peak ripeness.', '4.5', 'kg', '35', '50', '0', '1', 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-24 13:40:32');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('2', '1', '1', 'Organic Tuscan Kale & Chard Bundle', 'Deep green, pesticide-free kale bundled fresh with rainbow chard.', '3.25', 'bunch', '20', '30', '0', '1', 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-25 05:14:47');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('3', '1', '1', 'Sweet Japanese Sweet Potatoes', 'Creamy, purple-skinned white-fleshed sweet potatoes freshly dug.', '3.8', 'kg', '40', '40', '0', '1', 'https://plus.unsplash.com/premium_photo-1675365780148-a00379c54123?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('4', '1', '3', 'Pasture-Raised Organic Brown Eggs', 'Rich golden yolks from foraging, free-roaming Rhode Island hens.', '6', 'dozen', '15', '25', '0', '1', 'https://images.unsplash.com/photo-1506976785307-8732e854ad03?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('5', '2', '2', 'Honeycrisp Crisp Apples', 'Extra crunchy and explosive sweetness, orchard-picked yesterday.', '4.9', 'kg', '50', '60', '0', '1', 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('6', '2', '5', 'Raw Wildflower Honeycomb Jar', 'Pure unpasteurized honey bottled straight from our riverside hives.', '9.5', 'jar (500g)', '18', '20', '0', '1', 'https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('7', '2', '4', 'Rustic Country Sourdough Batard', 'Naturally fermented 36-hour sourdough with deep caramel blistered crust.', '6.5', 'loaf', '12', '15', '0', '1', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80', '2026-09-23 13:50:22', '2026-09-23 13:50:22');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('8', '4', '1', 'Rainbow Swiss Chard', 'Freshly harvested, locally cultivated rainbow swiss chard from Highland Valley Micro-Farm.', '3.75', 'bunch', '38', '34', '0', '1', 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 10:32:19');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('9', '4', '1', 'Organic Purple Carrots', 'Freshly harvested, locally cultivated organic purple carrots from Highland Valley Micro-Farm.', '4.2', 'lb', '47', '26', '0', '1', 'https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 10:32:19');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('10', '5', '2', 'Meyer Lemons', 'Freshly harvested, locally cultivated meyer lemons from Sunset Bay Citrus & Berries.', '5', 'bag', '45', '28', '0', '1', 'https://images.unsplash.com/photo-1590502593747-42a996133562?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 10:32:19');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('11', '5', '2', 'Golden Raspberries', 'Freshly harvested, locally cultivated golden raspberries from Sunset Bay Citrus & Berries.', '6.5', 'pint', '37', '40', '0', '1', 'https://images.unsplash.com/photo-1577069861033-55d04cec4ef5?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 10:32:19');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('12', '6', '1', 'Farmstead Chèvre Goat Cheese', 'Freshly harvested, locally cultivated farmstead chèvre goat cheese from Heritage Pastures Dairy & Pantry.', '8.5', 'wheel', '24', '35', '0', '1', 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 10:32:20');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('13', '6', '1', 'Cultured Herb Butter', 'Freshly harvested, locally cultivated cultured herb butter from Heritage Pastures Dairy & Pantry.', '6', 'block', '42', '37', '0', '1', 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:48:59', '2026-09-25 10:32:20');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('14', '7', '5', 'Heirloom Hardneck Garlic', 'Freshly harvested, locally cultivated heirloom hardneck garlic from Cedar Creek Root & Herb Co..', '3.5', 'braid', '30', '40', '0', '1', 'https://images.unsplash.com/photo-1540148426945-6cf22a6b2383?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:00', '2026-09-25 10:32:20');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('15', '7', '5', 'Fresh Rosemary & Thyme Bundle', 'Freshly harvested, locally cultivated fresh rosemary & thyme bundle from Cedar Creek Root & Herb Co..', '2.8', 'bunch', '24', '26', '0', '1', 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:00', '2026-09-25 10:32:20');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('16', '8', '1', 'Raw Forest Basswood Honey', 'Freshly harvested, locally cultivated raw forest basswood honey from Pinecrest Forest Honey & Wax.', '12', 'jar', '35', '42', '0', '1', 'https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:00', '2026-09-25 10:32:20');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('17', '8', '1', 'Wildflower Comb Honey', 'Freshly harvested, locally cultivated wildflower comb honey from Pinecrest Forest Honey & Wax.', '14.5', 'box', '26', '35', '0', '1', 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:00', '2026-09-25 10:32:20');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('18', '9', '2', 'Living Butterhead Lettuce', 'Freshly harvested, locally cultivated living butterhead lettuce from Eastside Urban Aquaponics.', '3.25', 'head', '32', '39', '0', '1', 'https://images.unsplash.com/photo-1556801712-76c8eb07bbc9?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:01', '2026-09-25 10:32:21');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('19', '9', '2', 'Genovese Micro Basil', 'Freshly harvested, locally cultivated genovese micro basil from Eastside Urban Aquaponics.', '4.5', 'clamshell', '38', '34', '0', '1', 'https://images.unsplash.com/photo-1618375569909-3c8616cf7733?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:01', '2026-09-25 10:32:21');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('20', '10', '1', 'Honeynut Squash', 'Freshly harvested, locally cultivated honeynut squash from Golden Ridge Heritage Farm.', '2.9', 'lb', '22', '26', '0', '1', 'https://plus.unsplash.com/premium_photo-1666823706503-46b8b71593fc?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:01', '2026-09-25 10:32:21');
INSERT INTO `products` (`id`, `farmer_id`, `category_id`, `name`, `description`, `price`, `unit`, `stock_quantity`, `weekly_recurring_stock`, `is_sold_out`, `is_available`, `image_url`, `created_at`, `updated_at`) VALUES ('21', '10', '1', 'Crisp Asian Pears', 'Freshly harvested, locally cultivated crisp asian pears from Golden Ridge Heritage Farm.', '5.25', 'bag', '21', '39', '0', '1', 'https://images.unsplash.com/photo-1514756331096-242fdeb70d4a?auto=format&fit=crop&w=600&q=80', '2026-09-25 09:49:01', '2026-09-25 10:32:21');

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
INSERT INTO `orders` (`id`, `customer_id`, `farmer_id`, `market_id`, `order_number`, `order_status`, `pickup_date`, `pickup_time_slot`, `total_amount`, `payment_method`, `cutoff_time`, `notes`, `created_at`, `updated_at`, `payment_status`) VALUES ('1', '5', '1', '1', 'ML-0998C705', 'completed', '2026-09-20 00:00:00', '09:00 AM - 10:00 AM', '15.25', 'pay_at_pickup', '2026-09-20 06:00:00', 'Please include extra ripe tomatoes if possible.', '2026-09-23 13:50:22', '2026-09-25 05:04:55', 'paid');
INSERT INTO `orders` (`id`, `customer_id`, `farmer_id`, `market_id`, `order_number`, `order_status`, `pickup_date`, `pickup_time_slot`, `total_amount`, `payment_method`, `cutoff_time`, `notes`, `created_at`, `updated_at`, `payment_status`) VALUES ('2', '5', '2', '2', 'ML-89BDA746', 'ready_for_pickup', '2026-09-24 00:00:00', '10:00 AM - 11:30 AM', '19.3', 'pay_at_pickup', '2026-09-24 07:30:00', 'I will be coming around 10:15 AM.', '2026-09-23 13:50:22', '2026-09-23 13:50:22', 'pending');
INSERT INTO `orders` (`id`, `customer_id`, `farmer_id`, `market_id`, `order_number`, `order_status`, `pickup_date`, `pickup_time_slot`, `total_amount`, `payment_method`, `cutoff_time`, `notes`, `created_at`, `updated_at`, `payment_status`) VALUES ('3', '1', '1', '1', 'ML-121E4BAC', 'cancelled', '2026-09-25 00:00:00', '08:30 AM - 10:30 AM', '4.5', 'pay_at_pickup', '2026-09-25 05:00:00', 'test', '2026-09-24 09:45:30', '2026-09-24 09:45:36', 'pending');
INSERT INTO `orders` (`id`, `customer_id`, `farmer_id`, `market_id`, `order_number`, `order_status`, `pickup_date`, `pickup_time_slot`, `total_amount`, `payment_method`, `cutoff_time`, `notes`, `created_at`, `updated_at`, `payment_status`) VALUES ('4', '5', '1', '1', 'ML-0854BFFE', 'cancelled', '2026-09-26 00:00:00', '08:30 AM - 10:30 AM', '16.25', 'pay_at_pickup', '2026-09-26 05:00:00', NULL, '2026-09-25 05:12:15', '2026-09-25 05:14:47', 'pending');

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
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES ('6', '3', '1', '1', '4.5', '4.5', '2026-09-24 09:45:30', '2026-09-24 09:45:30');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES ('7', '4', '2', '5', '3.25', '16.25', '2026-09-25 05:12:15', '2026-09-25 05:12:15');

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
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`, `updated_at`) VALUES ('2', '1', 'Pre-Order Confirmation #ML-121E4BAC', 'Your pre-order for Green Valley Organic Produce has been placed. Selected Pickup: Sep 25, 2026 (08:30 AM - 10:30 AM). Settlement is due in person at pickup.', 'order', '0', '2026-09-24 09:45:30', '2026-09-24 09:45:30');
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`, `updated_at`) VALUES ('3', '1', 'Pre-Order #ML-121E4BAC Cancelled', 'You have cancelled pre-order #ML-121E4BAC.', 'order', '0', '2026-09-24 09:45:36', '2026-09-24 09:45:36');
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`, `updated_at`) VALUES ('4', '5', 'Pre-Order Confirmation #ML-0854BFFE', 'Your pre-order for Green Valley Organic Produce has been placed. Selected Pickup: Sep 26, 2026 (08:30 AM - 10:30 AM). Settlement is due in person at pickup.', 'order', '0', '2026-09-25 05:12:15', '2026-09-25 05:12:15');
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`, `updated_at`) VALUES ('5', '5', 'Pre-Order #ML-0854BFFE Cancelled', 'You have cancelled pre-order #ML-0854BFFE.', 'order', '0', '2026-09-25 05:14:47', '2026-09-25 05:14:47');

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
INSERT INTO `announcements` (`id`, `created_by`, `title`, `content`, `badge_type`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'Welcome to MarketLink — Local Harvest Pre-Order Platform', 'Support local growers, reserve fresh harvest in advance, and pick up directly at your neighborhood market stalls. Remember: all pre-orders are settled in person at pickup.', 'success', '1', '2026-09-23 13:50:22', '2026-09-25 09:40:04');

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
