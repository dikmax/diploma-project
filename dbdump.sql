/*
SQLyog Enterprise - MySQL GUI v7.15 
MySQL - 5.0.67-0ubuntu6 : Database - librarian
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`librarian` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `librarian`;

/*Table structure for table `lib_author` */

DROP TABLE IF EXISTS `lib_author`;

CREATE TABLE `lib_author` (
  `lib_author_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `name` varchar(100) NOT NULL COMMENT 'Display name',
  `description_text_id` int(10) unsigned NOT NULL COMMENT 'ID with text description',
  `front_description` text NOT NULL COMMENT 'Description to show on main page',
  `lib_writeboard_id` int(10) unsigned NOT NULL COMMENT 'ID of author writeboard',
  PRIMARY KEY  (`lib_author_id`),
  KEY `FK_lib_author` (`description_text_id`),
  KEY `FK_lib_author_writeboard` (`lib_writeboard_id`),
  CONSTRAINT `FK_lib_author_text` FOREIGN KEY (`description_text_id`) REFERENCES `lib_text` (`lib_text_id`),
  CONSTRAINT `FK_lib_author_writeboard` FOREIGN KEY (`lib_writeboard_id`) REFERENCES `lib_writeboard` (`lib_writeboard_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1033 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Authors';

/*Table structure for table `lib_author_has_tag` */

DROP TABLE IF EXISTS `lib_author_has_tag`;

CREATE TABLE `lib_author_has_tag` (
  `lib_user_id` int(11) unsigned NOT NULL,
  `lib_tag_id` int(11) unsigned NOT NULL,
  `lib_author_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`lib_user_id`,`lib_tag_id`,`lib_author_id`),
  KEY `FK_lib_author_has_tag_lib_tag` (`lib_tag_id`),
  KEY `FK_lib_author_has_tag_lib_author` (`lib_author_id`),
  CONSTRAINT `FK_lib_author_has_tag_lib_author` FOREIGN KEY (`lib_author_id`) REFERENCES `lib_author` (`lib_author_id`),
  CONSTRAINT `FK_lib_author_has_tag_lib_tag` FOREIGN KEY (`lib_tag_id`) REFERENCES `lib_tag` (`lib_tag_id`),
  CONSTRAINT `FK_lib_author_has_tag_lib_user` FOREIGN KEY (`lib_user_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_author_has_title` */

DROP TABLE IF EXISTS `lib_author_has_title`;

CREATE TABLE `lib_author_has_title` (
  `lib_author_id` int(11) unsigned NOT NULL,
  `lib_title_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`lib_author_id`,`lib_title_id`),
  KEY `FK_lib_author_has_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_author_has_title` FOREIGN KEY (`lib_title_id`) REFERENCES `lib_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_author_has_title_author` FOREIGN KEY (`lib_author_id`) REFERENCES `lib_author` (`lib_author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_author_image` */

DROP TABLE IF EXISTS `lib_author_image`;

CREATE TABLE `lib_author_image` (
  `lib_author_image_id` int(10) unsigned NOT NULL auto_increment,
  `lib_author_id` int(10) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `image_date` datetime NOT NULL,
  PRIMARY KEY  (`lib_author_image_id`),
  KEY `FK_lib_author_image_author` (`lib_author_id`),
  CONSTRAINT `FK_lib_author_image_author` FOREIGN KEY (`lib_author_id`) REFERENCES `lib_author` (`lib_author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_author_name` */

DROP TABLE IF EXISTS `lib_author_name`;

CREATE TABLE `lib_author_name` (
  `lib_author_name_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_author_id` int(10) unsigned NOT NULL COMMENT 'Author ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  PRIMARY KEY  (`lib_author_name_id`),
  KEY `FK_lib_author_name` (`lib_author_id`),
  CONSTRAINT `FK_lib_author_name` FOREIGN KEY (`lib_author_id`) REFERENCES `lib_author` (`lib_author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1032 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Author different names names';

/*Table structure for table `lib_author_name_index` */

DROP TABLE IF EXISTS `lib_author_name_index`;

CREATE TABLE `lib_author_name_index` (
  `lib_author_name_index_id` int(10) unsigned NOT NULL auto_increment,
  `word` varchar(50) NOT NULL,
  `lib_author_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`lib_author_name_index_id`),
  KEY `FK_lib_author_name_index` (`lib_author_id`),
  KEY `lib_author_name_word` (`word`(10)),
  CONSTRAINT `FK_lib_author_name_index` FOREIGN KEY (`lib_author_id`) REFERENCES `lib_author` (`lib_author_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_channel` */

DROP TABLE IF EXISTS `lib_channel`;

CREATE TABLE `lib_channel` (
  `lib_channel_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name of channel',
  `description` varchar(255) NOT NULL COMMENT 'Description of channel',
  PRIMARY KEY  (`lib_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Different channels (eg. news or blog)';

/*Table structure for table `lib_channel_item` */

DROP TABLE IF EXISTS `lib_channel_item`;

CREATE TABLE `lib_channel_item` (
  `lib_channel_item_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_channel_id` int(10) unsigned NOT NULL COMMENT 'Channel ID',
  `item_text_id` int(10) unsigned NOT NULL COMMENT 'Text of the item',
  `item_date` datetime NOT NULL COMMENT 'Date of the item',
  `author_id` int(10) unsigned NOT NULL COMMENT 'Author''s ID',
  `published` tinyint(1) unsigned NOT NULL default '1' COMMENT 'Is published',
  PRIMARY KEY  (`lib_channel_item_id`),
  KEY `FK_lib_news` (`item_text_id`),
  KEY `FK_lib_channel_item_channel` (`lib_channel_id`),
  KEY `FK_lib_channel_item` (`author_id`),
  CONSTRAINT `FK_lib_channel_item` FOREIGN KEY (`author_id`) REFERENCES `lib_user` (`lib_user_id`),
  CONSTRAINT `FK_lib_channel_item_channel` FOREIGN KEY (`lib_channel_id`) REFERENCES `lib_channel` (`lib_channel_id`),
  CONSTRAINT `FK_lib_channel_item_text` FOREIGN KEY (`item_text_id`) REFERENCES `lib_text` (`lib_text_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Channel items';

/*Table structure for table `lib_channel_item_has_tag` */

DROP TABLE IF EXISTS `lib_channel_item_has_tag`;

CREATE TABLE `lib_channel_item_has_tag` (
  `lib_channel_item_id` int(10) unsigned NOT NULL COMMENT 'Channel item ID',
  `lib_tag_id` int(10) unsigned NOT NULL COMMENT 'Tag ID',
  PRIMARY KEY  (`lib_channel_item_id`,`lib_tag_id`),
  KEY `FK_lib_channel_item_has_tag` (`lib_tag_id`),
  CONSTRAINT `FK_lib_channel_item_has_tag` FOREIGN KEY (`lib_tag_id`) REFERENCES `lib_tag` (`lib_tag_id`),
  CONSTRAINT `FK_lib_channel_item_has_tag_channel_item` FOREIGN KEY (`lib_channel_item_id`) REFERENCES `lib_channel_item` (`lib_channel_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lib_mail_message` */

DROP TABLE IF EXISTS `lib_mail_message`;

CREATE TABLE `lib_mail_message` (
  `lib_mail_message_id` bigint(20) unsigned NOT NULL auto_increment,
  `lib_mail_thread_id` bigint(20) unsigned NOT NULL,
  `from_user1` tinyint(3) unsigned NOT NULL COMMENT '1 - from user1, 0 - from user2',
  `message` text NOT NULL,
  `date` datetime NOT NULL,
  `is_new` tinyint(1) NOT NULL,
  PRIMARY KEY  (`lib_mail_message_id`),
  KEY `FK_lib_mail_message_thread` (`lib_mail_thread_id`),
  CONSTRAINT `FK_lib_mail_message_thread` FOREIGN KEY (`lib_mail_thread_id`) REFERENCES `lib_mail_thread` (`lib_mail_thread_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_mail_thread` */

DROP TABLE IF EXISTS `lib_mail_thread`;

CREATE TABLE `lib_mail_thread` (
  `lib_mail_thread_id` bigint(20) unsigned NOT NULL auto_increment,
  `user1_id` int(10) unsigned NOT NULL,
  `user2_id` int(10) unsigned NOT NULL,
  `state_user1` int(10) unsigned NOT NULL default '0' COMMENT 'State of thread user1: 1 - active, 2 - sent, 3 - archive',
  `state_user2` int(10) unsigned NOT NULL default '0' COMMENT 'State of thread user2: 1 - active, 2 - sent, 3 - archive',
  `subject` varchar(255) NOT NULL,
  `date` datetime NOT NULL COMMENT 'Date of last thread update',
  PRIMARY KEY  (`lib_mail_thread_id`),
  KEY `FK_lib_mail_thread_user1` (`user1_id`),
  KEY `FK_lib_mail_thread_user2` (`user2_id`),
  KEY `state_user1` (`state_user1`),
  KEY `state_user2` (`state_user2`),
  CONSTRAINT `FK_lib_mail_thread_user1` FOREIGN KEY (`user1_id`) REFERENCES `lib_user` (`lib_user_id`),
  CONSTRAINT `FK_lib_mail_thread_user2` FOREIGN KEY (`user2_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_tag` */

DROP TABLE IF EXISTS `lib_tag`;

CREATE TABLE `lib_tag` (
  `lib_tag_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Tag name',
  PRIMARY KEY  (`lib_tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Tags';

/*Table structure for table `lib_text` */

DROP TABLE IF EXISTS `lib_text`;

CREATE TABLE `lib_text` (
  `lib_text_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_text_revision_id` int(10) unsigned NOT NULL COMMENT 'Revision ID',
  `cdate` datetime NOT NULL COMMENT 'Date of creation',
  PRIMARY KEY  (`lib_text_id`),
  KEY `FK_lib_text_rev_text` (`lib_text_revision_id`),
  CONSTRAINT `FK_lib_text_rev_text` FOREIGN KEY (`lib_text_revision_id`) REFERENCES `lib_text_revision` (`lib_text_revision_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55698 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Table contains text which need revisioning';

/*Table structure for table `lib_text_revision` */

DROP TABLE IF EXISTS `lib_text_revision`;

CREATE TABLE `lib_text_revision` (
  `lib_text_revision_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_text_id` int(10) unsigned default NULL COMMENT 'Connected text ID',
  `lib_text_revision_content_id` int(10) unsigned NOT NULL COMMENT 'Revision content ID',
  `mdate` datetime NOT NULL COMMENT 'Modify date',
  `revision` int(10) unsigned NOT NULL default '0' COMMENT 'Revision number',
  `author_id` int(10) unsigned NOT NULL COMMENT 'ID of user who made changes',
  `changes` text NOT NULL COMMENT 'Description of made changes',
  PRIMARY KEY  (`lib_text_revision_id`),
  KEY `FK_lib_text_revision` (`lib_text_revision_content_id`),
  KEY `FK_lib_text_revision_user` (`author_id`),
  KEY `FK_lib_text_revision_text` (`lib_text_id`),
  CONSTRAINT `FK_lib_text_revision` FOREIGN KEY (`lib_text_revision_content_id`) REFERENCES `lib_text_revision_content` (`lib_text_revision_content_id`),
  CONSTRAINT `FK_lib_text_revision_text` FOREIGN KEY (`lib_text_id`) REFERENCES `lib_text` (`lib_text_id`),
  CONSTRAINT `FK_lib_text_revision_user` FOREIGN KEY (`author_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55710 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Text revisions';

/*Table structure for table `lib_text_revision_content` */

DROP TABLE IF EXISTS `lib_text_revision_content`;

CREATE TABLE `lib_text_revision_content` (
  `lib_text_revision_content_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `content` longtext NOT NULL COMMENT 'Revision content',
  PRIMARY KEY  (`lib_text_revision_content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55715 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Revision text data';

/*Table structure for table `lib_title` */

DROP TABLE IF EXISTS `lib_title`;

CREATE TABLE `lib_title` (
  `lib_title_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `name` varchar(255) NOT NULL,
  `authors_index` varchar(255) NOT NULL COMMENT 'List of authors in format "author1#url1#author2#url2#..."',
  `description_text_id` int(10) unsigned NOT NULL,
  `front_description` text NOT NULL,
  `lib_writeboard_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`lib_title_id`),
  KEY `FK_lib_author_title_writeboard` (`lib_writeboard_id`),
  KEY `FK_lib_author_title_description` (`description_text_id`),
  CONSTRAINT `FK_lib_author_title_description` FOREIGN KEY (`description_text_id`) REFERENCES `lib_text` (`lib_text_id`),
  CONSTRAINT `FK_lib_author_title_writeboard` FOREIGN KEY (`lib_writeboard_id`) REFERENCES `lib_writeboard` (`lib_writeboard_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54667 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_title_has_tag` */

DROP TABLE IF EXISTS `lib_title_has_tag`;

CREATE TABLE `lib_title_has_tag` (
  `lib_user_id` int(10) unsigned NOT NULL,
  `lib_tag_id` int(10) unsigned NOT NULL,
  `lib_title_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`lib_user_id`,`lib_tag_id`,`lib_title_id`),
  KEY `FK_lib_title_has_tag_lib_tag` (`lib_tag_id`),
  KEY `FK_lib_title_has_tag_lib_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_title_has_tag_lib_tag` FOREIGN KEY (`lib_tag_id`) REFERENCES `lib_tag` (`lib_tag_id`),
  CONSTRAINT `FK_lib_title_has_tag_lib_title` FOREIGN KEY (`lib_title_id`) REFERENCES `lib_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_title_has_tag_lib_user` FOREIGN KEY (`lib_user_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_title_similar` */

DROP TABLE IF EXISTS `lib_title_similar`;

CREATE TABLE `lib_title_similar` (
  `title1_id` int(10) unsigned NOT NULL,
  `title2_id` int(10) unsigned NOT NULL,
  `avg` float NOT NULL,
  `count` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`title1_id`,`title2_id`),
  KEY `FK_lib_title_similar_title2` (`title2_id`),
  CONSTRAINT `FK_lib_title_similar_title1` FOREIGN KEY (`title1_id`) REFERENCES `lib_title` (`lib_title_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_lib_title_similar_title2` FOREIGN KEY (`title2_id`) REFERENCES `lib_title` (`lib_title_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_user` */

DROP TABLE IF EXISTS `lib_user`;

CREATE TABLE `lib_user` (
  `lib_user_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `login` varchar(255) NOT NULL COMMENT 'User login',
  `password` varchar(32) NOT NULL COMMENT 'MD5 of user password',
  `email` varchar(32) NOT NULL COMMENT 'User''s email',
  `registration_date` datetime NOT NULL COMMENT 'User registration date',
  `login_date` datetime NOT NULL COMMENT 'User last login date',
  `login_ip` varchar(15) NOT NULL default '0.0.0.0' COMMENT 'User last login IP',
  `lib_writeboard_id` int(10) unsigned NOT NULL COMMENT 'User personal writeboard',
  PRIMARY KEY  (`lib_user_id`),
  UNIQUE KEY `user_login` (`login`(10)),
  KEY `FK_lib_user_writeboard_id` (`lib_writeboard_id`),
  KEY `user_email` (`email`),
  CONSTRAINT `FK_lib_user_writeboard_id` FOREIGN KEY (`lib_writeboard_id`) REFERENCES `lib_writeboard` (`lib_writeboard_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1032 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='User accounts';

/*Table structure for table `lib_user_bookshelf` */

DROP TABLE IF EXISTS `lib_user_bookshelf`;

CREATE TABLE `lib_user_bookshelf` (
  `lib_user_bookshelf_id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_user_id` int(10) unsigned NOT NULL COMMENT 'User ID',
  `lib_title_id` int(10) unsigned NOT NULL COMMENT 'Title ID',
  `relation` int(11) NOT NULL COMMENT 'Don''t know yet',
  PRIMARY KEY  (`lib_user_bookshelf_id`),
  KEY `FK_lib_user_bookshelf_user` (`lib_user_id`),
  KEY `FK_lib_user_bookshelf_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_user_bookshelf_title` FOREIGN KEY (`lib_title_id`) REFERENCES `lib_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_user_bookshelf_user` FOREIGN KEY (`lib_user_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3343980 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_user_friendship` */

DROP TABLE IF EXISTS `lib_user_friendship`;

CREATE TABLE `lib_user_friendship` (
  `user1_id` int(10) unsigned NOT NULL,
  `user2_id` int(10) unsigned NOT NULL,
  `state` tinyint(3) unsigned NOT NULL default '0' COMMENT '1 - approved, 2 - request sent, 3 - request received, 4 - declined',
  PRIMARY KEY  (`user1_id`,`user2_id`),
  KEY `FK_lib_user_friendship_user2` (`user2_id`),
  CONSTRAINT `FK_lib_user_friendship_user1` FOREIGN KEY (`user1_id`) REFERENCES `lib_user` (`lib_user_id`),
  CONSTRAINT `FK_lib_user_friendship_user2` FOREIGN KEY (`user2_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_user_neighborhood` */

DROP TABLE IF EXISTS `lib_user_neighborhood`;

CREATE TABLE `lib_user_neighborhood` (
  `user1_id` int(10) unsigned NOT NULL,
  `user2_id` int(11) unsigned NOT NULL,
  `avg` float NOT NULL,
  `count` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`user1_id`,`user2_id`),
  KEY `FK_lib_user_neighborhood_user2` (`user2_id`),
  CONSTRAINT `FK_lib_user_neighborhood_user1` FOREIGN KEY (`user1_id`) REFERENCES `lib_user` (`lib_user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_lib_user_neighborhood_user2` FOREIGN KEY (`user2_id`) REFERENCES `lib_user` (`lib_user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `lib_writeboard` */

DROP TABLE IF EXISTS `lib_writeboard`;

CREATE TABLE `lib_writeboard` (
  `lib_writeboard_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `owner_description` varchar(50) NOT NULL COMMENT 'String with data to whom writeboard belongs',
  PRIMARY KEY  (`lib_writeboard_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56737 DEFAULT CHARSET=utf8;

/*Table structure for table `lib_writeboard_message` */

DROP TABLE IF EXISTS `lib_writeboard_message`;

CREATE TABLE `lib_writeboard_message` (
  `lib_writeboard_message_id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_writeboard_id` int(10) unsigned NOT NULL COMMENT 'Writeboard ID',
  `writeboard_writer` int(10) unsigned NOT NULL COMMENT 'Message author',
  `message` text NOT NULL COMMENT 'Message',
  `message_date` datetime NOT NULL COMMENT 'Message date/time',
  PRIMARY KEY  (`lib_writeboard_message_id`),
  KEY `FK_lib_writeboard_writer` (`writeboard_writer`),
  KEY `FK_lib_writeboard_message_writeboard_id` (`lib_writeboard_id`),
  CONSTRAINT `FK_lib_writeboard_message_writeboard_id` FOREIGN KEY (`lib_writeboard_id`) REFERENCES `lib_writeboard` (`lib_writeboard_id`),
  CONSTRAINT `FK_lib_writeboard_writer` FOREIGN KEY (`writeboard_writer`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
