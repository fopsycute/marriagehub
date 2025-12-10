-- SQL to create therapist unavailable dates table

CREATE TABLE IF NOT EXISTS ma_therapist_unavailable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    therapist_id INT NOT NULL,
    unavailable_date DATE NOT NULL,
    reason VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY unique_therapist_date (therapist_id, unavailable_date),
    INDEX idx_therapist (therapist_id),
    INDEX idx_date (unavailable_date),
    FOREIGN KEY (therapist_id) REFERENCES ma_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
