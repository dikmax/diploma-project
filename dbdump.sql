/*
SQLyog Enterprise - MySQL GUI v7.11 
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Authors';

/*Data for the table `lib_author` */

insert  into `lib_author`(`lib_author_id`,`name`,`description_text_id`,`front_description`,`lib_writeboard_id`) values (1,'Саймак, Клиффорд Доналд',1,'Кли́ффорд До́налд Са́ймак (Clifford Donald Simak) родился 3 августа 1904 года в американском городе Милвилл, штат Висконсин. Его родители — Джон Льюис и Маргарет Саймак. 13 апреля 1929 года он женился на Агнес Каченберг, у них родилось два ребенка, Скотт и Шелли. Саймак учился в Университете Висконсина, но не окончил его. Работал в различных газетах. С 1939 года (по 1976 год) он уже в «Minneapolis Star and Tribune». В них он стал редактором новостей (в «Minneapolis Star») с начала 1949 года и координатором раздела научные публичные серии (в «Minneapolis Tribune») с начала 1961.',3),(2,'New author',1,'',3);

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

/*Data for the table `lib_author_has_title` */

insert  into `lib_author_has_title`(`lib_author_id`,`lib_title_id`) values (1,1);

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

/*Data for the table `lib_author_image` */

insert  into `lib_author_image`(`lib_author_image_id`,`lib_author_id`,`path`,`image_date`) values (1,1,'/public/images/test/simak.jpg','2008-08-25 00:27:13');

/*Table structure for table `lib_author_name` */

DROP TABLE IF EXISTS `lib_author_name`;

