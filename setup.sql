-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` varchar(100) NOT NULL,
  `path` text NOT NULL,
  `size` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_parent_IDX` (`parent`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 2020-08-20 09:33:33