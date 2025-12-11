-- Add is_pinned field to forums (blogs) table
ALTER TABLE ma_forums 
ADD COLUMN IF NOT EXISTS is_pinned TINYINT(1) DEFAULT 0 COMMENT 'Whether post is pinned to top';

-- Add is_pinned field to questions table
ALTER TABLE ma_questions 
ADD COLUMN IF NOT EXISTS is_pinned TINYINT(1) DEFAULT 0 COMMENT 'Whether question is pinned to top';

-- Add index for better performance
CREATE INDEX IF NOT EXISTS idx_forums_pinned ON ma_forums(is_pinned, created_at DESC);
CREATE INDEX IF NOT EXISTS idx_questions_pinned ON ma_questions(is_pinned, created_at DESC);
