-- Drop the database if it already exists for a clean setup
DROP DATABASE IF EXISTS artisania_db;

-- Create the new database
CREATE DATABASE artisania_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the new database for all subsequent commands
USE artisania_db;

-- =================================================================
-- Table structure for `users`
-- Stores all users: clients, artisans, delivery personnel, and admins
-- =================================================================
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('client','artisan','livreur','admin') NOT NULL DEFAULT 'client',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =================================================================
-- Table structure for `categories`
-- =================================================================
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;
-- =================================================================
-- Table structure for `products`
-- Stores product information, linked to an artisan and requiring admin approval
-- =================================================================
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL, -- Foreign key to the artisan (user) who owns it
  `category_id` INT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `image_path` VARCHAR(255),
  `stock` INT UNSIGNED NOT NULL DEFAULT 0,
  `dimensions` VARCHAR(255) NULL,      
  `care_instructions` VARCHAR(255) NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL -- NEW FOREIGN KEY
) ENGINE=InnoDB;


-- =================================================================
-- Table structure for `reviews`
-- Stores customer reviews for products
-- =================================================================
CREATE TABLE `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `comment` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;


-- =================================================================
-- Table structure for `orders`
-- Stores overall information about a customer's order
-- =================================================================
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL, -- The customer who placed the order
  `livreur_id` INT UNSIGNED NULL DEFAULT NULL, -- The delivery person assigned to this order
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'paid', 'ready_for_delivery', 'shipped', 'delivered', 'canceled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`livreur_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =================================================================
-- Table structure for `order_items`
-- Links products to orders, storing quantity and price at time of purchase
-- =================================================================
CREATE TABLE `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;


-- =================================================================
-- Table structure for `transactions`
-- Stores payment information for each successful order
-- =================================================================
CREATE TABLE `transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `gateway_transaction_id` VARCHAR(255),
  `status` ENUM('completed', 'failed') NOT NULL DEFAULT 'completed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;


-- =================================================================
-- SEED DATA: Pre-populate the database with essential starting data
-- =================================================================

-- Create the Administrator user
-- The password is 'admin123', which has been securely hashed.
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@artisania.com', '$2y$10$gO6hM7f0SAxR.ApveLX5fOvy2EKBW.Yxoc.C239N/35dIeA4f8hWC', 'admin');

-- Create a sample Artisan user (password: 'artisan123')
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('potteryman', 'pottery@example.com', '$2y$10$mBq.gVzO8dJ/s/E4w9Wp9O/j7YvY2uC8e/8n/rC9e.9/hA/5fO/u.', 'artisan');

-- Create a sample Client user (password: 'client123')
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('janedoe', 'jane.doe@example.com', '$2y$10$C8.c5i8jH2uF8hE9jK0oA./eB9cE2t.1jJ5pD7s/wU6gH0kM4lA9y', 'client');

-- Add a product from the artisan that is waiting for approval
INSERT INTO `products` (`user_id`, `name`, `description`, `price`, `image_path`, `stock`, `status`) VALUES
(2, 'Handmade Rustic Mug', 'A beautiful, one-of-a-kind mug made from local clay.', 25.00, 'path/to/mug.png', 10, 'pending');

-- Add a product from the artisan that is already approved
INSERT INTO `products` (`user_id`, `name`, `description`, `price`, `image_path`, `stock`, `status`) VALUES
(2, 'Ceramic Soup Bowl', 'Perfect for your favorite soup or stew.', 35.00, 'path/to/bowl.png', 5, 'approved');
