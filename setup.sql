-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `encoder_queue`;
CREATE TABLE `encoder_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  `codec` text NOT NULL,
  `resolution` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` varchar(100) NOT NULL,
  `path` text NOT NULL,
  `size` bigint(20) NOT NULL,
  `modified` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`) USING HASH,
  KEY `files_parent_IDX` (`parent`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 2020-09-18 09:34:49
