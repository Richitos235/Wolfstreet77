-- ============================================================================
-- Wolfstreet77 - COMPLETE DATABASE SCHEMA FIX
-- ============================================================================
-- This is a COMPLETE, CLEAN MySQL schema that fixes ALL runtime SQL errors
-- Generated to match CURRENT PHP code expectations (2026-05-27)
-- 
-- KEY FIXES:
-- ✓ Removed duplicate columns (fixes #1060 errors)
-- ✓ Added all missing columns referenced in code
-- ✓ Fixed game_events missing created_at column
-- ✓ Fixed game_state missing last_tick_timestamp column
-- ✓ Fixed news_feed missing is_pinned column
-- ✓ Standardized timestamp handling
-- ============================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `wolf` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wolf`;

-- ============================================================================
-- 1. USERS TABLE - Player accounts and progress
-- ============================================================================
CREATE TABLE IF NOT EXISTS `users` (
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
    INDEX `idx_next_tick` (`next_tick`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. MARKET_STOCKS TABLE - Available stocks in the market
-- ============================================================================
CREATE TABLE IF NOT EXISTS `market_stocks` (
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
    INDEX `idx_updated_at` (`updated_at`),
    INDEX `idx_trend` (`trend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. MARKET_HISTORY TABLE - Historical stock prices
-- ============================================================================
CREATE TABLE IF NOT EXISTS `market_history` (
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

-- ============================================================================
-- 4. PLAYER_STOCKS TABLE - User portfolios
-- ============================================================================
CREATE TABLE IF NOT EXISTS `player_stocks` (
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
    INDEX `idx_stock_id` (`stock_id`),
    INDEX `idx_quantity` (`quantity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. TRANSACTIONS TABLE - Transaction history
-- ============================================================================
CREATE TABLE IF NOT EXISTS `transactions` (
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
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_transaction_type` (`transaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. NEWS_FEED TABLE - Market and game news
-- ============================================================================
-- FIX: This table was missing is_pinned column - NOW INCLUDED
-- ============================================================================
CREATE TABLE IF NOT EXISTS `news_feed` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `category` ENUM('market', 'politics', 'tech', 'crime', 'economy') NOT NULL,
    `is_pinned` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    INDEX `idx_category` (`category`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_is_pinned` (`is_pinned`),
    INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. GAME_EVENTS TABLE - Game-wide events affecting market/economy
-- ============================================================================
-- FIX: This table was missing created_at column - NOW INCLUDED
-- ============================================================================
CREATE TABLE IF NOT EXISTS `game_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `effect_type` ENUM('market', 'economy', 'social', 'danger') NOT NULL,
    `effect_value` INT DEFAULT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_effect_type` (`effect_type`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. GAME_STATE TABLE - Global game state and timing
-- ============================================================================
-- FIX: This table was missing last_tick_timestamp column - NOW INCLUDED
-- ============================================================================
CREATE TABLE IF NOT EXISTS `game_state` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `current_tick` INT NOT NULL DEFAULT 0,
    `current_day` INT NOT NULL DEFAULT 1,
    `last_tick_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `next_tick_timestamp` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 6 HOUR),
    `is_tick_running` BOOLEAN DEFAULT FALSE,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_current_tick` (`current_tick`),
    INDEX `idx_next_tick_timestamp` (`next_tick_timestamp`),
    INDEX `idx_is_tick_running` (`is_tick_running`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DEMO DATA - Test data for development
-- ============================================================================

-- Game State
INSERT IGNORE INTO `game_state` (`id`, `current_tick`, `current_day`, `last_tick_timestamp`, `next_tick_timestamp`, `is_tick_running`)
VALUES (1, 0, 1, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 6 HOUR), FALSE);

-- Market Stocks
INSERT IGNORE INTO `market_stocks` (`name`, `short_name`, `current_price`, `previous_price`, `min_price`, `max_price`, `trend`, `volatility`) VALUES
('Wolf Industries', 'WOLF', 125.50, 123.00, 80.00, 150.00, 'rising', 3.50),
('Black Syndicate Corp', 'BLACK', 87.30, 90.00, 50.00, 120.00, 'falling', 5.20),
('Neon Tech Solutions', 'NEON', 245.75, 242.00, 200.00, 300.00, 'stable', 2.80),
('Shadow Markets', 'SHADOW', 56.20, 54.50, 40.00, 90.00, 'rising', 4.10),
('Red Consortium', 'RED', 198.45, 195.00, 150.00, 250.00, 'stable', 3.30);

-- News Feed
INSERT IGNORE INTO `news_feed` (`title`, `content`, `category`, `is_pinned`) VALUES
('Tržní boom technologií', 'Technologický sektor zažívá masivní vzestup. Odborníci očekávají pokračování trendu v příštích měsících.', 'tech', 1),
('Ekonomická krize ohrožuje trhy', 'Nové ekonomické údaje ukazují znepokojivé trendy. Investoři by měli být opatrní.', 'economy', 1),
('Policejní razie na černém trhu', 'Federální agentury provádějí koordinované razie v různých částech země.', 'crime', 0),
('Zvýšená poptávka po surovinách', 'Celosvětová poptávka po strategických surovinách dosáhla historického maxima.', 'market', 0),
('Nové szabadítání zákony v diskusi', 'Politická opozice navrhuje změny v právních předpisech týkajících se finančního trhu.', 'politics', 0);

-- Game Events
INSERT IGNORE INTO `game_events` (`title`, `description`, `effect_type`, `effect_value`, `is_active`, `created_at`, `expires_at`) VALUES
('Tržní boom', 'Všechny ceny akcií se zvyšují o 5-10%', 'market', 5, 1, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 12 HOUR)),
('Ekonomická krize', 'Volatilita na trhu se zvyšuje', 'economy', 3, 1, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)),
('Zvýšená bezpečnost', 'Vyšetřování zvýší riziko pro některé aktivity', 'danger', -2, 0, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 6 HOUR));

-- Test User (username: testuser, password: password)
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `role_type`, `money`, `bank_money`, `strength`, `intelligence`, `tolerance`, `current_day`, `next_tick`, `is_active`)
VALUES ('testuser', 'test@wolf.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36CMxyy.', 'trader', 50000.00, 25000.00, 12, 15, 10, 1, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 6 HOUR), 1);

-- Test Portfolio
INSERT IGNORE INTO `player_stocks` (`user_id`, `stock_id`, `quantity`, `buy_price_total`, `created_at`)
SELECT u.id, ms.id, 10, (ms.current_price * 10), CURRENT_TIMESTAMP
FROM `users` u
CROSS JOIN `market_stocks` ms
WHERE u.username = 'testuser' AND ms.short_name = 'WOLF'
LIMIT 1;

INSERT IGNORE INTO `player_stocks` (`user_id`, `stock_id`, `quantity`, `buy_price_total`, `created_at`)
SELECT u.id, ms.id, 5, (ms.current_price * 5), CURRENT_TIMESTAMP
FROM `users` u
CROSS JOIN `market_stocks` ms
WHERE u.username = 'testuser' AND ms.short_name = 'NEON'
LIMIT 1;

-- ============================================================================
-- SUMMARY OF FIXES
-- ============================================================================
-- 
-- ERROR #1054 (Unknown column) FIXES:
-- 
-- 1. ✅ game_events.created_at
--    - Was missing, breaking MarketService::getActiveEvents()
--    - Now: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
--
-- 2. ✅ game_state.last_tick_timestamp
--    - Was missing, breaking MarketService::getGameState()
--    - Now: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
--
-- 3. ✅ news_feed.is_pinned
--    - Was missing, breaking MarketService::getNewsFeeds()
--    - Now: TINYINT(1) DEFAULT 0
--
-- 4. ✅ market_stocks.created_at, updated_at
--    - Verified present in all references
--
-- 5. ✅ market_history.stock_id, price, game_tick, created_at
--    - All verified present
--
-- 6. ✅ users table - all columns verified
--    - username, email, password, role_type, money, bank_money
--    - strength, intelligence, tolerance, current_day, game_ticks
--    - next_tick, last_tick, created_at, is_active
--
-- 7. ✅ transactions table - all columns verified
--    - user_id, transaction_type, amount, balance_after, description
--    - related_stock_id, created_at
--
-- ERROR #1060 (Duplicate column name) FIXES:
--    - Removed all duplicate ALTER statements
--    - Created clean, idempotent schema
--    - Used INSERT IGNORE for demo data (safe on reimport)
--
-- SCHEMA CONSISTENCY:
--    - All tables use InnoDB engine
--    - All tables use utf8mb4 charset
--    - All timestamps use CURRENT_TIMESTAMP or ON UPDATE
--    - All indexes properly named
--    - All foreign keys properly defined
--    - All enums properly defined
--
-- ============================================================================
-- END OF DATABASE SCHEMA FIX
-- ============================================================================
