-- Add anonymous posting field to forums and questions tables
-- Run this migration via admin/run-migrations.php

-- Add is_anonymous field to forums table (blog posts)
ALTER TABLE `ma_forums` 
ADD COLUMN `is_anonymous` TINYINT(1) DEFAULT 0 COMMENT '1=Anonymous, 0=Public Author' AFTER `status`;

-- Add is_anonymous field to questions table
ALTER TABLE `ma_questions` 
ADD COLUMN `is_anonymous` TINYINT(1) DEFAULT 0 COMMENT '1=Anonymous, 0=Public Author' AFTER `status`;

-- Add indexes for better performance
ALTER TABLE `ma_forums` ADD INDEX `idx_forums_anonymous` (`is_anonymous`);
ALTER TABLE `ma_questions` ADD INDEX `idx_questions_anonymous` (`is_anonymous`);
