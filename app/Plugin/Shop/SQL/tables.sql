CREATE TABLE IF NOT EXISTS `shop__categories` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop__items` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` int(20) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `servers` text DEFAULT NULL,
  `commands` text NOT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `category` int(20) NOT NULL,
  `timedCommand` int(1) NOT NULL DEFAULT '0',
  `timedCommand_cmd` text,
  `timedCommand_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop__paypals` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` varchar(20) NOT NULL,
  `money` varchar(20) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop__paysafecards` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `amount` varchar(3) NOT NULL,
  `code` varchar(20) NOT NULL,
  `author` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop__paysafecard_messages` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `to` varchar(50) NOT NULL,
  `type` int(1) NOT NULL,
  `amount` int(3) NOT NULL,
  `added_points` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop__starpasses` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `money` int(20) NOT NULL,
  `idd` int(5) NOT NULL,
  `idp` int(5) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop__vouchers` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1',
  `reduction` int(2) NOT NULL,
  `effective_on` text NOT NULL,
  `limit_per_user` int(10) DEFAULT '0',
  `end_date` datetime NOT NULL DEFAULT '2100-01-01 00:00:01',
  `created` datetime NOT NULL,
  `affich` int(1) NOT NULL DEFAULT '1',
  `used` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
