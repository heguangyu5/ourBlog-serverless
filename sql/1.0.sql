CREATE DATABASE `ourblog`;

USE `ourblog`;

CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Linux'),
(2, 'Apache'),
(3, 'PHP'),
(4, 'MySQL'),
(5, 'Javascript'),
(6, 'Misc');

CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `category` int(10) unsigned NOT NULL,
  `title` varchar(500) NOT NULL,
  `content` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT '2017-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `password` char(32) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `user` (`uid`, `email`, `password`) VALUES
(1, 'heguangyu5@qq.com', '6abb1bd6591204f34d0479fc512c7316');

CREATE USER ourblog@localhost IDENTIFIED BY 'thisisourblog';
GRANT INSERT,UPDATE,DELETE,SELECT ON ourblog.* TO ourblog@localhost;
