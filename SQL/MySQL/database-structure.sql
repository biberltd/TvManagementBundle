/*
Navicat MariaDB Data Transfer

Source Server         : localmariadb
Source Server Version : 100108
Source Host           : localhost:3306
Source Database       : bod_core

Target Server Type    : MariaDB
Target Server Version : 100108
File Encoding         : 65001

Date: 2015-11-10 18:52:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tv_channel
-- ----------------------------
DROP TABLE IF EXISTS `tv_channel`;
CREATE TABLE `tv_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `name` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Name of the channel.',
  `logo` text COLLATE utf8_turkish_ci COMMENT 'Path of logo file.',
  `frequency` text COLLATE utf8_turkish_ci COMMENT 'Can hold json_decoded values.',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for tv_programme
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme`;
CREATE TABLE `tv_programme` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `time` time NOT NULL,
  `summary` text COLLATE utf8_turkish_ci,
  `rating` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `title_original` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `title_local` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `channel` int(10) unsigned NOT NULL,
  `category` int(10) unsigned NOT NULL,
  `genre` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idxFTvProgrammeChannel` (`channel`),
  KEY `idxFTvProgrammeGenre` (`genre`),
  KEY `idxFTvProgrammeCategory` (`category`),
  CONSTRAINT `idxFTvProgrammeCategory` FOREIGN KEY (`category`) REFERENCES `tv_programme_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTvProgrammeChannel` FOREIGN KEY (`channel`) REFERENCES `tv_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTvProgrammeGenre` FOREIGN KEY (`genre`) REFERENCES `tv_programme_genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for tv_programme_category
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_category`;
CREATE TABLE `tv_programme_category` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for tv_programme_category_localization
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_category_localization`;
CREATE TABLE `tv_programme_category_localization` (
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL,
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `language` int(5) unsigned DEFAULT NULL,
  `category` int(10) unsigned DEFAULT NULL,
  KEY `idxFTvProgrammeCategoryLocalization` (`category`),
  KEY `idxFTvProgrammeCategoryLocalizationLanguage` (`language`),
  CONSTRAINT `idxFTvProgrammeCategoryLocalization` FOREIGN KEY (`category`) REFERENCES `tv_programme_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTvProgrammeCategoryLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for tv_programme_genre
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_genre`;
CREATE TABLE `tv_programme_genre` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for tv_programme_genre_localization
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_genre_localization`;
CREATE TABLE `tv_programme_genre_localization` (
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL,
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `language` int(5) unsigned DEFAULT NULL,
  `genre` int(10) unsigned DEFAULT NULL,
  KEY `idxFTvProgrammeGenreyLocalization` (`genre`) USING BTREE,
  KEY `idxFTvProgrammeGenreLocalizationLanguage` (`language`) USING BTREE,
  CONSTRAINT `idxFTvProgrammeGenreLocalization` FOREIGN KEY (`genre`) REFERENCES `tv_programme_genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTvProgrammeGenreLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;
