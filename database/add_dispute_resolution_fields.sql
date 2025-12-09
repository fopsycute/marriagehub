-- SQL to add dispute resolution fields to ma_disputes table

ALTER TABLE ma_disputes 
ADD COLUMN IF NOT EXISTS resolution_note TEXT DEFAULT NULL AFTER issue,
ADD COLUMN IF NOT EXISTS refund_amount DECIMAL(10,2) DEFAULT NULL AFTER resolution_note,
ADD COLUMN IF NOT EXISTS refund_to ENUM('buyer', 'seller') DEFAULT NULL AFTER refund_amount,
ADD COLUMN IF NOT EXISTS resolved_at DATETIME DEFAULT NULL AFTER refund_to;

-- Add index for faster queries on resolved disputes
ALTER TABLE ma_disputes ADD INDEX idx_resolved_at (resolved_at);
