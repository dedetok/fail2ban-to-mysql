-- Host: localhost    Database: myf2b

--
-- Table structure for table `kci_category`
--

DROP TABLE IF EXISTS `kci_category`;
CREATE TABLE `kci_category` (
  `id` int(11) NOT NULL,
  `category` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kci_category`
--

LOCK TABLES `kci_category` WRITE;
INSERT INTO `kci_category` VALUES (10,'SSH'),(20,'FTP'),(30,'HTTP/HTTPS'),(40,'SMTP/POP/IMAP/POP3/S'),(99,'TEST');
UNLOCK TABLES;

--
-- Table structure for table `kci_logipv4`
--

DROP TABLE IF EXISTS `kci_logipv4`;
CREATE TABLE `kci_logipv4` (
  `logdate` datetime DEFAULT NULL,
  `logipv4` int(11) unsigned DEFAULT NULL,
  `logmsg` varchar(1000) DEFAULT NULL,
  `kci_category` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codecontinent` char(2),
  `codecountry2` char(2), 
  `codecountry3` char(3),
  PRIMARY KEY (`id`),
  KEY `fk_kci_category` (`kci_category`),
  CONSTRAINT `kci_logipv4_ibfk_2` FOREIGN KEY (`kci_category`) REFERENCES `kci_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
