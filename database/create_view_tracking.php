<?php
/**
 * Create view tracking tables for unique view counting
 * Run this file once to create the ma_blog_views and ma_question_views tables
 */

include "../script/connection.php";

// SQL to create blog views table
$sqlBlogViews = "CREATE TABLE IF NOT EXISTS {$siteprefix}blog_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    viewed_at DATETIME NOT NULL,
    INDEX idx_slug (slug),
    INDEX idx_ip_date (ip_address, viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// SQL to create question views table
$sqlQuestionViews = "CREATE TABLE IF NOT EXISTS {$siteprefix}question_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    viewed_at DATETIME NOT NULL,
    INDEX idx_slug (slug),
    INDEX idx_ip_date (ip_address, viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Execute queries
if (mysqli_query($con, $sqlBlogViews)) {
    echo "✅ Table '{$siteprefix}blog_views' created successfully.<br>";
} else {
    echo "❌ Error creating '{$siteprefix}blog_views' table: " . mysqli_error($con) . "<br>";
}

if (mysqli_query($con, $sqlQuestionViews)) {
    echo "✅ Table '{$siteprefix}question_views' created successfully.<br>";
} else {
    echo "❌ Error creating '{$siteprefix}question_views' table: " . mysqli_error($con) . "<br>";
}

mysqli_close($con);
echo "<br>✅ View tracking tables setup complete!";
?>
