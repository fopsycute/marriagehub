-- Migration: Switch from Paystack to VPay Payment System
-- Date: December 10, 2025
-- Description: Add VPay configuration columns and rename paystack_key column

-- Add new VPay configuration columns to ma_site_settings
ALTER TABLE ma_site_settings 
  ADD COLUMN vpay_domain VARCHAR(20) DEFAULT 'sandbox' COMMENT 'VPay domain: sandbox or live',
  ADD COLUMN vpay_test_public_key VARCHAR(255) DEFAULT '' COMMENT 'VPay test/sandbox public key',
  ADD COLUMN vpay_live_public_key VARCHAR(255) DEFAULT '' COMMENT 'VPay live/production public key',
  ADD COLUMN payment_provider VARCHAR(20) DEFAULT 'vpay' COMMENT 'Payment provider: vpay or paystack';

-- Rename paystack_key to legacy_paystack_key for reference
ALTER TABLE ma_site_settings 
  CHANGE COLUMN paystack_key legacy_paystack_key VARCHAR(255) DEFAULT '' COMMENT 'Legacy Paystack key (deprecated)';

-- Update existing record to use VPay
UPDATE ma_site_settings 
SET payment_provider = 'vpay',
    vpay_domain = 'sandbox'
WHERE s = 1;

-- Migration completed successfully
SELECT 'VPay migration completed. Please configure VPay keys in admin settings.' AS status;
