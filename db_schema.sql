-- MySQL dump 10.11
--
-- Host: localhost    Database: ircplanet_services
-- ------------------------------------------------------
-- Server version	5.0.58-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `info_line` varchar(150) NOT NULL default '',
  `register_ts` int(11) NOT NULL default '0',
  `lastseen_ts` int(11) NOT NULL default '0',
  `suspend` tinyint(1) NOT NULL default '0',
  `no_purge` tinyint(1) NOT NULL default '0',
  `auto_op` tinyint(1) NOT NULL default '1',
  `auto_voice` tinyint(1) NOT NULL default '1',
  `enforce_nick` tinyint(1) NOT NULL default '0',
  `create_date` datetime default NULL,
  `update_date` datetime default NULL,
  PRIMARY KEY  (`account_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=121 DEFAULT CHARSET=latin1 PACK_KEYS=1 COMMENT='User accounts';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `channel_access`
--

DROP TABLE IF EXISTS `channel_access`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `channel_access` (
  `access_id` int(10) unsigned NOT NULL auto_increment,
  `chan_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `level` smallint(6) NOT NULL default '0',
  `suspend` tinyint(1) NOT NULL default '0',
  `protect` tinyint(1) NOT NULL default '0',
  `auto_op` tinyint(1) NOT NULL default '1',
  `auto_voice` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`access_id`),
  KEY `chan_id` (`chan_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=latin1 COMMENT='Channel Access Records';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `channel_bans`
--

DROP TABLE IF EXISTS `channel_bans`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `channel_bans` (
  `ban_id` int(10) unsigned NOT NULL auto_increment,
  `chan_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `set_ts` int(11) NOT NULL default '0',
  `expire_ts` int(11) NOT NULL default '0',
  `level` smallint(6) NOT NULL default '0',
  `mask` varchar(100) NOT NULL default '',
  `reason` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`ban_id`),
  KEY `chan_id` (`chan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `channels`
--

DROP TABLE IF EXISTS `channels`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `channels` (
  `channel_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `register_ts` int(11) NOT NULL default '0',
  `create_ts` int(11) NOT NULL default '0',
  `register_date` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `purpose` varchar(200) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `def_topic` varchar(255) NOT NULL default '',
  `def_modes` varchar(20) NOT NULL default 'nt',
  `info_lines` tinyint(1) NOT NULL default '0',
  `suspend` tinyint(1) NOT NULL default '0',
  `no_purge` tinyint(1) NOT NULL default '0',
  `auto_op` tinyint(1) NOT NULL default '1',
  `auto_op_all` tinyint(1) NOT NULL default '0',
  `auto_voice` tinyint(1) NOT NULL default '0',
  `auto_voice_all` tinyint(1) NOT NULL default '0',
  `auto_limit` tinyint(1) NOT NULL default '0',
  `auto_limit_buffer` tinyint(4) NOT NULL default '5',
  `auto_limit_wait` tinyint(4) NOT NULL default '30',
  `strict_op` tinyint(1) NOT NULL default '0',
  `strict_voice` tinyint(1) NOT NULL default '0',
  `strict_modes` tinyint(1) NOT NULL default '0',
  `strict_topic` tinyint(1) NOT NULL default '0',
  `no_op` tinyint(1) NOT NULL default '0',
  `no_voice` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`channel_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=latin1 COMMENT='Registered Channels';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cs_admins`
--

DROP TABLE IF EXISTS `cs_admins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cs_admins` (
  `user_id` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Nickserv Admins';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `help`
--

DROP TABLE IF EXISTS `help`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `help` (
  `service` varchar(5) NOT NULL default '',
  `minlevel` int(11) NOT NULL default '0',
  `topic` varchar(20) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`service`,`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Commands Help';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ns_admins`
--

DROP TABLE IF EXISTS `ns_admins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ns_admins` (
  `user_id` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Nickserv Admins';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `os_admins`
--

DROP TABLE IF EXISTS `os_admins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `os_admins` (
  `user_id` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Operserv Admins';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `stats_channel_users`
--

DROP TABLE IF EXISTS `stats_channel_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `stats_channel_users` (
  `channel_name` varchar(255) NOT NULL,
  `nick` varchar(15) NOT NULL,
  `is_op` smallint(5) unsigned NOT NULL,
  `is_voice` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `stats_channels`
--

DROP TABLE IF EXISTS `stats_channels`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `stats_channels` (
  `channel_name` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `modes` varchar(45) NOT NULL,
  PRIMARY KEY  (`channel_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `stats_history`
--

DROP TABLE IF EXISTS `stats_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `stats_history` (
  `date` datetime NOT NULL,
  `servers` int(10) unsigned NOT NULL,
  `users` int(10) unsigned NOT NULL,
  `channels` int(10) unsigned NOT NULL,
  `accounts` int(10) unsigned NOT NULL,
  `opers` int(10) unsigned NOT NULL,
  `services` int(10) unsigned NOT NULL,
  `service_servers` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `stats_servers`
--

DROP TABLE IF EXISTS `stats_servers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `stats_servers` (
  `server_name` varchar(100) NOT NULL,
  `desc` varchar(100) NOT NULL,
  `start_date` datetime NOT NULL,
  `max_users` int(10) unsigned NOT NULL,
  `is_service` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`server_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `stats_users`
--

DROP TABLE IF EXISTS `stats_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `stats_users` (
  `nick` varchar(15) NOT NULL,
  `ident` varchar(15) NOT NULL,
  `host` varchar(80) NOT NULL,
  `name` varchar(100) NOT NULL,
  `server` varchar(60) NOT NULL,
  `modes` varchar(10) NOT NULL,
  `account` varchar(15) NOT NULL,
  `signon_date` datetime NOT NULL,
  PRIMARY KEY  (`nick`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `web_admins`
--

DROP TABLE IF EXISTS `web_admins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `web_admins` (
  `user_id` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Nickserv Admins';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `web_docs`
--

DROP TABLE IF EXISTS `web_docs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `web_docs` (
  `name` varchar(50) NOT NULL default '',
  `upd_uid` int(11) NOT NULL default '0',
  `upd_date` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Web Documents';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `web_news`
--

DROP TABLE IF EXISTS `web_news`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `web_news` (
  `news_id` int(11) NOT NULL auto_increment,
  `create_ts` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `body` text NOT NULL,
  PRIMARY KEY  (`news_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-11-18  7:39:18