CREATE TABLE `lib_author_name` (
  `lib_author_name_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_author_id` int(10) unsigned NOT NULL COMMENT 'Author ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  PRIMARY KEY  (`lib_author_name_id`),
  KEY `FK_lib_author_name` (`lib_author_id`),
  CONSTRAINT `FK_lib_author_name` FOREIGN KEY (`lib_author_id`) REFERENCES `lib_author` (`lib_author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Author different names names';

/*Data for the table `lib_author_name` */

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

/*Data for the table `lib_author_name_index` */

insert  into `lib_author_name_index`(`lib_author_name_index_id`,`word`,`lib_author_id`) values (1,'Clifford',1),(2,'Simak',1);

/*Table structure for table `lib_channel` */

DROP TABLE IF EXISTS `lib_channel`;

CREATE TABLE `lib_channel` (
  `lib_channel_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Name of channel',
  `description` varchar(255) NOT NULL COMMENT 'Description of channel',
  PRIMARY KEY  (`lib_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Different channels (eg. news or blog)';

/*Data for the table `lib_channel` */

insert  into `lib_channel`(`lib_channel_id`,`name`,`description`) values (1,'Имя','Описание');

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

/*Data for the table `lib_channel_item` */

insert  into `lib_channel_item`(`lib_channel_item_id`,`lib_channel_id`,`item_text_id`,`item_date`,`author_id`,`published`) values (1,1,1,'2008-07-16 10:51:50',1,1);

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

/*Data for the table `lib_channel_item_has_tag` */

/*Table structure for table `lib_tag` */

DROP TABLE IF EXISTS `lib_tag`;

CREATE TABLE `lib_tag` (
  `lib_tag_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Tag name',
  PRIMARY KEY  (`lib_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Tags';

/*Data for the table `lib_tag` */

/*Table structure for table `lib_text` */

DROP TABLE IF EXISTS `lib_text`;

CREATE TABLE `lib_text` (
  `lib_text_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_text_revision_id` int(10) unsigned NOT NULL COMMENT 'Revision ID',
  `cdate` datetime NOT NULL COMMENT 'Date of creation',
  PRIMARY KEY  (`lib_text_id`),
  KEY `FK_lib_text_rev_text` (`lib_text_revision_id`),
  CONSTRAINT `FK_lib_text_rev_text` FOREIGN KEY (`lib_text_revision_id`) REFERENCES `lib_text_revision` (`lib_text_revision_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Table contains text which need revisioning';

/*Data for the table `lib_text` */

insert  into `lib_text`(`lib_text_id`,`lib_text_revision_id`,`cdate`) values (1,6,'2008-07-16 10:51:50');

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Text revisions';

/*Data for the table `lib_text_revision` */

insert  into `lib_text_revision`(`lib_text_revision_id`,`lib_text_id`,`lib_text_revision_content_id`,`mdate`,`revision`,`author_id`,`changes`) values (1,1,1,'2008-07-16 10:51:50',1,1,'First revision'),(2,NULL,2,'2008-08-31 19:39:04',1,1,'Update text'),(3,NULL,3,'2008-08-31 19:44:28',1,1,'Update text'),(4,NULL,4,'2008-08-31 19:44:48',1,1,'Update text'),(6,1,12,'2008-08-31 19:57:36',2,1,'Update text');

/*Table structure for table `lib_text_revision_content` */

DROP TABLE IF EXISTS `lib_text_revision_content`;

CREATE TABLE `lib_text_revision_content` (
  `lib_text_revision_content_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `content` longtext NOT NULL COMMENT 'Revision content',
  PRIMARY KEY  (`lib_text_revision_content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Revision text data';

/*Data for the table `lib_text_revision_content` */

insert  into `lib_text_revision_content`(`lib_text_revision_content_id`,`content`) values (1,'Тест'),(2,'Тест\r\nИ еще тест'),(3,'Тест\r\nИ еще тест +1'),(4,'Тест\r\nИ еще тест'),(12,'Кли́ффорд До́налд Са́ймак (Clifford Donald Simak) родился 3 августа 1904 года в американском городе Милвилл, штат Висконсин. Его родители — Джон Льюис и Маргарет Саймак. 13 апреля 1929 года он женился на Агнес Каченберг, у них родилось два ребенка, Скотт и Шелли. Саймак учился в Университете Висконсина, но не окончил его. Работал в различных газетах. С 1939 года (по 1976 год) он уже в «Minneapolis Star and Tribune». В них он стал редактором новостей (в «Minneapolis Star») с начала 1949 года и координатором раздела научные публичные серии (в «Minneapolis Tribune») с начала 1961.');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `lib_title` */

insert  into `lib_title`(`lib_title_id`,`name`,`authors_index`,`description_text_id`,`front_description`,`lib_writeboard_id`) values (1,'Город','Клиффорд Саймак#clifford_simak',1,'City',4);

/*Table structure for table `lib_user` */

DROP TABLE IF EXISTS `lib_user`;

CREATE TABLE `lib_user` (
  `lib_user_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `login` varchar(255) NOT NULL COMMENT 'User login',
  `password` varchar(32) NOT NULL COMMENT 'MD5 of user password',
  `registration_date` datetime NOT NULL COMMENT 'User registration date',
  `login_date` datetime NOT NULL COMMENT 'User last login date',
  `login_ip` varchar(15) NOT NULL default '0.0.0.0' COMMENT 'User last login IP',
  `lib_writeboard_id` int(10) unsigned NOT NULL COMMENT 'User personal writeboard',
  PRIMARY KEY  (`lib_user_id`),
  UNIQUE KEY `user_login` (`login`(10)),
  KEY `FK_lib_user_writeboard_id` (`lib_writeboard_id`),
  CONSTRAINT `FK_lib_user_writeboard_id` FOREIGN KEY (`lib_writeboard_id`) REFERENCES `lib_writeboard` (`lib_writeboard_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='User accounts';

/*Data for the table `lib_user` */

insert  into `lib_user`(`lib_user_id`,`login`,`password`,`registration_date`,`login_date`,`login_ip`,`lib_writeboard_id`) values (1,'dikmax','77122cb39a3aa48e3a6ff8df64aa93b9','2008-07-16 11:26:52','2008-07-16 11:28:18','127.0.0.1',1),(2,'dikmax2','77122cb39a3aa48e3a6ff8df64aa93b9','2008-08-23 02:21:05','2008-08-23 02:21:05','0.0.0.0',2);

/*Table structure for table `lib_user_bookshelf` */

DROP TABLE IF EXISTS `lib_user_bookshelf`;

CREATE TABLE `lib_user_bookshelf` (
  `lib_user_bookshelf_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_user_id` int(10) unsigned NOT NULL COMMENT 'User ID',
  `lib_title_id` int(10) unsigned NOT NULL COMMENT 'Title ID',
  `relation` int(11) NOT NULL COMMENT 'Don''t know yet',
  PRIMARY KEY  (`lib_user_bookshelf_id`),
  KEY `FK_lib_user_bookshelf_user` (`lib_user_id`),
  KEY `FK_lib_user_bookshelf_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_user_bookshelf_title` FOREIGN KEY (`lib_title_id`) REFERENCES `lib_title` (`lib_title_id`),
  CONSTRAINT `FK_lib_user_bookshelf_user` FOREIGN KEY (`lib_user_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `lib_user_bookshelf` */

insert  into `lib_user_bookshelf`(`lib_user_bookshelf_id`,`lib_user_id`,`lib_title_id`,`relation`) values (1,1,1,0);

/*Table structure for table `lib_user_data` */

DROP TABLE IF EXISTS `lib_user_data`;

CREATE TABLE `lib_user_data` (
  `lib_user_data_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_user_id` int(10) unsigned NOT NULL COMMENT 'User ID',
  `variable` varchar(255) NOT NULL COMMENT 'Additional data name',
  `value` text NOT NULL COMMENT 'Additional data value',
  PRIMARY KEY  (`lib_user_data_id`),
  KEY `FK_lib_user_data_user` (`lib_user_id`),
  CONSTRAINT `FK_lib_user_data_user` FOREIGN KEY (`lib_user_id`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Additional data for user profiles (reserved for future usage';

/*Data for the table `lib_user_data` */

/*Table structure for table `lib_writeboard` */

DROP TABLE IF EXISTS `lib_writeboard`;

CREATE TABLE `lib_writeboard` (
  `lib_writeboard_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `owner_description` varchar(50) NOT NULL COMMENT 'String with data to whom writeboard belongs',
  PRIMARY KEY  (`lib_writeboard_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `lib_writeboard` */

insert  into `lib_writeboard`(`lib_writeboard_id`,`owner_description`) values (1,'User 1'),(2,'User 2'),(3,'Author 1'),(4,'Title 1');

/*Table structure for table `lib_writeboard_message` */

DROP TABLE IF EXISTS `lib_writeboard_message`;

CREATE TABLE `lib_writeboard_message` (
  `lib_writeboard_message_id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lib_writeboard_id` int(10) unsigned NOT NULL COMMENT 'Writeboard ID',
  `writeboard_writer` int(10) unsigned NOT NULL COMMENT 'Message author',
  `message` text NOT NULL COMMENT 'Message',
  `message_date` datetime NOT NULL COMMENT 'Message date/time',
  PRIMARY KEY  (`lib_writeboard_message_id`),
  KEY `FK_lib_writeboard_writer` (`writeboard_writer`),
  KEY `FK_lib_writeboard_message_writeboard_id` (`lib_writeboard_id`),
  CONSTRAINT `FK_lib_writeboard_message_writeboard_id` FOREIGN KEY (`lib_writeboard_id`) REFERENCES `lib_writeboard` (`lib_writeboard_id`),
  CONSTRAINT `FK_lib_writeboard_writer` FOREIGN KEY (`writeboard_writer`) REFERENCES `lib_user` (`lib_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `lib_writeboard_message` */

insert  into `lib_writeboard_message`(`lib_writeboard_message_id`,`lib_writeboard_id`,`writeboard_writer`,`message`,`message_date`) values (1,1,1,'Test message 1','2008-08-10 22:21:51'),(2,1,1,'Test message 2','2008-08-20 22:22:07'),(3,1,1,'test','2008-08-21 00:45:33'),(4,1,1,'test2','2008-08-21 01:13:48'),(5,1,2,'Сообщение от друга','2008-08-23 03:17:34'),(7,1,1,'asdf','2008-08-24 00:43:20'),(8,3,1,'Good author','2008-08-27 22:10:40'),(9,3,1,'Еще одно сообщение','2008-08-27 22:12:39');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
