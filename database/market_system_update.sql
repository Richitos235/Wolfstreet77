-- Update market_stocks with supply and volatility
ALTER TABLE market_stocks 
ADD COLUMN IF NOT EXISTS total_supply INT DEFAULT 10000,
ADD COLUMN IF NOT EXISTS available_supply INT DEFAULT 10000,
ADD COLUMN IF NOT EXISTS volatility DECIMAL(5,2) DEFAULT 5.00;

-- Clear old stocks and seed the 10 core stocks
TRUNCATE TABLE market_stocks;

INSERT INTO market_stocks (name, short_name, current_price, previous_price, total_supply, available_supply, volatility, trend) VALUES
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