-- SQL to create promotional codes/discount system table

CREATE TABLE IF NOT EXISTS ma_promo_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    vendor_id INT NOT NULL,
    discount_type ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    min_purchase DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2) DEFAULT NULL,
    usage_limit INT DEFAULT NULL,
    usage_count INT DEFAULT 0,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    description TEXT DEFAULT NULL,
    applicable_to ENUM('all', 'products', 'services', 'bookings') DEFAULT 'all',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    INDEX idx_code (code),
    INDEX idx_vendor (vendor_id),
    INDEX idx_status (status),
    INDEX idx_valid_dates (valid_from, valid_until),
    FOREIGN KEY (vendor_id) REFERENCES ma_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table to track promo code usage
CREATE TABLE IF NOT EXISTS ma_promo_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    promo_code_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id VARCHAR(100) NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    order_amount DECIMAL(10,2) NOT NULL,
    used_at DATETIME NOT NULL,
    INDEX idx_promo (promo_code_id),
    INDEX idx_user (user_id),
    INDEX idx_order (order_id),
    FOREIGN KEY (promo_code_id) REFERENCES ma_promo_codes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES ma_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
