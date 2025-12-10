-- Create ma_feedback table for article/question helpfulness votes
CREATE TABLE IF NOT EXISTS `ma_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL COMMENT 'ID of the article or question',
  `content_type` varchar(50) NOT NULL COMMENT 'Type: article, question, etc.',
  `user_id` int(11) DEFAULT NULL COMMENT 'User ID if logged in',
  `user_ip` varchar(45) NOT NULL COMMENT 'IP address for anonymous tracking',
  `vote` enum('yes','no') NOT NULL COMMENT 'Was this helpful? yes or no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `content_type` (`content_type`),
  KEY `user_id` (`user_id`),
  KEY `vote` (`vote`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores helpfulness feedback for articles and questions';
