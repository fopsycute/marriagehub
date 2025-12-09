-- SQL to add order tracking fields to ma_orders table

ALTER TABLE ma_orders 
ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL AFTER date,
ADD COLUMN IF NOT EXISTS vendor_notes TEXT DEFAULT NULL AFTER updated_at;

-- Add index for faster queries on order status
ALTER TABLE ma_orders ADD INDEX IF NOT EXISTS idx_status (status);
ALTER TABLE ma_orders ADD INDEX IF NOT EXISTS idx_updated_at (updated_at);
