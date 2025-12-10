-- SQL to create view tracking tables for unique view counting

-- Table for tracking blog views
CREATE TABLE IF NOT EXISTS ma_blog_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    viewed_at DATETIME NOT NULL,
    INDEX idx_slug (slug),
    INDEX idx_ip_date (ip_address, viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for tracking question views
CREATE TABLE IF NOT EXISTS ma_question_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    viewed_at DATETIME NOT NULL,
    INDEX idx_slug (slug),
    INDEX idx_ip_date (ip_address, viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clean up old view records (optional - run periodically to keep table size manageable)
-- DELETE FROM ma_blog_views WHERE viewed_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
-- DELETE FROM ma_question_views WHERE viewed_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
