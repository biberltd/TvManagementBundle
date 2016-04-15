/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        28.12.2015
 */
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for categories_of_tv_programme
-- ----------------------------
DROP TABLE IF EXISTS `categories_of_tv_programme`;
CREATE TABLE `categories_of_tv_programme` (
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `programme` int(10) unsigned NOT NULL,
  `category` int(5) unsigned NOT NULL,
  PRIMARY KEY (`programme`,`category`),
  KEY `idxFCategoryOfTvProgramme` (`category`),
  CONSTRAINT `idxFCategoryOfTvProgramme` FOREIGN KEY (`category`) REFERENCES `tv_programme_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTvProgrammeCategory` FOREIGN KEY (`programme`) REFERENCES `tv_programme_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of categories_of_tv_programme
-- ----------------------------

-- ----------------------------
-- Table structure for genres_of_tv_programme
-- ----------------------------
DROP TABLE IF EXISTS `genres_of_tv_programme`;
CREATE TABLE `genres_of_tv_programme` (
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `programme` int(10) unsigned NOT NULL,
  `genre` int(5) unsigned NOT NULL,
  PRIMARY KEY (`genre`,`programme`),
  KEY `idxFTvProgrammeOfGenre` (`programme`),
  CONSTRAINT `idxFGenreOfTvProgramme` FOREIGN KEY (`genre`) REFERENCES `tv_programme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTvProgrammeOfGenre` FOREIGN KEY (`programme`) REFERENCES `tv_programme_genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of genres_of_tv_programme
-- ----------------------------

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
-- Records of tv_channel
-- ----------------------------

-- ----------------------------
-- Table structure for tv_programme
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme`;
CREATE TABLE `tv_programme` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `summary` text COLLATE utf8_turkish_ci,
  `rating_tag` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `title_original` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `title_local` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `broadcast_type` char(1) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'l:live,r:recorded (banttan), s:second show (ikinci gösterim - tekrar)',
  `description` text COLLATE utf8_turkish_ci,
  `motto` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL,
  `url` text COLLATE utf8_turkish_ci,
  `presented` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `broadcast_quality` varchar(3) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'hd:High definition, 3d:3d',
  `production_year` int(4) DEFAULT NULL,
  `is_dubbed` char(1) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'y:yes,n:no',
  `is_turkish` char(1) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'y:yes,n:no',
  `raw_json` longtext COLLATE utf8_turkish_ci,
  `uniq_key` text COLLATE utf8_turkish_ci DEFAULT  NULL COMMENT 'A special key to identify unique programmes.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of tv_programme
-- ----------------------------

-- ----------------------------
-- Table structure for tv_programme_category
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_category`;
CREATE TABLE `tv_programme_category` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `parent` int(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idxFParentTvProgrammeCategory` (`parent`),
  CONSTRAINT `idxFParentTvProgrammeCategory` FOREIGN KEY (`parent`) REFERENCES `tv_programme_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of tv_programme_category
-- ----------------------------

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
-- Records of tv_programme_category_localization
-- ----------------------------

-- ----------------------------
-- Table structure for tv_programme_genre
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_genre`;
CREATE TABLE `tv_programme_genre` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `parent` int(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idxFParentTvProgrammeGenre` (`parent`),
  CONSTRAINT `idxFParentTvProgrammeGenre` FOREIGN KEY (`parent`) REFERENCES `tv_programme_genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of tv_programme_genre
-- ----------------------------

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

-- ----------------------------
-- Records of tv_programme_genre_localization
-- ----------------------------

-- ----------------------------
-- Table structure for tv_programme_reminder
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_reminder`;
CREATE TABLE `tv_programme_reminder` (
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `date_reminder` datetime DEFAULT NULL,
  `member` int(10) unsigned DEFAULT NULL,
  `programme` int(10) unsigned DEFAULT NULL,
  KEY `idxFMemberOfTvProgrammeReminder` (`member`),
  KEY `idxFProgrammeOfTvProgrammeReminder` (`programme`),
  CONSTRAINT `idxFMemberOfTvProgrammeReminder` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFProgrammeOfTvProgrammeReminder` FOREIGN KEY (`programme`) REFERENCES `tv_programme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of tv_programme_reminder
-- ----------------------------

-- ----------------------------
-- Table structure for tv_programme_schedule
-- ----------------------------
DROP TABLE IF EXISTS `tv_programme_schedule`;
CREATE TABLE `tv_programme_schedule` (
  `utc_offset` int(2) unsigned NOT NULL COMMENT '2',
  `gmt_offset` int(2) unsigned DEFAULT NULL COMMENT '2',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `actual_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `duration` int(10) unsigned DEFAULT NULL COMMENT 'in minutes.',
  `channel` int(10) unsigned NOT NULL,
  `programme` int(10) unsigned NOT NULL,
  KEY `idxFChannelOfTvProgrammeSchedule` (`channel`),
  KEY `idxFProgrammeOfTvProgrammeSchedule` (`programme`),
  CONSTRAINT `idxFChannelOfTvProgrammeSchedule` FOREIGN KEY (`channel`) REFERENCES `tv_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFProgrammeOfTvProgrammeSchedule` FOREIGN KEY (`programme`) REFERENCES `tv_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of tv_programme_schedule
-- ----------------------------
