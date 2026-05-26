-- Update game_state table for the new time system
ALTER TABLE game_state 
ADD COLUMN IF NOT EXISTS game_minutes INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS max_game_days INT DEFAULT 160,
ADD COLUMN IF NOT EXISTS real_time_reference TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Initialize the state if empty
INSERT IGNORE INTO game_state (id, current_day, current_tick, game_minutes, max_game_days, last_tick_timestamp, next_tick_timestamp, real_time_reference)
VALUES (1, 1, 0, 0, 160, NOW(), DATE_ADD(NOW(), INTERVAL 3 HOUR), NOW());