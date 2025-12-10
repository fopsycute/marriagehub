<?php
/**
 * Add order tracking fields to ma_orders table
 * Run this file once to add tracking columns
 */

include "../script/connection.php";

// Add tracking fields to orders table
$sql1 = "ALTER TABLE {$siteprefix}orders 
         ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL AFTER date";

$sql2 = "ALTER TABLE {$siteprefix}orders 
         ADD COLUMN IF NOT EXISTS vendor_notes TEXT DEFAULT NULL AFTER updated_at";

$sql3 = "ALTER TABLE {$siteprefix}orders 
         ADD INDEX IF NOT EXISTS idx_status (status)";

$sql4 = "ALTER TABLE {$siteprefix}orders 
         ADD INDEX IF NOT EXISTS idx_updated_at (updated_at)";

// Execute queries
$queries = [$sql1, $sql2, $sql3, $sql4];
$fieldNames = ['updated_at', 'vendor_notes', 'status index', 'updated_at index'];

foreach ($queries as $index => $sql) {
    if (mysqli_query($con, $sql)) {
        echo "✅ Added {$fieldNames[$index]} to orders table.<br>";
    } else {
        // Check if column/index already exists
        if (strpos(mysqli_error($con), 'Duplicate') !== false || strpos(mysqli_error($con), 'already exists') !== false) {
            echo "ℹ️ {$fieldNames[$index]} already exists in orders table.<br>";
        } else {
            echo "❌ Error adding {$fieldNames[$index]}: " . mysqli_error($con) . "<br>";
        }
    }
}

mysqli_close($con);
echo "<br>✅ Order tracking fields setup complete!";
?>
