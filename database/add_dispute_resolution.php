<?php
/**
 * Add dispute resolution fields to ma_disputes table
 * Run this file once to add resolution tracking columns
 */

include "../script/connection.php";

// Add resolution fields to disputes table
$sql1 = "ALTER TABLE {$siteprefix}disputes 
         ADD COLUMN IF NOT EXISTS resolution_note TEXT DEFAULT NULL AFTER issue";

$sql2 = "ALTER TABLE {$siteprefix}disputes 
         ADD COLUMN IF NOT EXISTS refund_amount DECIMAL(10,2) DEFAULT NULL AFTER resolution_note";

$sql3 = "ALTER TABLE {$siteprefix}disputes 
         ADD COLUMN IF NOT EXISTS refund_to ENUM('buyer', 'seller') DEFAULT NULL AFTER refund_amount";

$sql4 = "ALTER TABLE {$siteprefix}disputes 
         ADD COLUMN IF NOT EXISTS resolved_at DATETIME DEFAULT NULL AFTER refund_to";

$sql5 = "ALTER TABLE {$siteprefix}disputes 
         ADD INDEX IF NOT EXISTS idx_resolved_at (resolved_at)";

// Execute queries
$queries = [$sql1, $sql2, $sql3, $sql4, $sql5];
$fieldNames = ['resolution_note', 'refund_amount', 'refund_to', 'resolved_at', 'index'];

foreach ($queries as $index => $sql) {
    if (mysqli_query($con, $sql)) {
        echo "✅ Added {$fieldNames[$index]} to disputes table.<br>";
    } else {
        // Check if column already exists
        if (strpos(mysqli_error($con), 'Duplicate') !== false || strpos(mysqli_error($con), 'already exists') !== false) {
            echo "ℹ️ {$fieldNames[$index]} already exists in disputes table.<br>";
        } else {
            echo "❌ Error adding {$fieldNames[$index]}: " . mysqli_error($con) . "<br>";
        }
    }
}

mysqli_close($con);
echo "<br>✅ Dispute resolution fields setup complete!";
?>
