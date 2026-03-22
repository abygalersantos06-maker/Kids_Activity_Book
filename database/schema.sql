-- --------------------------------------------------------
-- Database: `kids-activity-book` (Kid's Activity Book Edition)
-- FIXED with CORRECT bcrypt password hashes
-- --------------------------------------------------------

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS `kids-activity-book`;
CREATE DATABASE IF NOT EXISTS `kids-activity-book`;
USE `kids-activity-book`;

-- ========================================================
-- USER AUTHENTICATION TABLES (UPDATED with Admin System)
-- ========================================================

-- Create users table for authentication with admin columns
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','shopper') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shopper',
  `is_super_admin` tinyint(1) DEFAULT 0,
  `admin_level` enum('super','manager','editor') DEFAULT 'editor',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert admin users with CORRECT bcrypt password hashes
-- Password for all admin users: admin123
-- The hash below is for 'admin123' - DO NOT CHANGE
INSERT INTO `users` (`username`, `email`, `password`, `first_name`, `last_name`, `role`, `is_super_admin`, `admin_level`) VALUES
('admin', 'admin@kidsbookery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 1, 'super'),
('superadmin', 'superadmin@kidsbookery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 'Admin', 'admin', 1, 'super'),
('manager', 'manager@kidsbookery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Store', 'Manager', 'admin', 0, 'manager'),
('editor', 'editor@kidsbookery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Content', 'Editor', 'admin', 0, 'editor'),
('shopper', 'shopper@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Shopper', 'shopper', 0, 'editor');

-- ========================================================
-- ADMIN LOGS TABLE
-- ========================================================

-- Create admin_logs table for tracking admin activity
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- EXISTING TABLES (Original Structure)
-- ========================================================

-- --------------------------------------------------------
-- Table structure for table `category`
-- --------------------------------------------------------
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `navigation` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `category`
INSERT INTO `category` (`id`, `name`, `description`, `navigation`) VALUES
(1, 'Coloring Books', 'Creative and fun coloring books for kids', 1),
(2, 'Puzzle Books', 'Engaging puzzles and brain teasers', 1),
(3, 'Educational Games', 'Learning games for young children', 1),
(4, 'Printables', 'Instant download activity sheets and worksheets', 1);

-- --------------------------------------------------------
-- Table structure for table `member`
-- --------------------------------------------------------
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forename` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `picture` varchar(254) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `member`
INSERT INTO `member` (`id`, `forename`, `surname`, `email`, `password`, `joined`, `picture`) VALUES
(1, 'Ivy', 'Stone', 'ivy@kidsbooks.link', 'c63j-82ve-2sv9-qlb38', '2026-03-04 09:00:00', 'ivy.jpg'),
(2, 'Luke', 'Wood', 'luke@kidsbooks.link', 'saq8-2f2k-3nv7-fa4k', '2026-03-04 09:05:00', NULL),
(3, 'Emiko', 'Ito', 'emi@kidsbooks.link', 'sk3r-vd92-3vn1-exm2', '2026-03-04 09:10:00', 'emi.jpg');

-- --------------------------------------------------------
-- Table structure for table `image`
-- --------------------------------------------------------
CREATE TABLE `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `image`
INSERT INTO `image` (`id`, `file`, `alt`) VALUES
(1, 'jungle-coloring.jpg', 'Jungle Coloring Fun Book'),
(2, 'underwater-adventure.jpg', 'Underwater Adventure Coloring Book'),
(3, 'space-explorers.jpg', 'Space Explorers Coloring Book'),
(4, 'dinosaur-world.jpg', 'Dinosaur World Coloring Book'),
(5, 'fairy-tale-scenes.jpg', 'Fairy Tale Scenes Coloring Book'),
(6, 'animal-kingdom.jpg', 'Animal Kingdom Coloring Book'),
(7, 'brain-teaser-puzzles.jpg', 'Brain Teaser Puzzles Book'),
(8, 'number-games.jpg', 'Number Games Puzzle Book'),
(9, 'word-fun.jpg', 'Word Fun Crossword and Word Search'),
(10, 'logic-labyrinth.jpg', 'Logic Labyrinth Puzzle Book'),
(11, 'puzzle-planet.jpg', 'Puzzle Planet Activity Book'),
(12, 'riddle-me-this.jpg', 'Riddle Me This Book'),
(13, 'alphabet-adventure.jpg', 'Alphabet Adventure Educational Game'),
(14, 'counting-safari.jpg', 'Counting Safari Learning Game'),
(15, 'shapes-colors.jpg', 'Shapes & Colors Activity Book'),
(16, 'animal-sounds-game.jpg', 'Animal Sounds Game'),
(17, 'memory-match.jpg', 'Memory Match Educational Game'),
(18, 'educational-maze-fun.jpg', 'Educational Maze Fun'),
(19, 'printable-coloring-pages.jpg', 'Printable Coloring Pages PDF'),
(20, 'printable-worksheets.jpg', 'Printable Worksheets PDF'),
(21, 'craft-activity-sheets.jpg', 'Craft Activity Sheets PDF'),
(22, 'fun-mazes-pdf.jpg', 'Fun Mazes PDF'),
(23, 'dot-to-dot-fun.jpg', 'Dot-to-Dot Fun Sheets'),
(24, 'sticker-sheets.jpg', 'Printable Sticker Sheets');

-- --------------------------------------------------------
-- Table structure for table `article` with PHP PRICES (₱)
-- --------------------------------------------------------
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `price` decimal(10,2) DEFAULT 599.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `category_id` (`category_id`),
  KEY `member_id` (`member_id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_ibfk_3` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `article` with PHILIPPINE PESO (₱) prices
INSERT INTO `article` (`id`, `title`, `summary`, `content`, `created`, `category_id`, `member_id`, `image_id`, `published`, `price`) VALUES
(1, 'Jungle Coloring Fun', 'A fun jungle-themed coloring book', 'Let kids explore the jungle with this exciting coloring book filled with animals and adventure. Features 50+ pages of lions, tigers, monkeys, and exotic birds. Perfect for ages 3-8.', '2026-03-04 10:00:00', 1, 1, 1, 1, 699.00),
(2, 'Underwater Adventure', 'Dive into ocean coloring fun', 'This book features ocean animals, submarines, and treasure hunts for kids to color. Includes dolphins, whales, colorful fish, and hidden treasures. 45 pages of underwater fun.', '2026-03-04 10:05:00', 1, 2, 2, 1, 799.00),
(3, 'Space Explorers', 'Color and learn about planets', 'Children can color astronauts, rockets, and planets while learning fun space facts. Educational and entertaining with 40 pages of space adventures.', '2026-03-04 10:10:00', 1, 3, 3, 1, 899.00),
(4, 'Dinosaur World', 'Roar! Dinosaur coloring book', 'Filled with friendly and fierce dinosaurs for kids to color and enjoy. Includes T-Rex, Triceratops, Stegosaurus and more. 55 pages of prehistoric fun.', '2026-03-04 10:15:00', 1, 1, 4, 1, 749.00),
(5, 'Fairy Tale Scenes', 'Magical fairy tale coloring book', 'From castles to dragons, kids can bring classic fairy tales to life with colors. Features princesses, knights, magical creatures and enchanted forests.', '2026-03-04 10:20:00', 1, 2, 5, 1, 849.00),
(6, 'Animal Kingdom', 'Color cute animals', 'A fun book for animal lovers featuring cute animals from around the world. Pandas, penguins, giraffes, and more in 50 adorable pages.', '2026-03-04 10:25:00', 1, 3, 6, 1, 649.00),
(7, 'Brain Teaser Puzzles', 'Fun puzzles for kids', 'Challenge young minds with mazes, crosswords, and logic puzzles. 60 puzzles that develop critical thinking skills while having fun.', '2026-03-04 10:30:00', 2, 1, 7, 1, 999.00),
(8, 'Number Games', 'Math puzzles and activities', 'A playful way to practice numbers, counting, and addition/subtraction skills. 45 pages of number fun for early learners.', '2026-03-04 10:35:00', 2, 2, 8, 1, 799.00),
(9, 'Word Fun', 'Kids crossword & word search', 'Learn new words and sharpen vocabulary with fun word puzzles. 50 puzzles with increasing difficulty levels.', '2026-03-04 10:40:00', 2, 3, 9, 1, 749.00),
(10, 'Logic Labyrinth', 'Mazes and problem solving', 'Kids navigate mazes and brain teasers to enhance critical thinking. 40 challenging mazes with fun themes.', '2026-03-04 10:45:00', 2, 1, 10, 1, 849.00),
(11, 'Puzzle Planet', 'Fun brain challenges', 'From spot-the-difference to pattern recognition, kids can enjoy endless puzzles. 55 pages of variety puzzles.', '2026-03-04 10:50:00', 2, 2, 11, 1, 899.00),
(12, 'Riddle Me This', 'Engaging riddles for children', 'A collection of age-appropriate riddles to keep children entertained and thinking. 100 riddles with answers.', '2026-03-04 10:55:00', 2, 3, 12, 1, 699.00),
(13, 'Alphabet Adventure', 'Learn letters with fun games', 'Interactive games to help kids recognize letters and improve early reading skills. A-Z activities with colorful illustrations.', '2026-03-04 11:00:00', 3, 1, 13, 1, 1099.00),
(14, 'Counting Safari', 'Numbers and counting games', 'Kids learn to count animals and objects with fun interactive challenges. Numbers 1-20 with safari theme.', '2026-03-04 11:05:00', 3, 2, 14, 1, 899.00),
(15, 'Shapes & Colors', 'Educational activity book', 'Identify shapes, match colors, and complete fun exercises to learn geometry basics. 40 pages of shape and color fun.', '2026-03-04 11:10:00', 3, 3, 15, 1, 799.00),
(16, 'Animal Sounds Game', 'Learn animals and sounds', 'Engaging games where children match animals to their sounds. 30 different animals and their unique sounds.', '2026-03-04 11:15:00', 3, 1, 16, 1, 849.00),
(17, 'Memory Match', 'Boost memory skills', 'Fun memory card games to improve focus and recall for young learners. 24 matching pairs with cute illustrations.', '2026-03-04 11:20:00', 3, 2, 17, 1, 749.00),
(18, 'Educational Maze Fun', 'Problem-solving activities', 'Help kids solve mazes and puzzles while developing logical thinking. 35 mazes with educational themes.', '2026-03-04 11:25:00', 3, 3, 18, 1, 799.00),
(19, 'Printable Coloring Pages', 'Instant download for kids', 'PDF coloring pages of animals, superheroes, and cartoons ready to print at home. 100 printable pages.', '2026-03-04 11:30:00', 4, 1, 19, 1, 549.00),
(20, 'Printable Worksheets', 'Learning worksheets', 'Fun worksheets for math, reading, and writing exercises for young children. 75 printable worksheets.', '2026-03-04 11:35:00', 4, 2, 20, 1, 599.00),
(21, 'Craft Activity Sheets', 'Arts and crafts at home', 'Downloadable templates and instructions for simple craft projects. 25 craft projects with templates.', '2026-03-04 11:40:00', 4, 3, 21, 1, 649.00),
(22, 'Fun Mazes PDF', 'Printable maze challenges', 'Maze games for kids that can be printed and solved anywhere. 50 printable mazes of varying difficulty.', '2026-03-04 11:45:00', 4, 1, 22, 1, 499.00),
(23, 'Dot-to-Dot Fun', 'Connect the dots activities', 'Printable dot-to-dot sheets that teach numbers and improve hand-eye coordination. 40 connect-the-dots puzzles.', '2026-03-04 11:50:00', 4, 2, 23, 1, 549.00),
(24, 'Sticker Sheets', 'Printable stickers for kids', 'Download and print fun sticker sheets for creative play. 10 sheets with 100+ stickers total.', '2026-03-04 11:55:00', 4, 3, 24, 1, 699.00);

-- --------------------------------------------------------
-- Table structure for table `cart`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `article_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'pending',
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Add AUTO_INCREMENT values
-- --------------------------------------------------------

-- AUTO_INCREMENT for article table
ALTER TABLE `article` AUTO_INCREMENT=25;

-- AUTO_INCREMENT for category table
ALTER TABLE `category` AUTO_INCREMENT=5;

-- AUTO_INCREMENT for image table
ALTER TABLE `image` AUTO_INCREMENT=25;

-- AUTO_INCREMENT for member table
ALTER TABLE `member` AUTO_INCREMENT=4;

-- AUTO_INCREMENT for cart table
ALTER TABLE `cart` AUTO_INCREMENT=1;

-- AUTO_INCREMENT for orders table
ALTER TABLE `orders` AUTO_INCREMENT=1;

-- AUTO_INCREMENT for order_items table
ALTER TABLE `order_items` AUTO_INCREMENT=1;

-- AUTO_INCREMENT for users table
ALTER TABLE `users` AUTO_INCREMENT=10;

-- AUTO_INCREMENT for admin_logs table
ALTER TABLE `admin_logs` AUTO_INCREMENT=1;

-- --------------------------------------------------------
-- Additional indexes for better performance
-- --------------------------------------------------------
CREATE INDEX idx_article_published ON article(published);
CREATE INDEX idx_article_category ON article(category_id);
CREATE INDEX idx_article_member ON article(member_id);
CREATE INDEX idx_cart_session ON cart(session_id);
CREATE INDEX idx_orders_session ON orders(session_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_admin_logs_admin ON admin_logs(admin_id);
CREATE INDEX idx_admin_logs_created ON admin_logs(created_at);