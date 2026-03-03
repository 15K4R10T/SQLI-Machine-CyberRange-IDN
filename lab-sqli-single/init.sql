-- =============================================
-- Lab SQLi - Database Initialization
-- =============================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    secret VARCHAR(100)
);

INSERT INTO users (username, password, email, role, secret) VALUES
('admin',    'admin123',      'admin@lab.local',   'admin', 'FLAG{basic_sqli_found_admin}'),
('alice',    'alice_pass',    'alice@lab.local',   'user',  'FLAG{you_found_alice}'),
('bob',      'b0b_s3cr3t',    'bob@lab.local',     'user',  'FLAG{you_found_bob}'),
('charlie',  'ch@rlie99',     'charlie@lab.local', 'user',  'FLAG{you_found_charlie}'),
('dbadmin',  'Sup3rS3cr3t!',  'db@lab.local',      'admin', 'FLAG{hidden_admin_account}');

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    category VARCHAR(50),
    hidden BOOLEAN DEFAULT FALSE
);

INSERT INTO products (name, description, price, category, hidden) VALUES
('Laptop Pro',      'High-end laptop',           15000000, 'Electronics', FALSE),
('Mouse Wireless',  'Ergonomic wireless mouse',    250000, 'Electronics', FALSE),
('USB Hub',         'USB 3.0 7-port hub',          180000, 'Electronics', FALSE),
('Keyboard Mech',   'Mechanical keyboard',         850000, 'Electronics', FALSE),
('Secret Product',  'You should not see this',     999999, 'Secret',      TRUE);

CREATE TABLE IF NOT EXISTS flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flag_name VARCHAR(100),
    flag_value VARCHAR(200)
);

INSERT INTO flags (flag_name, flag_value) VALUES
('blind_flag_1', 'FLAG{blind_sqli_boolean_success}'),
('blind_flag_2', 'FLAG{blind_sqli_time_based_win}');

CREATE TABLE IF NOT EXISTS auth_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    attempt_time DATETIME DEFAULT NOW(),
    success BOOLEAN
);
