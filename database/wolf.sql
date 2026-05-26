-- Wolfstreet77 Database Schema - FIXED
-- Complete game database with users, market, events, news

CREATE DATABASE IF NOT EXISTS `wolf` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wolf`;

-- Users / Players Table
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role_type` ENUM('trader', 'gangster', 'pimp') NOT NULL,
    `money` DECIMAL(20, 2) NOT NULL DEFAULT 10000.00,
    `bank_money` DECIMAL(20, 2) NOT NULL DEFAULT 0.00,
    `strength` INT NOT NULL DEFAULT 10,
    `intelligence` INT NOT NULL DEFAULT 10,
    `tolerance` INT NOT NULL DEFAULT 10,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `game_ticks` INT NOT NULL DEFAULT 0,
    `current_day` INT NOT NULL DEFAULT 1,
    `next_tick` TIMESTAMP NULL DEFAULT NULL,
    `last_tick` TIMESTAMP NULL DEFAULT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_next_tick` (`next_tick`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Market Stocks Table
CREATE TABLE `market_stocks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `short_name` VARCHAR(10) NOT NULL UNIQUE,
    `current_price` DECIMAL(10, 2) NOT NULL,
    `previous_price` DECIMAL(10, 2) NOT NULL,
    `min_price` DECIMAL(10, 2) NOT NULL,
    `max_price` DECIMAL(10, 2) NOT NULL,
    `trend` ENUM('rising', 'falling', 'stable') NOT NULL DEFAULT 'stable',
    `volatility` DECIMAL(5, 2) NOT NULL DEFAULT 2.50,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_change_tick` INT NOT NULL DEFAULT 0,
    INDEX `idx_short_name` (`short_name`),
    INDEX `idx_updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Price History Table
CREATE TABLE `market_history` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `stock_id` BIGINT UNSIGNED NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `game_tick` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`stock_id`) REFERENCES `market_stocks`(`id`) ON DELETE CASCADE,
    INDEX `idx_stock_id` (`stock_id`),
    INDEX `idx_game_tick` (`game_tick`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game Events Table
CREATE TABLE `game_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `effect_type` ENUM('market', 'economy', 'social', 'danger') NOT NULL,
    `effect_value` INT DEFAULT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_effect_type` (`effect_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- News Feed Table
CREATE TABLE `news_feed` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `category` ENUM('market', 'politics', 'tech', 'crime', 'economy') NOT NULL,
    `is_pinned` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_is_pinned` (`is_pinned`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Player Stocks Portfolio Table
CREATE TABLE `player_stocks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `stock_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL DEFAULT 0,
    `buy_price_total` DECIMAL(20, 2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`stock_id`) REFERENCES `market_stocks`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_player_stock` (`user_id`, `stock_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_stock_id` (`stock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transaction History Table
CREATE TABLE `transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `transaction_type` ENUM('buy', 'sell', 'transfer', 'income', 'expense') NOT NULL,
    `amount` DECIMAL(20, 2) NOT NULL,
    `balance_after` DECIMAL(20, 2) NOT NULL,
    `description` VARCHAR(255),
    `related_stock_id` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`related_stock_id`) REFERENCES `market_stocks`(`id`) ON DELETE SET NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game State / Tick Management Table
CREATE TABLE `game_state` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `current_tick` INT NOT NULL DEFAULT 0,
    `current_day` INT NOT NULL DEFAULT 1,
    `last_tick_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `next_tick_timestamp` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 6 HOUR),
    `is_tick_running` BOOLEAN DEFAULT FALSE,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INSERT DEMO DATA

-- Game State
INSERT INTO `game_state` (`current_tick`, `current_day`, `last_tick_timestamp`, `next_tick_timestamp`)
VALUES (0, 1, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 6 HOUR));

-- Demo Market Stocks
INSERT INTO `market_stocks` (`name`, `short_name`, `current_price`, `previous_price`, `min_price`, `max_price`, `trend`, `volatility`) VALUES
('Wolf Industries', 'WOLF', 125.50, 123.00, 80.00, 150.00, 'rising', 3.50),
('Black Syndicate Corp', 'BLACK', 87.30, 90.00, 50.00, 120.00, 'falling', 5.20),
('Neon Tech Solutions', 'NEON', 245.75, 242.00, 200.00, 300.00, 'stable', 2.80),
('Shadow Markets', 'SHADOW', 56.20, 54.50, 40.00, 90.00, 'rising', 4.10),
('Red Consortium', 'RED', 198.45, 195.00, 150.00, 250.00, 'stable', 3.30);

-- Demo News
INSERT INTO `news_feed` (`title`, `content`, `category`, `is_pinned`) VALUES
('Tržní boom technologií', 'Technologický sektor zažívá masivní vzestup. Odborníci očekávají pokračování trendu v příštích měsících.', 'tech', TRUE),
('Ekonomická krize ohrožuje trhy', 'Nové ekonomické údaje ukazují znepokojivé trendy. Investoři by měli být opatrní.', 'economy', TRUE),
('Policejní razie na černém trhu', 'Federální agentury provádějí koordinované razie v různých částech země.', 'crime', FALSE),
('Zvýšená poptávka po surovinách', 'Celosvětová poptávka po strategických surovinách dosáhla historického maxima.', 'market', FALSE),
('Nové szabadítási zákonem v diskusi', 'Politická opozice navrhuje změny v právních předpisech týkajících se finančního trhu.', 'politics', FALSE);

-- Demo Game Events
INSERT INTO `game_events` (`title`, `description`, `effect_type`, `effect_value`, `is_active`, `expires_at`) VALUES
('Tržní boom', 'Všechny ceny akcií se zvyšují o 5-10%', 'market', 5, TRUE, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 12 HOUR)),
('Ekonomická krize', 'Volatilita na trhu se zvyšuje', 'economy', 3, TRUE, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)),
('Zvýšená bezpečnost', 'Vyšetřování zvýší riziko pro některé aktivity', 'danger', -2, FALSE, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 6 HOUR));

-- Create test user (password: "password")
INSERT INTO `users` (`username`, `email`, `password`, `role_type`, `money`, `bank_money`, `strength`, `intelligence`, `tolerance`, `current_day`, `next_tick`)
VALUES ('testuser', 'test@wolf.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36CMxyy.', 'trader', 50000.00, 25000.00, 12, 15, 10, 1, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 6 HOUR));

-- Add test portfolio
INSERT INTO `player_stocks` (`user_id`, `stock_id`, `quantity`, `buy_price_total`)
SELECT u.id, ms.id, 10, (ms.current_price * 10)
FROM `users` u
CROSS JOIN `market_stocks` ms
WHERE u.username = 'testuser' AND ms.short_name = 'WOLF'
LIMIT 1;

INSERT INTO `player_stocks` (`user_id`, `stock_id`, `quantity`, `buy_price_total`)
SELECT u.id, ms.id, 5, (ms.current_price * 5)
FROM `users` u
CROSS JOIN `market_stocks` ms
WHERE u.username = 'testuser' AND ms.short_name = 'NEON'
LIMIT 1;
