CREATE TABLE IF NOT EXISTS `brainstorms` (
  `brainstorm_id` varchar(32) NOT NULL,
  `range_id` varchar(32) NULL,
  `seminar_id` varchar(32) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `user_id` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `chdate` BIGINT NOT NULL,
  `mkdate` BIGINT NOT NULL,
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`brainstorm_id`),
  KEY `range_id` (`range_id`)
);

CREATE TABLE IF NOT EXISTS `brainstorm_votes` (
  `brainstorm_id` varchar(32) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `vote` tinyint(4) DEFAULT NULL,
  `chdate` BIGINT NOT NULL,
  `mkdate` BIGINT NOT NULL,
  PRIMARY KEY (`brainstorm_id`,`user_id`)
);