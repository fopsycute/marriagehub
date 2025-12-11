<?php
/**
 * Migration: Switch from Paystack to VPay Payment System
 * Date: December 10, 2025
 * Description: Add VPay configuration columns to site settings
 */

require_once __DIR__ . '/../script/connect.php';

echo "Starting VPay migration...\n";

// Add new VPay configuration columns
$sql1 = "ALTER TABLE {$siteprefix}site_settings 
  ADD COLUMN IF NOT EXISTS vpay_domain VARCHAR(20) DEFAULT 'sandbox' COMMENT 'VPay domain: sandbox or live',
  ADD COLUMN IF NOT EXISTS vpay_test_public_key VARCHAR(255) DEFAULT '' COMMENT 'VPay test/sandbox public key',
  ADD COLUMN IF NOT EXISTS vpay_live_public_key VARCHAR(255) DEFAULT '' COMMENT 'VPay live/production public key',
  ADD COLUMN IF NOT EXISTS payment_provider VARCHAR(20) DEFAULT 'vpay' COMMENT 'Payment provider: vpay or paystack'";

if (mysqli_query($con, $sql1)) {
    echo "✓ VPay configuration columns added successfully\n";
} else {
    $error = mysqli_error($con);
    if (strpos($error, 'Duplicate column name') !== false) {
        echo "✓ VPay columns already exist\n";
    } else {
        echo "✗ Error adding columns: $error\n";
        exit(1);
    }
}

// Rename paystack_key to legacy_paystack_key
$sql2 = "ALTER TABLE {$siteprefix}site_settings 
  CHANGE COLUMN paystack_key legacy_paystack_key VARCHAR(255) DEFAULT '' COMMENT 'Legacy Paystack key (deprecated)'";

if (mysqli_query($con, $sql2)) {
    echo "✓ Paystack key renamed to legacy_paystack_key\n";
} else {
    $error = mysqli_error($con);
    if (strpos($error, "Unknown column 'paystack_key'") !== false) {
        echo "✓ Paystack key already migrated\n";
    } else {
        echo "✗ Error renaming column: $error\n";
    }
}

// Update existing record to use VPay
$sql3 = "UPDATE {$siteprefix}site_settings 
SET payment_provider = 'vpay',
    vpay_domain = 'sandbox'
WHERE s = 1";

if (mysqli_query($con, $sql3)) {
    echo "✓ Payment provider updated to VPay\n";
} else {
    echo "✗ Error updating provider: " . mysqli_error($con) . "\n";
}

echo "\n=== VPay Migration Completed ===\n";
echo "Next steps:\n";
echo "1. Configure VPay keys in admin settings\n";
echo "2. Test sandbox: fdcdb195-6553-4890-844c-ee576b7ea715\n";
echo "3. Get your keys from: https://vpay.africa\n";
echo "4. Switch domain to 'live' when ready for production\n";

mysqli_close($con);
?>
