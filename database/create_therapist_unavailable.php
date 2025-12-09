<?php
/**
 * Create therapist unavailable dates table
 * Run this file once to create the ma_therapist_unavailable table
 */

include "../script/connection.php";

$sqlTable = "CREATE TABLE IF NOT EXISTS {$siteprefix}therapist_unavailable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    therapist_id INT NOT NULL,
    unavailable_date DATE NOT NULL,
    reason VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY unique_therapist_date (therapist_id, unavailable_date),
    INDEX idx_therapist (therapist_id),
    INDEX idx_date (unavailable_date),
    FOREIGN KEY (therapist_id) REFERENCES {$siteprefix}users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($con, $sqlTable)) {
    echo "✅ Table '{$siteprefix}therapist_unavailable' created successfully.<br>";
} else {
    echo "❌ Error creating table: " . mysqli_error($con) . "<br>";
}

mysqli_close($con);
echo "<br>✅ Therapist unavailable dates table setup complete!";
?>
