DROP TABLE IF EXISTS `software`;
CREATE TABLE `software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `url_regex` varchar(255) NOT NULL,
  `regex` varchar(255) NOT NULL,
  `error` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `version`;
CREATE TABLE `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `software_id` int(11) NOT NULL,
  `number` varchar(25) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `software_id` (`software_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

