/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : bod_core

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2015-04-30 21:48:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for site
-- ----------------------------
DROP TABLE IF EXISTS `site`;
CREATE TABLE `site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `title` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Title of the site.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Url key of site.',
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Short description of site.',
  `default_language` int(5) unsigned DEFAULT NULL COMMENT 'Default language of the site.',
  `settings` text COLLATE utf8_turkish_ci COMMENT 'Base64 Encoded and serialized site settings information.',
  `date_added` datetime NOT NULL COMMENT 'Date when the site is added.',
  `date_updated` datetime NOT NULL COMMENT 'Date when the site''s details last updated.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  `domain` text COLLATE utf8_turkish_ci COMMENT 'Domain of the site.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUSiteId` (`id`) USING BTREE,
  UNIQUE KEY `idxUSiteUrlKey` (`url_key`) USING BTREE,
  KEY `idxNSiteDateAdded` (`date_added`) USING BTREE,
  KEY `idxNSiteDateUpdated` (`date_updated`) USING BTREE,
  KEY `idxFDefaultLanguageOfSite` (`default_language`) USING BTREE,
  KEY `idxNSiteDateRemoved` (`date_removed`),
  CONSTRAINT `idxFDefaultLanguageOfSite` FOREIGN KEY (`default_language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;
