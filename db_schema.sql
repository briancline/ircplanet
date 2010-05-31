-- MySQL dump 10.13  Distrib 5.1.43, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: ircplanet_services
-- ------------------------------------------------------
-- Server version	5.1.43

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `info_line` varchar(150) NOT NULL DEFAULT '',
  `fakehost` varchar(100) NOT NULL,
  `register_ts` int(11) NOT NULL DEFAULT '0',
  `lastseen_ts` int(11) NOT NULL DEFAULT '0',
  `suspend` tinyint(1) NOT NULL DEFAULT '0',
  `no_purge` tinyint(1) NOT NULL DEFAULT '0',
  `auto_op` tinyint(1) NOT NULL DEFAULT '1',
  `auto_voice` tinyint(1) NOT NULL DEFAULT '1',
  `enforce_nick` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`account_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1 COMMENT='User accounts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `channel_access`
--

DROP TABLE IF EXISTS `channel_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_access` (
  `access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chan_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `level` smallint(6) NOT NULL DEFAULT '0',
  `suspend` tinyint(1) NOT NULL DEFAULT '0',
  `protect` tinyint(1) NOT NULL DEFAULT '0',
  `auto_op` tinyint(1) NOT NULL DEFAULT '1',
  `auto_voice` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`access_id`),
  KEY `chan_id` (`chan_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Channel Access Records';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `channel_bans`
--

DROP TABLE IF EXISTS `channel_bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_bans` (
  `ban_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chan_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `set_ts` int(11) NOT NULL DEFAULT '0',
  `expire_ts` int(11) NOT NULL DEFAULT '0',
  `level` smallint(6) NOT NULL DEFAULT '0',
  `mask` varchar(100) NOT NULL DEFAULT '',
  `reason` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`ban_id`),
  KEY `chan_id` (`chan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `channels`
--

DROP TABLE IF EXISTS `channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channels` (
  `channel_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `register_ts` int(11) NOT NULL DEFAULT '0',
  `create_ts` int(11) NOT NULL DEFAULT '0',
  `register_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `purpose` varchar(200) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `def_topic` varchar(255) NOT NULL DEFAULT '',
  `def_modes` varchar(20) NOT NULL DEFAULT 'nt',
  `info_lines` tinyint(1) NOT NULL DEFAULT '0',
  `suspend` tinyint(1) NOT NULL DEFAULT '0',
  `no_purge` tinyint(1) NOT NULL DEFAULT '0',
  `auto_op` tinyint(1) NOT NULL DEFAULT '1',
  `auto_op_all` tinyint(1) NOT NULL DEFAULT '0',
  `auto_voice` tinyint(1) NOT NULL DEFAULT '0',
  `auto_voice_all` tinyint(1) NOT NULL DEFAULT '0',
  `auto_limit` tinyint(1) NOT NULL DEFAULT '0',
  `auto_limit_buffer` tinyint(4) NOT NULL DEFAULT '5',
  `auto_limit_wait` tinyint(4) NOT NULL DEFAULT '30',
  `strict_op` tinyint(1) NOT NULL DEFAULT '0',
  `strict_voice` tinyint(1) NOT NULL DEFAULT '0',
  `strict_modes` tinyint(1) NOT NULL DEFAULT '0',
  `strict_topic` tinyint(1) NOT NULL DEFAULT '0',
  `no_op` tinyint(1) NOT NULL DEFAULT '0',
  `no_voice` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`channel_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Registered Channels';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cs_admins`
--

DROP TABLE IF EXISTS `cs_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_admins` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Nickserv Admins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cs_badchans`
--

DROP TABLE IF EXISTS `cs_badchans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_badchans` (
  `badchan_id` int(11) NOT NULL AUTO_INCREMENT,
  `chan_mask` varchar(50) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`badchan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='CS Bad Channels';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ds_admins`
--

DROP TABLE IF EXISTS `ds_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ds_admins` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Defense Service Admins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ds_whitelist`
--

DROP TABLE IF EXISTS `ds_whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ds_whitelist` (
  `whitelist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mask` varchar(200) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`whitelist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help`
--

DROP TABLE IF EXISTS `help`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help` (
  `service` varchar(5) NOT NULL DEFAULT '',
  `minlevel` int(11) NOT NULL DEFAULT '0',
  `topic` varchar(20) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`service`,`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Commands Help';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help`
--

LOCK TABLES `help` WRITE;
/*!40000 ALTER TABLE `help` DISABLE KEYS */;
INSERT INTO `help` VALUES 
('CS',0,'access','Displays access level information for users having access on\r\nthe specified channel. The search mask can either be a service\r\naccount name or a search mask (where * and ? are wildcards).\r\n\r\n%BExample:%B  /msg %N access #southpole brian\r\n          /msg %N access #southpole br*\r\n          /msg %N access #southpole *'),
('CS',0,'adduser','Grants a user access to the channel. You may optionally\r\nspecify the user\'s level. If no level is provided, the\r\ndefault of 100 is used.\r\n'),
('CS',0,'ban','Sets a ban in the specified channel. An optional ban duration,\r\naccess level, and reason may be provided.\r\n\r\nDurations must use the following format: %B<number><unit>%B\r\nThe unit can be any of the following and can be combined as\r\nlong as larger units come first.\r\n   %Bw%B - weeks\r\n   %Bd%B - days\r\n   %Bh%B - hours\r\n   %Bm%B - minutes\r\n   %Bs%B - seconds\r\n\r\nExamples:\r\n   %B2w%B    - Two weeks.\r\n   %B5d12h%B - Five days and twelve hours.\r\n   %B90s%B   - Ninety seconds.\r\n\r\nAn access level can be provided to prevent other users with\r\nlower access levels from removing the ban. For example, a ban\r\nwith a level of 200 would prevent anyone with less than level\r\n200 access from removing the ban. You cannot use a level\r\nthat is higher than your own.\r\n\r\n%BExamples:%B  /msg %N ban #southpole *!*flooder@*.aol.com\r\n           /msg %N ban #southpole *!*@*.ca 5w 200 No canadians.\r\n           /msg %N ban #southpole flooder 30d'),
('CS',0,'banlist','Shows the channel\'s current ban list. An optional search mask\r\nmay be used in order to find information on a specific ban.\r\n\r\n%BExamples:%B  /msg %N banlist #southpole *flooder*\r\n           /msg %N banlist #southpole *@aol.com'),
('CS',0,'chaninfo','Displays various channel settings for the specified channel, if it \r\nis registered.\r\n\r\n%BExample:%B  /msg %N chaninfo #southpole'),
('CS',0,'clearbans','Clears all bans in the channel.\r\n'),
('CS',0,'clearmodes','Clears all modes in the specified channel.'),
('CS',0,'deop','Removes op status from the specified user(s) in the channel.\r\nIf no nicks are provided, %N will deop you.'),
('CS',0,'deopall','Removes op status from every user in the specified channel.\r\n'),
('CS',0,'devoice','Removes a voice from the specified user(s) in the channel. If\r\nno nicks are provided, %N will devoice you.'),
('CS',0,'devoiceall','Removes voices from every user in the specified channel.'),
('CS',0,'do','Has the bot perform a public action (/me) in the channel.'),
('CS',0,'help','%N is a channel service designed to help users maintain\r\nownership and control over their channels. Channels are\r\nregistered on a first-come, first-served basis. Users can\r\nregister up to %MAX_CHAN_REGS% channels with the %Bregister%B command.\r\n\r\nTo get a list of commands that are available to you, simply\r\ntype %Bshowcommands%B. To get a list of commands available\r\nto you on a specific channel, type %Bshowcommands #channel%B.\r\n\r\nIf you wish to look up usage information about a command,\r\ntype %Bhelp commandname%B, where commandname is the command\r\nyou need help with.'),
('CS',0,'invite','Invites the specified user(s) to the channel. If no nicks are\r\nspecified, %N will only invite you.\r\n\r\n%BExamples:%B  invite #southpole brian tsunam1\r\n           invite #southpole'),
('CS',0,'kick','Kicks the specified user from the channel. An optional reason\r\nmay be specified for the kick.'),
('CS',0,'kickall','Kicks all users from the specified channel. The user issuing this\r\ncommand will not be kicked.\r\n'),
('CS',0,'kickban','Kicks and bans the specified user from the channel. A hostmask\r\nmay be specified instead of a nickname, in which case %N\r\nwill kick and ban all users matching that mask.\r\n\r\nBans set as a result of this command will last for one hour,\r\nand will have a level of 75.\r\n\r\n%BExamples:%B  kickban #southpole lamer\r\n           kickban #southpole whoremonger Quiet please.\r\n           kickban #southpole *!*@*.aol.com Down with AOL.'),
('CS',0,'kickbanall','Deops, kicks and bans all users from the specified channel. The user\r\nissuing this command will not be kicked or banned.\r\n'),
('CS',0,'mode','Sets the specified mode(s) in the channel.'),
('CS',0,'moderate','Moderates the channel (+m) and grants voice status to every\r\nnon-op.\r\n'),
('CS',0,'moduser autoop','%BSyntax:%B moduser <channel> <username> autoop [on|off]\r\n\r\nSets whether the specified user will be auto-opped on the channel\r\nonce they have logged in to their account and joined the channel.\r\n\r\nOmitting the ON or OFF parameter will cause the user\'s existing\r\nauto-op setting to be toggled.\r\n\r\n%BExample:%B  /msg %N moduser #southpole brian autoop on\r\n          /msg %N moduser #southpole brian autoop'),
('CS',0,'moduser autovoice','%BSyntax:%B moduser <channel> <username> autovoice [on|off]\r\n\r\nSets whether the specified user will be auto-voiced on the channel\r\nonce they have logged in to their account and joined the channel.\r\n\r\nOmitting the ON or OFF parameter will cause the user\'s existing\r\nauto-voice setting to be toggled.\r\n\r\n%BExample:%B  /msg %N moduser #southpole brian autovoice on\r\n          /msg %N moduser #southpole brian autovoice'),
('CS',0,'moduser level','%BSyntax:%B moduser <channel> <username> level <level>\r\n\r\nChanges the specified user\'s access level on the channel. Access \r\nlevels range from 1 to 500, and you may only set someone\'s access\r\nlevel to be less than your own.\r\n\r\n%BExample:%B  /msg %N moduser #southpole tsunam1 level 499'),
('CS',0,'moduser protect','%BSyntax:%B moduser <channel> <username> protect [on|off]\r\n\r\nSets whether the specified user will be protected from kicks, bans,\r\ndeops, and devoices on the channel that are performed by persons\r\nwith either no access, or lesser access.\r\n\r\nOmitting the ON or OFF parameter will cause the user\'s existing\r\nprotection setting to be toggled.\r\n\r\n%BExample:%B  /msg %N moduser #southpole brian protect on\r\n          /msg %N moduser #southpole brian protect'),
('CS',0,'moduser','Modifies a user\'s access record on the channel. The following\r\naccess settings may be modified with this command:\r\n\r\n%BLEVEL%B [new level]      Sets the user\'s access level.\r\n%BAUTOOP%B [on|off]        Toggles the user\'s autoop flag.\r\n%BAUTOVOICE%B [on|off]     Toggles the user\'s autovoice flag.\r\n\r\nNote: The autoop and autovoice flags may be ignored if the\r\nuser turns them off for their user account, or if the channel\r\nautoop and autovoice flags are turned off.\r\n\r\n%BExamples:%B  moduser #southpole tsunam1 level 499\r\n           moduser #southpole brian autoop on\r\n'),
('CS',0,'op','Grants op status to the specified user(s) in the channel. If\r\nno nicks are provided, %N will op you.'),
('CS',0,'opall','Grants op status to every user in the specified channel.\r\n'),
('CS',0,'part','Causes %N to part the specified channel.\r\n\r\n%BExample:%B  /msg %N part #southpole'),
('CS',0,'quote','Sends the specified line as raw text to %S\'s uplink.\r\n\r\n%BUSE WITH CAUTION. RAW TEXT IS NOT PARSED FOR ACCURACY NOR IS ITS%B\r\n%BEFFECTS PERSISTED IN MEMORY BEFORE BEING SENT ACROSS THE WIRE.%B\r\n\r\n%BYOU SHOULD BE WELL-VERSED IN RAW P10 PROTOCOL BEFORE USING THIS.%B'),
('CS',0,'rdefmodes','Resets the channel modes to the default. Use the %Bset defmodes%B\r\ncommand to set the default channel modes.\r\n'),
('CS',0,'rdeftopic','Resets the topic to the default topic. Use the %Bset deftopic%B\r\ncommand to set the default topic.'),
('CS',0,'register','Registers a channel with the specified purpose. You may \r\nregister up to %MAX_CHAN_REGS% channels.'),
('CS',0,'remuser','Removes a user\'s access from the channel. You may use the \r\n%Baccess%B command to see who has channel access.\r\n'),
('CS',0,'say','Has the bot speak publicly in the channel.'),
('CS',0,'set autolimit','%BSyntax:%B set <channel> autolimit [on|off]\r\n\r\nToggles automatic size limiting for the channel. If this\r\nsetting is enabled, %N will automatically set a size limit\r\non the channel approximately N seconds after a user joins,\r\nwhere N is the number of seconds specified by the \r\nAUTOLIMITWAIT setting. The size limit it sets will be\r\nequivalent to the number of users in the channel plus Z,\r\nwhere Z is the number specified by the AUTOLIMITBUFFER\r\nsetting.\r\n\r\nFor example, assume the following settings for #southpole:\r\n     autolimit:        on\r\n     autolimitwait:    30\r\n     autolimitbuffer:  5\r\n\r\n30 seconds after a user joins #southpole, %N will wait 30\r\nseconds before setting a limit of N+5, where N is the current\r\nnumber of users in the channel.\r\n\r\nIf a user joins #southpole 30 seconds after the first user,\r\n%N will wait an additional 30 seconds before setting the\r\nnew limit.\r\n\r\n%BExamples:%B  /msg %N set #southpole autolimit on\r\n           /msg %N set #southpole autolimit off'),
('CS',0,'set autolimitbuffer','%BSyntax:%B set <channel> autolimitbuffer <number>\r\n\r\nSets the auto limit buffer, or the number that will be added\r\nto the current channel user count when %N sets an automatic\r\nlimit. The buffer must be between %MIN_CHAN_AUTOLIMIT_BUFFER% and %MAX_CHAN_AUTOLIMIT_BUFFER%.\r\n\r\n%BExample:%B  set #southpole autolimitbuffer 5\r\n\r\nFor more information, use %Bhelp set autolimit%B.'),
('CS',0,'set autolimitwait','%BSyntax:%B set <channel> autolimitwait <seconds>\r\n\r\nSets the number of seconds that %N will wait before\r\nsetting an auto limit. The number of seconds must\r\nbe between %MIN_CHAN_AUTOLIMIT_WAIT% and %MAX_CHAN_AUTOLIMIT_WAIT% seconds.\r\n\r\n%BExample:%B  set #southpole autolimitwait 60\r\n\r\nFor more information, use %Bhelp set autolimit%B.'),
('CS',0,'set autoop','%BSyntax:%B set <channel> autoop [on|off]\r\n\r\nToggles automatic ops for users who have proper access.\r\nThis setting may be ignored if a user has autoop turned off\r\nfor their user account, or if their channel access has the\r\nautoop flag turned off (see the %Bmoduser%B command).'),
('CS',0,'set autoopall','%BSyntax:%B set <channel> autoopall [on|off]\r\n\r\nToggles automatic ops for everyone that enters the channel,\r\neven if they are not registered with services.'),
('CS',0,'set autovoice','%BSyntax:%B set <channel> autovoice [on|off]\r\n\r\nToggles automatic voices for users who have proper access.\r\nThis setting may be ignored if a user has autovoice turned off\r\nfor their user account, or if their channel access has the\r\nautovoice flag turned off (see the %Bmoduser%B command).'),
('CS',0,'set autovoiceall','%BSyntax:%B set <channel> autovoiceall [on|off]\r\n\r\nToggles automatic voices for everyone that enters the channel,\r\neven if they are not registered with services.'),
('CS',0,'set defmodes','%BSyntax:%B set <channel> defmodes [modes]\r\n\r\nSets the default channel modes, which are automatically set\r\nwhen %N joins the network.\r\n\r\n%BExamples:%B  set defmodes +nt\r\n       set defmodes +ntsk key\r\n       set defmodes +ntl 20\r\n'),
('CS',0,'set deftopic','%BSyntax:%B set <channel> deftopic [topic]\r\n\r\n\r\nSets the default topic, which is automatically set when %N\r\njoins the network.'),
('CS',0,'set infolines','%BSyntax:%B set <channel> infolines [on|off]\r\n\r\nToggles the display of infolines when a registered user with\r\nan infoline joins the channel.'),
('CS',0,'set purpose','%BSyntax:% set <channel> purpose [purpose]\r\n\r\nSets the channel\'s purpose.\r\n'),
('CS',0,'set strictmodes','%BSyntax:%B set <channel> strictmodes [on|off]\r\n\r\nToggles strict modes. If enabled, %N will undo any channel\r\nmode changes that are not performed with the %Bmode%B command.\r\n\r\n%BExample:%B  set #southpole strictmodes on\r\n          set #southpole strictmodes off'),
('CS',0,'set strictop','%BSyntax:%B set <channel> strictop [on|off]\r\n\r\nToggles strict ops. If enabled, %N will always deop any\r\nusers that do not have channel access.\r\n\r\n%BExample:%B  set #southpole strictop on\r\n          set #southpole strictop off'),
('CS',0,'set stricttopic','%BSyntax:%B set <channel> stricttopic [on|off]\r\n\r\nToggles strict topic. If enabled, %N will reset the \r\ntopic if anyone tries to change it without using the %Btopic%B\r\ncommand.\r\n\r\n%BExample:%B  set #southpole stricttopic on\r\n          set #southpole stricttopic off'),
('CS',0,'set strictvoice','%BSyntax:%B set <channel> strictvoice [on|off]\r\n\r\nToggles strict voices. If enabled, %N will always devoice\r\nany users that do not have channel access.\r\n\r\n%BExample:%B  set #southpole strictvoice on\r\n          set #southpole strictvoice off'),
('CS',0,'set url','%BSyntax:%B set <channel> url [address]\r\n\r\nSets the channel\'s web site address.\r\n'),
('CS',0,'set','Changes various channel settings. The following is a list\r\nof all settings that can be changed with this command:\r\n\r\n%BPURPOSE%B            Purpose\r\n%BURL%B                Web site address\r\n%BDEFTOPIC%B           Default topic\r\n%BDEFMODES%B           Default channel modes\r\n%BINFOLINES%B          Display user info lines upon join\r\n%BAUTOOP%B             Enables auto opping\r\n%BAUTOOPALL%B          Automatically ops everyone upon join\r\n%BAUTOVOICE%B          Enables auto voicing\r\n%BAUTOVOICEALL%B       Automatically voices everyone upon join\r\n%BAUTOLIMIT%B          Enables automatic channel limits\r\n%BAUTOLIMITBUFFER%B    Buffer for autolimit\r\n%BAUTOLIMITWAIT%B      Delay for autolimit (in seconds)\r\n%BSTRICTOP%B           Enables strict ops\r\n%BSTRICTVOICE%B        Enables strict voices\r\n%BSTRICTTOPIC%B        Enables strict topic\r\n%BSTRICTMODES%B        Enables strict channel modes\r\n\r\nFor more details on a setting, /msg %N help set <setting>.\r\nFor example: /msg %N help set autoop\r\n'),
('CS',0,'showcommands','Lists all commands available to you.'),
('CS',0,'topic','Sets the topic in the specified channel.'),
('CS',0,'unban','Removes a ban in the specified channel.'),
('CS',0,'unreg','Unregisters the specified channel. All channel settings, access\r\ninformation, and bans will be %Bpermanently deleted.%B\r\n\r\n%BPLEASE USE THIS COMMAND WITH CAUTION.%B\r\n%BDeleted channel data CANNOT be recovered.%B\r\n\r\n%BExample:%B  /msg %N unreg #southpole'),
('CS',0,'uptime','Shows %N\'s running time and bandwidth usage.'),
('CS',0,'verify','Tells whether the specified nickname is a valid channel services\r\nadministrator or representative, or if they are simply a user.\r\n\r\n%BExample:%B  /msg %N verify brian'),
('CS',0,'voice','Grants a voice to the specified user(s) in the channel. If no\r\nnicks are provided, %N will voice you.'),
('CS',0,'voiceall','Grants voices to every user in the specified channel.'),
('CS',501,'addadmin','Adds a user to %N\'s administrator access list with the specified\r\nlevel.\r\n\r\n%BExamples:%B  /msg %N addadmin s1amson 800\r\n\r\n'),
('CS',501,'addbad','Adds a word to the bad words list, which prevents users from\r\nregistering a channel whose name contains that word.\r\n\r\n%BExample:%B  /msg %N addbad warez'),
('CS',501,'adminreg','Registers the specified channel and sets the specified user name as\r\nthe new channel\'s owner. You may optionally specify the channel\'s\r\npurpose.\r\n\r\n%BExample:%B  /msg %N adminreg #help HelpfulGuy Official help channel\r\n          /msg %N adminreg #opers AdminGal'),
('CS',501,'deladmin','Removes a user from %N\'s administrator access list.\r\n\r\n%BExamples:%B  /msg %N deladmin fiddy\r\n\r\n'),
('CS',501,'delchan','Unregisters the specified channel. All channel settings, access\r\ninformation, and bans will be %Bpermanently deleted.%B\r\n\r\n%BPLEASE USE THIS COMMAND WITH CAUTION.%B\r\n%BDeleted channel data CANNOT be recovered.%B\r\n\r\n%BExample:%B  /msg %N delchan #warez'),
('CS',501,'die','Use the %Bdie%B command to terminate the service, effectively\r\nremoving it from the network. You may optionally provide a\r\nreason that will be used in each bot\'s quit message, as well\r\nas the server quit message.\r\n\r\nUse this command carefully.'),
('CS',501,'rembad','Removes a word from the list of words that cannot appear in\r\nregistered channel names.\r\n\r\n%BExample:%B  /msg %N rembad warez'),
('CS',501,'reop','Forces %S to reop %N in any channels where it\r\ndoes not currently have chanop status.'),
('CS',501,'set nopurge','%BSyntax:%B set <channel> nopurge [on|off]\r\n\r\nToggles the channel\'s nopurge setting, which, if turned ON, will\r\nprevent %N from unregistering the channel when any 500-level owner\r\non the channel has not logged in for an extended period of time.'),
('CS',501,'show admins','Displays %N\'s administrator access list with each administrator\'s\r\nname, level, and e-mail address.\r\n\r\n%BExample:%B  /msg %N show admins'),
('CS',501,'show bad','Displays the list of words that are not allowed to appear in \r\nregistered channel names.'),
('CS',501,'show','Displays details on various admin-level settings. The following \r\nis a list of all settings whose details can be shown.\r\n\r\n%BADMINS%B    The list of users with admin-level access to %N.\r\n%BBAD%B       The list of words that are not allowed to appear in\r\n              registered channel names.\r\n\r\n%BExample:%B  /msg %N show admins\r\n\r\nFor more details on a setting, /msg %N help show <setting>.\r\n%BExample:%B  /msg %N help show admins'),

('DS',0,'help','%N is a service designed to protect the network from unwanted\r\nspam and abuse.\r\n\r\nTo get a list of commands that are available to you, simply\r\ntype %B/msg %N showcommands%B.\r\n\r\nIf you wish to look up usage information about a command,\r\ntype %B/msg %N help commandname%B, where commandname is the \r\ncommand you need help with.'),
('DS',0,'showcommands','Lists all commands available to you.'),
('DS',0,'uptime','Show\'s the bot\'s running time and bandwidth usage.'),
('DS',500,'access','Displays access level information for users having access on %N.\r\nAn account name mask or a full account name can be specified.\r\n\r\n%BExamples:%B  /msg %N access brian\r\n           /msg %N access br*\r\n           /msg %N access *'),
('DS',500,'adduser','Adds a user to %N\'s access list with the specified level.\r\n\r\n%BExamples:%B  adduser s1amson 800'),
('DS',500,'addwhite','Adds a nick!user@host mask to the white list of masks that should \r\nbe exempt from scanning and G-lining by %N.\r\n\r\nThis is especially helpful if you have recurring false positives\r\nthat follow a particular nick!user@host pattern.\r\n\r\n%BExamples:%B  /msg %N addwhite *@187.62.90.211\r\n               /msg %N addwhite coderman!*coderman@*\r\n               /msg %N addwhite coderman*@*'),
('DS',500,'inviteme','Invites you to %N\'s command reporting channel.'),
('DS',500,'moduser level','Update\'s the specified user\'s access level on %N.\r\n\r\n%BExample:%B  /msg %N moduser s1amson level 900'),
('DS',500,'moduser','Allows you to change settings for a user in %N\'s access list.\r\nThe list of allowed settings are as follows:\r\n\r\n%BLEVEL%B      Change the user\'s access level.\r\n\r\nFor more information on a specific setting, please use\r\n/msg %N help moduser <setting>'),
('DS',500,'quote','Sends the specified line as raw text to %S\'s uplink.\r\n\r\n%BUSE WITH CAUTION. RAW TEXT IS NOT PARSED FOR ACCURACY NOR IS ITS%B\r\n%BEFFECTS PERSISTED IN MEMORY BEFORE BEING SENT ACROSS THE WIRE.%B\r\n\r\n%BYOU SHOULD BE WELL-VERSED IN RAW P10 PROTOCOL BEFORE USING THIS.%B'),
('DS',500,'remuser','Removes a user from %N\'s access list.\r\n\r\n%BExamples:%B  /msg %N remuser fiddy'),
('DS',500,'remwhite','Removes a mask from the white list of nick!user@host masks that \r\nshould be exempt from scanning and G-lining by %N.\r\n\r\n%BExample:%B  /msg %N remwhite *@187.62.90.211\r\n'),
('DS',500,'showwhite','Shows all nick!user@host masks that are on the white list of masks \r\nthat should be exempt from scanning and G-lining by %N.'),
('DS',500,'die','Use the %Bdie%B command to terminate the service, effectively\r\nremoving it from the network. You may optionally provide a\r\nreason that will be used in each bot\'s quit message, as well\r\nas the server quit message.\r\n\r\nUse this command carefully.'),

('NS',0,'ghost','Logs you in to your account on %N, and issues a KILL against\r\nsomeone who is using your nick, or your ghost if you were \r\ndisconnected and it has not timed out yet. Note that you %Bmust%B \r\nuse this command securely.\r\n\r\nIf you do not provide an account name and password, %N will assume \r\nyou want to ghost your current logged-in account name.\r\n\r\n\r\n%BExample:%B  /msg %N@%S ghost myaccount myPassword'),
('NS',0,'help','%N is a nickname and account service designed as a central\r\nauthentication point for all other network services.\r\n\r\nTo get a list of commands that are available to you, simply\r\ntype %B/msg %N showcommands%B.\r\n\r\nIf you wish to look up usage information about a command,\r\ntype %B/msg %N help commandname%B, where commandname is the command\r\nyou need help with.'),
('NS',0,'info','Displays information about the specified account name.\r\n\r\n%BExample:%B\r\n  /msg %N info joebob'),
('NS',0,'inviteme','Invites you to %N\'s command reporting channel.'),
('NS',0,'login','Logs you in to your account on %N. Note that you %Bmust%B use\r\nthis command securely. If you do not provide an account name,\r\n%N will assume the account name is the same as your current\r\nnickname.\r\n\r\nFor example, if your nick is JoeBob, the below line would log\r\nyou in to the account JoeBob:\r\n  /msg %N@%S login myPassword\r\n\r\nIf your nick is taken, or your account name is different than\r\nyour nickname, you can specify an account to log in to:\r\n  /msg %N@%S login myaccount myPassword\r\n\r\nThe only way to log out of your account is by using /quit.'),
('NS',0,'quote','Sends the specified line as raw text to %S\'s uplink.\r\n\r\n%BUSE WITH CAUTION. RAW TEXT IS NOT PARSED FOR ACCURACY NOR IS ITS%B\r\n%BEFFECTS PERSISTED IN MEMORY BEFORE BEING SENT ACROSS THE WIRE.%B\r\n\r\n%BYOU SHOULD BE WELL-VERSED IN RAW P10 PROTOCOL BEFORE USING THIS.%B'),
('NS',0,'register','Creates an account that you can use on all network services.\r\nYou must provide a password and valid e-mail address.\r\n\r\n%BExample:%B\r\n  /%N register myPassword ircuser@myemail.com\r\n\r\nUpon registering, you will be automagically logged in.'),
('NS',0,'set autoop','%BSyntax:%B set autoop [on|off]\r\n\r\nUpdates the auto op flag on your account, which will prevent you\r\nfrom being auto-opped in any channels where you have sufficient\r\naccess.'),
('NS',0,'set autovoice','%BSyntax:%B set autovoice [on|off]\r\n\r\nUpdates the auto voice flag on your account, which will prevent you\r\nfrom being auto-voiced in any channels where you have sufficient\r\naccess.'),
('NS',0,'set email','%BSyntax:%B set email <address>\r\n\r\nUpdates the e-mail address on your account.\r\n'),
('NS',0,'set enforce','%BSyntax:%B set enforce [on|off]\r\n\r\nWhen toggled on, will cause %N to issue a KILL to anyone who \r\nattempts to use your nick without logging in to %N within a\r\nshort amount of time.'),
('NS',0,'set info','%BSyntax:%B set info [text]\r\n\r\nUpdates the info line on your account, which will be displayed\r\nevery time you enter a channel where you have access.\r\n\r\nNote that the channel owner must enable the infolines option\r\nin order for any user\'s info line to be displayed upon joining.\r\n'),
('NS',0,'showcommands','Lists all commands available to you.'),
('NS',0,'uptime','Show\'s the bot\'s running time and bandwidth usage.'),
('NS',0,'newpass','Changes your network account password. Note that you %Bmust%B\r\nuse this command securely.\r\n\r\n%BExample:%B\r\n  /msg %N@%S newpass myNewPassword'),
('NS',0,'set','Use the %Bset%B command to modify various aspects of your\r\naccount on %N. The following options may be used:\r\n\r\n  %BEMAIL%B      Updates your e-mail address.\r\n\r\n  %BINFO%B       Changes your info line. This line is sent to\r\n             a channel when you join it, but will only be\r\n             shown on channels that have the infolines\r\n             setting enabled.\r\n\r\n  %BAUTOOP%B     Toggles whether or not you should be auto-opped\r\n             on channels where you have proper access. \r\n             Can be set to %Bon%B or %Boff%B. Note that\r\n             being auto-opped may also depend on channel\r\n             settings and the auto-op flag in your channel\r\n             access record.\r\n\r\n  %BAUTOVOICE%B  Toggles your global auto-voice preference.\r\n             Can be set to %Bon%B or %Boff%B. Works the same\r\n             as the %Bauto op%B setting.\r\n\r\n  %BENFORCE%B    Instructs %N to kill unauthorized users of\r\n             your nick if they do not log in after 30 seconds.\r\n\r\n%BExamples:%B\r\n  /msg %N set email mynew@emailaddress.com\r\n  /msg %N set info Bow before your king...\r\n  /msg %N set autoop on\r\n  /msg %N set autovoice off\r\n  /msg %N set enforce on'),
('NS',500,'drop','Permanently deletes a registered nick from %N. All of the user\'s\r\nregistered channels, and channel access will also be erased.\r\n\r\n%BExample:%B  /msg %N drop hater'),
('NS',500,'addadmin','Adds a user to %N\'s administrator access list with the specified\r\nlevel.\r\n\r\n%BExamples:%B  addadmin s1amson 800\r\n\r\n'),
('NS',500,'addbad','Adds a word to the bad words list, which prevents users from\r\nregistering a nickname that contains that word.\r\n\r\n%BExample:%B  /msg %N addbad ircplanet'),
('NS',500,'adminlist','Displays %N\'s administrator access list with each administrator\'s\r\nname, level, and e-mail address.\r\n\r\n%BExample:%B  /msg %N adminlist\r\n\r\n'),
('NS',500,'deladmin','Removes a user from %N\'s administrator access list.\r\n\r\n%BExamples:%B  /msg %N deladmin fiddy\r\n\r\n'),
('NS',500,'die','Use the %Bdie%B command to terminate the service, effectively\r\nremoving it from the network. You may optionally provide a\r\nreason that will be used in each bot\'s quit message, as well\r\nas the server quit message.\r\n\r\nUse this command carefully.'),
('NS',500,'rembad','Removes a word from the list of words that cannot appear in\r\nregistered nick names.\r\n\r\n%BExample:%B  /msg %N rembad ircplanet'),
('NS',500,'set host','%BSyntax:%B set [account] host [hostname]\r\n\r\nUpdates the hidden host on your account, which you will receive\r\nonce you log in to %N. The hidden host will only be shown once\r\nyou have set user mode +x on yourself.\r\n\r\nAs an admin, you may optionally specify the account name of the\r\nuser whose hidden host you wish to update.\r\n\r\nIf you do not specify a hostname, it will be cleared and you will\r\nnot receive a hidden host upon future logins.'),
('NS',500,'set nopurge','%BSyntax:%B set [account] nopurge [on|off]\r\n\r\nWhen toggled on, will prevent %N from purging the account\r\nfor inactivity.\r\n\r\nAs an admin, you may optionally specify an account name for\r\nthe person whose nopurge flag you wish to update.'),
('NS',500,'set suspend','%BSyntax:%B set [account] suspend [on|off]\r\n\r\nWhen toggled on, will prevent the specified account from logging\r\nin or using their account.\r\n\r\nAs an admin, you may optionally specify an account name for\r\nthe person whose suspend flag you wish to update.'),

('OS',0,'access','Displays access level information for users having access on %N.\r\nAn account name mask or a full account name can be specified.\r\n\r\n%BExamples:%B  /msg %N access brian\r\n           /msg %N access br*\r\n           /msg %N access *'),
('OS',0,'addbad','Adds a word to the bad words list, which causes %S \r\nto set +s on any channel whose name contains the specified word.\r\nIf any channel users attempt to remove +s, it is re-applied.\r\n\r\n%BExample:%B  /msg %N addbad warez'),
('OS',0,'addgchan','Immediately kicks users from the specified channel and prevents\r\nany other users (except IRC operators) from entering the channel.\r\nThe channel G-line will be active for the specified duration and\r\nthe reason will be shown to users as the kick reason.\r\n\r\nDurations must use the following format: %B<number><unit>%B\r\nThe unit can be any of the following and can be combined as\r\nlong as larger units come first.\r\n   %Bw%B - weeks\r\n   %Bd%B - days\r\n   %Bh%B - hours\r\n   %Bm%B - minutes\r\n   %Bs%B - seconds\r\n\r\nExamples:\r\n   %B2w%B    - Two weeks.\r\n   %B5d12h%B - Five days and twelve hours.\r\n   %B90s%B   - Ninety seconds.\r\n\r\n%BExamples:%B  /msg %N addgchan #warez 5y No warez.\r\n           /msg %N addgchan #!!!dronecontrolchan 5y Take your drones elsewhere.'),
('OS',0,'addgname','Immediately disconnects users from the network whose real name\r\nfield matches the specified G-line mask, for the provided \r\nduration. The reason will be shown to affected users upon their\r\nbeing disconnected.\r\n\r\nDurations must use the following format: %B<number><unit>%B\r\nThe unit can be any of the following and can be combined as\r\nlong as larger units come first.\r\n   %Bw%B - weeks\r\n   %Bd%B - days\r\n   %Bh%B - hours\r\n   %Bm%B - minutes\r\n   %Bs%B - seconds\r\n\r\nExamples:\r\n   %B2w%B    - Two weeks.\r\n   %B5d12h%B - Five days and twelve hours.\r\n   %B90s%B   - Ninety seconds.\r\n\r\n%BExamples:%B  /msg %N addgname *sub7* 5y You are infected with a trojan.\r\n           /msg %N addgname [WarBot]* 5y You are infected with a trojan.'),
('OS',0,'adduser','Adds a user to %N\'s access list with the specified level.\r\n\r\n%BExamples:%B  adduser s1amson 800'),
('OS',0,'ban','Sets a ban in the specified channel. An optional kick reason may \r\nbe provided.\r\n\r\nDurations must use the following format: %B<number><unit>%B\r\nThe unit can be any of the following and can be combined as\r\nlong as larger units come first.\r\n   %Bw%B - weeks\r\n   %Bd%B - days\r\n   %Bh%B - hours\r\n   %Bm%B - minutes\r\n   %Bs%B - seconds\r\n\r\nExamples:\r\n   %B2w%B    - Two weeks.\r\n   %B5d12h%B - Five days and twelve hours.\r\n   %B90s%B   - Ninety seconds.\r\n\r\n%BExamples:%B  /msg %N ban #southpole *!*flooder@*.aol.com\r\n           /msg %N ban #southpole *!*@*.ca No canadians.\r\n           /msg %N ban #southpole flooder'),
('OS',0,'banlist','Shows the channel\'s current ban list. An optional search mask\r\nmay be used in order to find information on a specific ban.\r\n\r\n%BExamples:%B  /msg %N banlist #southpole *flooder*\r\n           /msg %N banlist #southpole *@aol.com'),
('OS',0,'broadcast','Sends a broadcast NOTICE to all users on the network.\r\n\r\n%BExample:%B  /msg %N broadcast Nickname and channel services will be restarting momentarily for an upgrade. Please come see us in #support if you have any questions.'),
('OS',0,'chaninfo','Displays various information about the specified channel\'s current\r\nstate, including modes and topic.\r\n\r\n%BExample:%B  /msg %N chaninfo #southpole'),
('OS',0,'chanlist','Displays a list of channels whose name matches the specified mask,\r\nadditionally displaying their currently set modes and user count.\r\n\r\n%BExamples:%B  /msg %N chanlist *mp3*\r\n           /msg %N chanlist #warez*\r\n           /msg %N chanlist *'),
('OS',0,'clearchan','Clears channel modes, users, bans, ops, and/or voices, in accordance\r\nwith the flags you specify. Flags can be specified in any order,\r\nand you can include as many as are necessary.\r\n\r\nThe following are flags that can be specified as part of the flag list:\r\n   %Bm%B   Clears all channel modes.\r\n   %Bk%B   Kicks all users from the channel, except you.\r\n   %Bo%B   Deops all opped users in the channel.\r\n   %Bv%B   Devoices all voiced users in the channel.\r\n   %Bb%B   Clears all channel bans.\r\n   %Bg%B   Issues G-lines for all users in the channel, except you.\r\n\r\n%BExamples%B:  To kick all users:\r\n           /msg %N clearchan #hackage k Kicking all users...\r\n\r\n           To clear all modes, bans, ops, and voices:\r\n           /msg %N clearchan #brasil mbov\r\n\r\n           To clear all modes and ops:\r\n           /msg %N clearchan #takenover om\r\n\r\n           To issue a 30-minute G-line for all channel users:\r\n           /msg %N clearchan #drones g 30m'),
('OS',0,'clearmodes','Clears all modes in the specified channel.'),
('OS',0,'deop','Removes op status from the specified user(s) in the channel.\r\nIf no nicks are provided, %N will deop you.'),
('OS',0,'deopall','Removes op status from every user in the specified channel.\r\n'),
('OS',0,'devoice','Removes a voice from the specified user(s) in the channel. If\r\nno nicks are provided, %N will devoice you.'),
('OS',0,'devoiceall','Removes voices from every user in the specified channel.'),
('OS',0,'die','Use the %Bdie%B command to terminate the service, effectively\r\nremoving it from the network. You may optionally provide a\r\nreason that will be used in each bot\'s quit message, as well\r\nas the server quit message.\r\n\r\nUse this command carefully.'),
('OS',0,'fakehost','Sets a user\'s fake/hidden host to the specified hostname. The user\r\nmust have user mode +x set on themselves in order for the hostname\r\nto appear.\r\n\r\n%BExample:%B  /msg %N fakehost s1amson svcadmin.virtuanet.org'),
('OS',0,'gline','Immediately disconnects users from the network whose user@host \r\nmatches the specified G-line mask, for the provided duration.\r\nThe reason will be shown to affected users upon their \r\nbeing disconnected.\r\n\r\nDurations must use the following format: %B<number><unit>%B\r\nThe unit can be any of the following and can be combined as\r\nlong as larger units come first.\r\n   %Bw%B - weeks\r\n   %Bd%B - days\r\n   %Bh%B - hours\r\n   %Bm%B - minutes\r\n   %Bs%B - seconds\r\n\r\nExamples:\r\n   %B2w%B    - Two weeks.\r\n   %B5d12h%B - Five days and twelve hours.\r\n   %B90s%B   - Ninety seconds.\r\n\r\n%BExamples:%B  /msg %N gline *@*.aol.com 90d No AOL users.\r\n           /msg %N gline *@43.190.216.12 30m Please get your connection under control.'),
('OS',0,'help','%N is a service designed to help administrators and operators \r\nmonitor and maintain the network through added visibility, as\r\nwell as provide enhanced methods of intervening in situations\r\nwhere normal operator privileges aren\'t enough.\r\n\r\nTo get a list of commands that are available to you, simply\r\ntype %B/msg %N showcommands%B.\r\n\r\nIf you wish to look up usage information about a command,\r\ntype %B/msg %N help commandname%B, where commandname is the \r\ncommand you need help with.'),
('OS',0,'inviteme','Invites you to %N\'s command reporting and network event reporting channels.'),
('OS',0,'jupe','Jupes the specified server to prevent it from connecting to the \r\nnetwork for the given duration.\r\n\r\n%BExample:%B  /msg %N jupe Provo.UT.US.ircPlanet.net 10y No servers from ultraconservative cities allowed'),
('OS',0,'kick','Kicks the specified user from the channel. An optional reason\r\nmay be specified for the kick.'),
('OS',0,'kickall','Kicks all users from the specified channel. The user issuing this\r\ncommand will not be kicked.\r\n'),
('OS',0,'kickban','Kicks and bans the specified user from the channel. A hostmask\r\nmay be specified instead of a nickname, in which case %N\r\nwill kick and ban all users matching that mask.\r\n\r\n%BExamples:%B  /msg %N kickban #southpole lamer\r\n           /msg %N kickban #southpole britneyspears Quiet please.\r\n           /msg %N kickban #southpole *!*@*.aol.com Down with AOL.'),
('OS',0,'kickbanall','Deops, kicks and bans all users from the specified channel. The user\r\nissuing this command will not be kicked or banned.\r\n'),
('OS',0,'mode','Sets the specified mode(s) in the channel.'),
('OS',0,'moderate','Moderates the channel (+m) and grants voice status to every\r\nnon-op.\r\n'),
('OS',0,'moduser level','Update\'s the specified user\'s access level on %N.\r\n\r\n%BExample:%B  /msg %N moduser s1amson level 900'),
('OS',0,'moduser','Allows you to change settings for a user in %N\'s access list.\r\nThe list of allowed settings are as follows:\r\n\r\n%BLEVEL%B      Change the user\'s access level.\r\n\r\nFor more information on a specific setting, please use\r\n/msg %N help moduser <setting>'),
('OS',0,'op','Grants op status to the specified user(s) in the channel. If\r\nno nicks are provided, %N will op you.'),
('OS',0,'opall','Grants op status to every user in the specified channel.\r\n'),
('OS',0,'opermsg','Sends a broadcast NOTICE to all IRC operators on the network.\r\n\r\n%BExample:%B  /msg %N broadcast Training on the new set of oper service commands will begin in 30 mins in #opers.'),
('OS',0,'quote','Sends the specified line as raw text to %S\'s uplink.\r\n\r\n%BUSE WITH CAUTION. RAW TEXT IS NOT PARSED FOR ACCURACY NOR IS ITS%B\r\n%BEFFECTS PERSISTED IN MEMORY BEFORE BEING SENT ACROSS THE WIRE.%B\r\n\r\n%BYOU SHOULD BE WELL-VERSED IN RAW P10 PROTOCOL BEFORE USING THIS.%B'),
('OS',0,'refreshg','Reissues all current G-lines, in the event any server has a \r\ndesynced list of G-lines.'),
('OS',0,'rembad','Removes a word from the list of words that cannot appear in\r\npublicly-listed channel names.\r\n\r\n%BExample:%B  /msg %N rembad warez'),
('OS',0,'remgchan','Deactivates a channel G-line, which will allow users to join any \r\nthe specified channel.\r\n\r\nExisting channel G-lines can be listed with %Bshow glines%B.\r\n\r\n%BExample:%B  /msg %N remgchan #warez'),
('OS',0,'remgline','Deactivates a G-line, which will allow any affected users to \r\nreconnect to the network.\r\n\r\nExisting G-lines can be listed with the %Bshow glines%B command.\r\n\r\n%BExample:%B  /msg %N remgline *@*.aol.com'),
('OS',0,'remgname','Deactivates a realname G-line, which will allow users whose real\r\nname field matches the given G-line mask.\r\n\r\nExisting realname G-lines can be listed with %Bshow glines%B.\r\n\r\n%BExample:%B  /msg %N remgname *sub7*'),
('OS',0,'remuser','Removes a user from %N\'s access list.\r\n\r\n%BExamples:%B  /msg %N remuser fiddy'),
('OS',0,'scan','Displays a list of all users whose user@host/IP mask matches\r\nthe specified mask, additionally displaying their full hostmask,\r\nIP address, and the server they\'re using.\r\n\r\n%BExamples:%B  /msg %N scan brian*\r\n           /msg %N scan *@ircplanet.net\r\n           /msg %N scan *ident@*\r\n           /msg %N scan *'),
('OS',0,'settime','Instructs all servers to update their internal clocks in ircu with\r\nthe specified timestamp. This does %Bnot%B update the system-level clock\r\non each server.'),
('OS',0,'showcommands','Lists all commands available to you.'),
('OS',0,'topic','Sets the topic in the specified channel.'),
('OS',0,'unjupe','Deactivates an existing jupe for the specified server to allow it\r\nto connect to the network again.\r\n\r\n%BExample:%B  /msg %N unjupe moonbus.ircplanet.net'),
('OS',0,'uptime','Show\'s the bot\'s running time and bandwidth usage.'),
('OS',0,'voice','Grants a voice to the specified user(s) in the channel. If no\r\nnicks are provided, %N will voice you.'),
('OS',0,'voiceall','Grants voices to every user in the specified channel.'),
('OS',0,'whois','Displays detailed information about the specified user.'),
('OS',0,'whoison','Displays a list of all users currently in the specified channel,\r\nadditionally denoting ops and voices.'),
('OS',0,'show','Displays details on various statistics and settings. The following \r\nis a list of all settings and statistics whose details can be shown.\r\n\r\n%BBAD%B        The list of words that are not allowed to appear in\r\n               public channel names.\r\n%BCLONES%B     All users who have clones on the network.\r\n%BGLINES%B     All G-lines.\r\n%BJUPES%B      All server jupes.\r\n%BOPERS%B      All signed-on IRC operators.\r\n\r\n%BExamples:%B  /msg %N show opers\r\n               /msg %N show clones'),

('SS',0,'help','%N is a service designed to maintain various live statistics\r\nand historical data about the network.\r\n\r\nTo get a list of commands that are available to you, simply\r\ntype %B/msg %N showcommands%B.\r\n\r\nIf you wish to look up usage information about a command,\r\ntype %B/msg %N help commandname%B, where commandname is the \r\ncommand you need help with.'),
('SS',0,'showcommands','Lists all commands available to you.'),
('SS',0,'uptime','Show\'s the bot\'s running time and bandwidth usage.'),
('SS',500,'access','Displays access level information for users having access on %N.\r\nAn account name mask or a full account name can be specified.\r\n\r\n%BExamples:%B  /msg %N access brian\r\n           /msg %N access br*\r\n           /msg %N access *'),
('SS',500,'adduser','Adds a user to %N\'s access list with the specified level.\r\n\r\n%BExamples:%B  adduser s1amson 800'),
('SS',500,'inviteme','Invites you to %N\'s command reporting channel.'),
('SS',500,'moduser level','Update\'s the specified user\'s access level on %N.\r\n\r\n%BExample:%B  /msg %N moduser s1amson level 900'),
('SS',500,'moduser','Allows you to change settings for a user in %N\'s access list.\r\nThe list of allowed settings are as follows:\r\n\r\n%BLEVEL%B      Change the user\'s access level.\r\n\r\nFor more information on a specific setting, please use\r\n/msg %N help moduser <setting>'),
('SS',500,'quote','Sends the specified line as raw text to %S\'s uplink.\r\n\r\n%BUSE WITH CAUTION. RAW TEXT IS NOT PARSED FOR ACCURACY NOR IS ITS%B\r\n%BEFFECTS PERSISTED IN MEMORY BEFORE BEING SENT ACROSS THE WIRE.%B\r\n\r\n%BYOU SHOULD BE WELL-VERSED IN RAW P10 PROTOCOL BEFORE USING THIS.%B'),
('SS',500,'remuser','Removes a user from %N\'s access list.\r\n\r\n%BExamples:%B  /msg %N remuser fiddy'),
('SS',500,'die','Use the %Bdie%B command to terminate the service, effectively\r\nremoving it from the network. You may optionally provide a\r\nreason that will be used in each bot\'s quit message, as well\r\nas the server quit message.\r\n\r\nUse this command carefully.');
/*!40000 ALTER TABLE `help` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_admins`
--

DROP TABLE IF EXISTS `ns_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ns_admins` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Nickserv Admins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ns_badnicks`
--

DROP TABLE IF EXISTS `ns_badnicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ns_badnicks` (
  `badnick_id` int(11) NOT NULL AUTO_INCREMENT,
  `nick_mask` varchar(50) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`badnick_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Bad Nick Words/Masks';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `os_admins`
--

DROP TABLE IF EXISTS `os_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `os_admins` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Operserv Admins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `os_badchans`
--

DROP TABLE IF EXISTS `os_badchans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `os_badchans` (
  `badchan_id` int(11) NOT NULL AUTO_INCREMENT,
  `chan_mask` varchar(50) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`badchan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Bad Channel Words/Masks';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `os_glines`
--

DROP TABLE IF EXISTS `os_glines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `os_glines` (
  `gline_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `set_ts` int(11) NOT NULL DEFAULT '0',
  `expire_ts` int(11) NOT NULL DEFAULT '0',
  `mask` varchar(100) NOT NULL DEFAULT '',
  `reason` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`gline_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_channel_users`
--

DROP TABLE IF EXISTS `stats_channel_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_channel_users` (
  `channel_name` varchar(255) NOT NULL,
  `nick` varchar(15) NOT NULL,
  `is_op` smallint(5) unsigned NOT NULL,
  `is_voice` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_channels`
--

DROP TABLE IF EXISTS `stats_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_channels` (
  `channel_name` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `modes` varchar(45) NOT NULL,
  PRIMARY KEY (`channel_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_history`
--

DROP TABLE IF EXISTS `stats_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_servers`
--

DROP TABLE IF EXISTS `stats_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_servers` (
  `server_name` varchar(100) NOT NULL,
  `desc` varchar(100) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `max_users` int(10) unsigned NOT NULL,
  `is_service` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`server_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_users`
--

DROP TABLE IF EXISTS `stats_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_users` (
  `nick` varchar(15) NOT NULL,
  `ident` varchar(15) NOT NULL,
  `host` varchar(80) NOT NULL,
  `name` varchar(100) NOT NULL,
  `server` varchar(60) NOT NULL,
  `modes` varchar(10) NOT NULL,
  `account` varchar(15) NOT NULL,
  `signon_date` datetime DEFAULT NULL,
  PRIMARY KEY (`nick`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-30  1:06:36
