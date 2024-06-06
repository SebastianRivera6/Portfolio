CREATE TABLE IF NOT EXISTS `failures_to_deliver`(
  `hist_date` date NOT NULL,
  `stock_ticker` char(4) NOT NULL,
  `failures_to_deliver` int(11) NOT NULL,
  KEY `stock_ticker` (`stock_ticker`),
  CONSTRAINT `failures_to_deliver_ibfk_2` FOREIGN KEY (`stock_ticker`) REFERENCES `tickers` (`stock_ticker`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `favorite_stocks` (
  `user_id` int(10) unsigned NOT NULL,
  `favorite_ticker` char(4) NOT NULL,
  KEY `user_id` (`user_id`),
  CONSTRAINT `favorite_stocks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `price_history` (
  `hist_date` date NOT NULL,
  `stock_ticker` char(4) NOT NULL,
  `volume` int(11) NOT NULL,
  `opening_price` int(11) NOT NULL,
  `closing_price` int(11) NOT NULL,
  `high_price` int(11) NOT NULL,
  `low_price` int(11) NOT NULL,
  KEY `stock_ticker` (`stock_ticker`),
  CONSTRAINT `price_history_ibfk_1` FOREIGN KEY (`stock_ticker`) REFERENCES `tickers` (`stock_ticker`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `tickers` (
  `stock_ticker` char(4) NOT NULL,
  `ticker_description` varchar(50) NOT NULL,
  PRIMARY KEY (`stock_ticker`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_email` char(50) NOT NULL,
  `user_password` varchar(250) NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
