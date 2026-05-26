-- Wolfstreet77 - Standardized Database Schema
-- Generated: 2026-05-27
-- Purpose: Resolve all schema inconsistencies and missing columns.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role_type` ENUM('trader', 'gangster', 'pimp') NOT NULL DEFAULT 'trader',
  `money` DECIMAL(15,2) NOT NULL DEFAULT 10000.00,
  `bank_money` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `strength` INT NOT NULL DEFAULT 10,
  `intelligence` INT NOT NULL DEFAULT 10,
  `tolerance` INT NOT NULL DEFAULT 10,
  `current_day` INT NOT NULL DEFAULT 1,
  `game_ticks` INT NOT NULL DEFAULT 0,
  `next_tick` TIMESTAMP NULL DEFAULT NULL,
  `last_tick` TIMESTAMP NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `market_stocks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `market_stocks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `short_name` VARCHAR(10) NOT NULL,
  `current_price` DECIMAL(15,2) NOT NULL DEFAULT 100.00,
  `previous_price` DECIMAL(15,2) NOT NULL DEFAULT 100.00,
  `min_price` DECIMAL(15,2) NOT NULL DEFAULT 1.00,
  `max_price` DECIMAL(15,2) NOT NULL DEFAULT 10000.00,
  `trend` ENUM('rising', 'falling', 'stable') NOT NULL DEFAULT 'stable',
  `volatility` DECIMAL(5,2) NOT NULL DEFAULT 5.00,
  `total_supply` INT NOT NULL DEFAULT 10000,
  `available_supply` INT NOT NULL DEFAULT 10000,
  `last_change_tick` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `short_name_UNIQUE` (`short_name` ASC)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `player_stocks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `player_stocks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `stock_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `buy_price_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_stock_UNIQUE` (`user_id` ASC, `stock_id` ASC),
  CONSTRAINT `fk_player_stocks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_player_stocks_stock` FOREIGN KEY (`stock_id`) REFERENCES `market_stocks` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `market_history`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `market_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_id` INT UNSIGNED NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `game_tick` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_market_history_stock_idx` (`stock_id` ASC),
  CONSTRAINT `fk_market_history_stock` FOREIGN KEY (`stock_id`) REFERENCES `market_stocks` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `transactions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `transaction_type` ENUM('buy', 'sell', 'transfer', 'income', 'expense') NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `balance_after` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `description` VARCHAR(255) NULL,
  `related_stock_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_transactions_user_idx` (`user_id` ASC),
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `news_feed`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `news_feed` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `category` ENUM('market', 'politics', 'tech', 'crime', 'economy') NOT NULL DEFAULT 'economy',
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `game_events`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `game_events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `effect_type` ENUM('market', 'economy', 'social', 'danger') NOT NULL,
  `effect_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `game_state`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `game_state` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `current_tick` INT NOT NULL DEFAULT 0,
  `current_day` INT NOT NULL DEFAULT 1,
  `game_minutes` INT NOT NULL DEFAULT 0,
  `max_game_days` INT NOT NULL DEFAULT 160,
  `last_tick_timestamp` TIMESTAMP NULL DEFAULT NULL,
  `next_tick_timestamp` TIMESTAMP NULL DEFAULT NULL,
  `real_time_reference` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_tick_running` TINYINT(1) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Seed Data
-- -----------------------------------------------------

-- Initial Game State
INSERT IGNORE INTO `game_state` (`id`, `current_tick`, `current_day`, `game_minutes`, `max_game_days`, `last_tick_timestamp`, `next_tick_timestamp`) 
VALUES (1, 0, 1, 0, 160, NOW(), DATE_ADD(NOW(), INTERVAL 3 HOUR));

-- Initial Stocks
INSERT IGNORE INTO `market_stocks` (`name`, `short_name`, `current_price`, `previous_price`, `total_supply`, `available_supply`, `volatility`, `trend`) VALUES
('NeoBank Corp', 'NEO', 150.00, 145.00, 10000, 8500, 3.50, 'rising'),
('ToxicOil Industries', 'TOX', 45.50, 48.00, 10000, 9200, 8.20, 'falling'),
('CyberNet Systems', 'CNS', 210.25, 205.00, 10000, 7100, 4.10, 'rising'),
('DarkCoin Exchange', 'DCX', 850.00, 920.00, 10000, 5400, 15.50, 'falling'),
('IronVault Holdings', 'IVH', 320.00, 318.00, 10000, 9800, 1.20, 'rising'),
('Crypto Syndicate', 'CSY', 12.40, 11.80, 10000, 4200, 12.00, 'rising'),
('Urban Motors Group', 'UMG', 88.00, 89.50, 10000, 8900, 5.40, 'falling'),
('BlackMarket Logistics', 'BML', 175.00, 170.00, 10000, 6300, 6.80, 'rising'),
('Quantum Energy Ltd', 'QEL', 410.00, 415.00, 10000, 9100, 3.20, 'falling'),
('Nexus Media Group', 'NMG', 62.30, 61.00, 10000, 7800, 4.50, 'rising');

-- Initial News
INSERT IGNORE INTO `news_feed` (`title`, `content`, `category`, `is_pinned`) VALUES
('Market Crash Imminent?', 'Analysts warn of a potential bubble in the tech sector as CyberNet Systems reaches all-time highs.', 'market', 1),
('New Crypto Regulations', 'The Syndicate is pushing back against new government attempts to track DarkCoin transactions.', 'politics', 0),
('Oil Spill in Sector 7', 'ToxicOil Industries faces massive fines after a pipeline leak contaminated the local water supply.', 'crime', 0);

SET FOREIGN_KEY_CHECKS = 1;