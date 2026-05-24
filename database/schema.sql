CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    referral_code VARCHAR(20) NOT NULL UNIQUE,
    referred_by_user_id BIGINT UNSIGNED NULL,
    package_name ENUM('silver', 'gold', 'diamond') NOT NULL DEFAULT 'silver',
    balance DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total_earnings DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    referral_earnings DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    failed_login_attempts INT NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    remember_token_hash VARCHAR(255) NULL,
    remember_token_expires_at DATETIME NULL,
    is_banned TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (referred_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS deposits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    asset ENUM('USDT_TRC20', 'USDT_BEP20') NOT NULL,
    wallet_address VARCHAR(190) NOT NULL,
    screenshot_path VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_note VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS withdrawals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    fee_percent DECIMAL(5,2) NOT NULL DEFAULT 20.00,
    fee_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    net_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    wallet_address VARCHAR(190) NOT NULL,
    network VARCHAR(60) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_note VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS referrals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    referrer_user_id BIGINT UNSIGNED NOT NULL,
    referred_user_id BIGINT UNSIGNED NOT NULL,
    commission_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'credited') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_ref_pair (referrer_user_id, referred_user_id),
    FOREIGN KEY (referrer_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referred_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS earnings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    source_type ENUM('trade', 'referral', 'manual') NOT NULL,
    source_id BIGINT UNSIGNED NULL,
    amount DECIMAL(14,2) NOT NULL,
    note VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trade_signals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    signal_code VARCHAR(40) NOT NULL UNIQUE,
    pair_name VARCHAR(30) NOT NULL,
    category VARCHAR(60) NOT NULL,
    target_package ENUM('all', 'silver', 'gold', 'diamond') NOT NULL DEFAULT 'all',
    profit_percent DECIMAL(6,2) NOT NULL DEFAULT 1.00,
    expires_in_minutes INT NOT NULL DEFAULT 60,
    one_time_use TINYINT(1) NOT NULL DEFAULT 1,
    direction ENUM('LONG', 'SHORT') NOT NULL,
    entry_text VARCHAR(120) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    scheduled_at DATETIME NULL,
    created_by_admin_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_admin_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS trades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    trade_signal_id BIGINT UNSIGNED NULL,
    signal_code_input VARCHAR(40) NOT NULL,
    estimated_percent DECIMAL(6,2) NOT NULL,
    estimated_amount DECIMAL(14,2) NOT NULL,
    result_status ENUM('estimated_gain', 'estimated_loss') NOT NULL,
    note VARCHAR(255) NOT NULL,
    executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trade_signal_id) REFERENCES trade_signals(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS copy_trade_usage (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trade_signal_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_signal_user (trade_signal_id, user_id),
    FOREIGN KEY (trade_signal_id) REFERENCES trade_signals(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS salaries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    package_name ENUM('silver', 'gold', 'diamond') NOT NULL,
    monthly_amount DECIMAL(14,2) NOT NULL,
    weekly_amount DECIMAL(14,2) NOT NULL,
    required_referrals INT NOT NULL,
    target_referral_plan ENUM('silver', 'gold', 'diamond') NOT NULL,
    status ENUM('locked', 'eligible') NOT NULL DEFAULT 'locked',
    achieved_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_package_salary (user_id, package_name),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admin_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(140) NOT NULL,
    context TEXT NULL,
    ip_address VARCHAR(64) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    title VARCHAR(140) NOT NULL,
    body TEXT NOT NULL,
    type ENUM('announcement', 'deposit', 'withdrawal', 'trade', 'system') NOT NULL DEFAULT 'system',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token_hash VARCHAR(255) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO settings (setting_key, setting_value) VALUES
('estimated_profit_min', '1.0'),
('estimated_profit_max', '4.0'),
('estimated_loss_chance_percent', '25'),
('referral_commission_percent', '5'),
('referral_earning_boost_per_user_percent', '0.5'),
('withdrawal_fee_percent', '20'),
('usdt_trc20_wallet_address', 'TXQ6P1PAv2vLqeXrwCvcmpguFWDZc5zFNd'),
('usdt_bep20_wallet_address', '0xea90fcbdee28f842cafd6985967855501ffd4f8d'),
('site_notice', 'Market risk applies. No guaranteed profits.')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
