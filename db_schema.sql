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
  `register_date` datetime default NULL,
  `create_date` datetime default NULL,
  `update_date` datetime default NULL,
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
-- Dumping data for table `help`
--

LOCK TABLES `help` WRITE;
/*!40000 ALTER TABLE `help` DISABLE KEYS */;
INSERT INTO `help` VALUES 
('NS',501,'die','Use the %Bdie%B command to terminate the service, effectively\r\nremoving it from the network. You may optionally provide a\r\nreason that will be used in each bot\'s quit message, as well\r\nas the server quit message.\r\n\r\nUse this command carefully.'),
('NS',501,'addadmin','Adds a user to %N\'s administrator access list with the specified\r\nlevel.\r\n\r\n%BExamples:%B  addadmin s1amson 800\r\n\r\n'),
('NS',501,'deladmin','Removes a user from %N\'s administrator access list.\r\n\r\n%BExamples:%B  deladmin poptix\r\n\r\n'),
('NS',501,'adminlist','Displays %N\'s administrator access list with each administrator\'s\r\nname, level, and e-mail address.\r\n\r\n%BExample:%B  /msg %N adminlist\r\n\r\n'),
('NS',0,'uptime','Show\'s the bot\'s running time and bandwidth usage.'),
('NS',0,'showcommands','Lists all commands available to you.'),
('NS',1,'set','Use the %Bset%B command to modify various aspects of your\r\naccount on %N. The following options may be used:\r\n\r\n  %BEMAIL%B      Updates your e-mail address.\r\n\r\n  %BINFO%B       Changes your info line. This line is sent to\r\n             a channel when you join it, but will only be\r\n             shown on channels that have the infolines\r\n             setting enabled.\r\n\r\n  %BAUTOOP%B     Toggles whether or not you should be auto-opped\r\n             on channels where you have proper access. \r\n             Can be set to %Bon%B or %Boff%B. Note that\r\n             being auto-opped may also depend on channel\r\n             settings and the auto-op flag in your channel\r\n             access record.\r\n\r\n  %BAUTOVOICE%B  Toggles your global auto-voice preference.\r\n             Can be set to %Bon%B or %Boff%B. Works the same\r\n             as the %Bauto op%B setting.\r\n\r\n  %BENFORCE%B    Instructs %N to kill unauthorized users of\r\n             your nick if they do not log in after 30 seconds.\r\n\r\n%BExamples:%B\r\n  /msg %N set email mynew@emailaddress.com\r\n  /msg %N set info Bow before your king...\r\n  /msg %N set autoop on\r\n  /msg %N set autovoice off\r\n  /msg %N set enforce on'),
('NS',1,'newpass','Changes your network account password. Note that you %Bmust%B\r\nuse this command securely.\r\n\r\n%BExample:%B\r\n  /%N newpass myNewPassword'),
('NS',0,'help','%N is a nickname and account service designed as a central\r\nauthentication point for all other network services.\r\n\r\nTo get a list of commands that are available to you, simply\r\ntype %Bshowcommands%B.\r\n\r\nIf you wish to look up usage information about a command,\r\ntype %Bhelp commandname%B, where commandname is the command\r\nyou need help with.'),
('NS',0,'info','Displays information about the specified account name.\r\n\r\n%BExample:%B\r\n  /%N info joebob'),
('NS',0,'login','Logs you in to your account on %N. Note that you %Bmust%B use\r\nthis command securely. If you do not provide an account name,\r\n%N will assume the account name is the same as your current\r\nnickname.\r\n\r\nFor example, if your nick is JoeBob, the below line would log\r\nyou in to the account JoeBob:\r\n  /%N login myPassword\r\n\r\nIf your nick is taken, or your account name is different than\r\nyour nickname, you can specify an account to log in to:\r\n  /%N login myaccount myPassword\r\n\r\nThe only way to log out of your account is by using /quit.'),
('NS',0,'register','Creates an account that you can use on all network services.\r\nYou must provide a password and valid e-mail address.\r\n\r\n%BExample:%B\r\n  /%N register myPassword ircuser@myemail.com\r\n\r\nUpon registering, you will be automagically logged in.'),

('CS',501,'addadmin','Adds a user to %N\'s administrator access list with the specified\r\nlevel.\r\n\r\n%BExamples:%B  addadmin s1amson 800\r\n\r\n'),
('CS',501,'deladmin','Removes a user from %N\'s administrator access list.\r\n\r\n%BExamples:%B  deladmin poptix\r\n\r\n'),
('CS',501,'adminlist','Displays %N\'s administrator access list with each administrator\'s\r\nname, level, and e-mail address.\r\n\r\n%BExample:%B  /msg %N adminlist\r\n\r\n'),
('CS',0,'uptime','Shows %N\'s running time and bandwidth usage.'),
('CS',0,'showcommands','Lists all commands available to you.'),
('CS',0,'access','Displays access level information for users having access on\r\nthe specified channel. The search mask can either be a service\r\naccount name or a search mask (where * and ? are wildcards).\r\n\r\n%BExample:%B  access #southpole brian\r\n          access #southpole br*\r\n          access #southpole *'),
('CS',0,'help','%N is a channel service designed to help users maintain\r\nownership and control over their channels. Channels are\r\nregistered on a first-come, first-served basis. Users can\r\nregister up to %MAX_CHAN_REGS% channels with the %Bregister%B command.\r\n\r\nTo get a list of commands that are available to you, simply\r\ntype %Bshowcommands%B. To get a list of commands available\r\nto you on a specific channel, type %Bshowcommands #channel%B.\r\n\r\nIf you wish to look up usage information about a command,\r\ntype %Bhelp commandname%B, where commandname is the command\r\nyou need help with.'),
('CS',0,'topic','Sets the topic in the specified channel.'),
('CS',0,'banlist','Shows the channel\'s current ban list. An optional search mask\r\nmay be used in order to find information on a specific ban.\r\n\r\n%BExample:%B  banlist #southpole *flooder*\r\n          banlist #southpole *@aol.com'),
('CS',0,'unban','Removes a ban in the specified channel.'),
('CS',0,'ban','Sets a ban in the specified channel. An optional ban duration,\r\naccess level, and reason may be provided.\r\n\r\nDurations must use the following format: %B<number><unit>%B\r\nThe unit can be any of the following and can be combined as\r\nlong as larger units come first.\r\n   %Bw%B - weeks\r\n   %Bd%B - days\r\n   %Bh%B - hours\r\n   %Bm%B - minutes\r\n   %Bs%B - seconds\r\n\r\nExamples:\r\n   %B2w%B    - Two weeks.\r\n   %B5d12h%B - Five days and twelve hours.\r\n   %B90s%B   - Ninety seconds.\r\n\r\nAn access level can be provided to prevent other users with\r\nlower access levels from removing the ban. For example, a ban\r\nwith a level of 200 would prevent anyone with less than level\r\n200 access from removing the ban. You cannot use a level\r\nthat is higher than your own.\r\n\r\n%BExamples:%B  ban #southpole *!*flooder@*.aol.com\r\n           ban #southpole *!*@*.ca 5w 200 No canadians.\r\n           ban #southpole flooder 30d'),
('CS',0,'kickban','Kicks and bans the specified user from the channel. A hostmask\r\nmay be specified instead of a nickname, in which case %N\r\nwill kick and ban all users matching that mask.\r\n\r\nBans set as a result of this command will last for one hour,\r\nand will have a level of 75.\r\n\r\n%BExamples:%B  kickban #southpole lamer\r\n           kickban #southpole demonhell Quiet please.\r\n           kickban #southpole *!*@*.aol.com Down with AOL.'),
('CS',0,'kick','Kicks the specified user from the channel. An optional reason\r\nmay be specified for the kick.'),
('CS',0,'do','Has the bot perform a public action (/me) in the channel.'),
('CS',0,'say','Has the bot speak publicly in the channel.'),
('CS',0,'invite','Invites the specified user(s) to the channel. If no nicks are\r\nspecified, %N will only invite you.\r\n\r\n%BExamples:%B  invite #southpole brian tsunam1\r\n           invite #southpole'),
('CS',0,'devoice','Removes a voice from the specified user(s) in the channel. If\r\nno nicks are provided, %N will devoice you.'),
('CS',0,'voice','Grants a voice to the specified user(s) in the channel. If no\r\nnicks are provided, %N will voice you.'),
('CS',0,'deop','Removes op status from the specified user(s) in the channel.\r\nIf no nicks are provided, %N will deop you.'),
('CS',0,'op','Grants op status to the specified user(s) in the channel. If\r\nno nicks are provided, %N will op you.'),
('CS',0,'mode','Sets the specified mode(s) in the channel.'),
('CS',0,'devoiceall','Removes voices from every user in the channel.'),
('CS',0,'voiceall','Grants voices to every user in the channel.'),
('CS',0,'deopall','Removes op status from every user in the channel.\r\n'),
('CS',0,'opall','Grants op status to every user in the channel.\r\n'),
('CS',0,'moderate','Moderates the channel (+m) and grants voice status to every\r\nnon-op.\r\n'),
('CS',0,'rdeftopic','Resets the topic to the default topic. Use the %Bset deftopic%B\r\ncommand to set the default topic.'),
('CS',0,'rdefmodes','Resets the channel modes to the default. Use the %Bset defmodes%B\r\ncommand to set the default channel modes.\r\n'),
('CS',0,'clearbans','Clears all bans in the channel.\r\n'),
('CS',0,'clearmodes','Clears all modes in the channel.\r\n'),
('CS',0,'remuser','Removes a user\'s access from the channel. You may use the \r\n%Baccess%B command to see who has channel access.\r\n'),
('CS',0,'moduser','Modifies a user\'s access record on the channel. The following\r\naccess settings may be modified with this command:\r\n\r\n%BLEVEL%B [new level]      Sets the user\'s access level.\r\n%BAUTOOP%B [on|off]        Toggles the user\'s autoop flag.\r\n%BAUTOVOICE%B [on|off]     Toggles the user\'s autovoice flag.\r\n\r\nNote: The autoop and autovoice flags may be ignored if the\r\nuser turns them off for their user account, or if the channel\r\nautoop and autovoice flags are turned off.\r\n\r\n%BExamples:%B  moduser #southpole tsunam1 level 499\r\n           moduser #southpole brian autoop on\r\n'),
('CS',0,'adduser','Grants a user access to the channel. You may optionally\r\nspecify the user\'s level. If no level is provided, the\r\ndefault of 100 is used.\r\n'),
('CS',0,'kickbanall','Deops, kicks and bans all users from the channel. The user\r\nissuing this command will not be kicked or banned.\r\n'),
('CS',0,'kickall','Kicks all users from the channel. The user issuing this\r\ncommand will not be kicked.\r\n'),
('CS',0,'set','Changes various channel settings. The following is a list\r\nof all settings that can be changed with this command:\r\n\r\n%BPURPOSE%B            Purpose\r\n%BURL%B                Web site address\r\n%BDEFTOPIC%B           Default topic\r\n%BDEFMODES%B           Default channel modes\r\n%BINFOLINES%B          Display user info lines upon join\r\n%BAUTOOP%B             Enables auto opping\r\n%BAUTOOPALL%B          Automatically ops everyone upon join\r\n%BAUTOVOICE%B          Enables auto voicing\r\n%BAUTOVOICEALL%B       Automatically voices everyone upon join\r\n%BAUTOLIMIT%B          Enables automatic channel limits\r\n%BAUTOLIMITBUFFER%B    Buffer for autolimit\r\n%BAUTOLIMITWAIT%B      Delay for autolimit (in seconds)\r\n%BSTRICTOP%B           Enables strict ops\r\n%BSTRICTVOICE%B        Enables strict voices\r\n%BSTRICTTOPIC%B        Enables strict topic\r\n%BSTRICTMODES%B        Enables strict channel modes\r\n\r\nFor more details on a setting, /msg %N help set <setting>.\r\nFor example: /msg %N help set autoop\r\n'),
('CS',0,'set purpose','%BSyntax:% set <channel> purpose [purpose]\r\n\r\nSets the channel\'s purpose.\r\n'),
('CS',0,'set url','%BSyntax:%B set <channel> url [address]\r\n\r\nSets the channel\'s web site address.\r\n'),
('CS',0,'set deftopic','%BSyntax:%B set <channel> deftopic [topic]\r\n\r\n\r\nSets the default topic, which is automatically set when %N\r\njoins the network.'),
('CS',0,'set defmodes','%BSyntax:%B set <channel> defmodes [modes]\r\n\r\nSets the default channel modes, which are automatically set\r\nwhen %N joins the network.\r\n\r\n%BExamples:%B  set defmodes +nt\r\n       set defmodes +ntsk key\r\n       set defmodes +ntl 20\r\n'),
('CS',0,'set infolines','%BSyntax:%B set <channel> infolines [on|off]\r\n\r\nToggles the display of infolines when a registered user with\r\nan infoline joins the channel.'),
('CS',0,'set autoop','%BSyntax:%B set <channel> autoop [on|off]\r\n\r\nToggles automatic ops for users who have proper access.\r\nThis setting may be ignored if a user has autoop turned off\r\nfor their user account, or if their channel access has the\r\nautoop flag turned off (see the %Bmoduser%B command).'),
('CS',0,'set autovoice','%BSyntax:%B set <channel> autovoice [on|off]\r\n\r\nToggles automatic voices for users who have proper access.\r\nThis setting may be ignored if a user has autovoice turned off\r\nfor their user account, or if their channel access has the\r\nautovoice flag turned off (see the %Bmoduser%B command).'),
('CS',0,'set autoopall','%BSyntax:%B set <channel> autoopall [on|off]\r\n\r\nToggles automatic ops for everyone that enters the channel,\r\neven if they are not registered with services.'),
('CS',0,'set autovoiceall','%BSyntax:%B set <channel> autovoiceall [on|off]\r\n\r\nToggles automatic voices for everyone that enters the channel,\r\neven if they are not registered with services.'),
('CS',0,'set autolimit','%BSyntax:%B set <channel> autolimit [on|off]\r\n\r\nToggles automatic size limiting for the channel. If this\r\nsetting is enabled, %N will automatically set a size limit\r\non the channel approximately N seconds after a user joins,\r\nwhere N is the number of seconds specified by the \r\nAUTOLIMITWAIT setting. The size limit it sets will be\r\nequivalent to the number of users in the channel plus Z,\r\nwhere Z is the number specified by the AUTOLIMITBUFFER\r\nsetting.\r\n\r\nFor example, assume the following settings for #southpole:\r\n     autolimit:        on\r\n     autolimitwait:    30\r\n     autolimitbuffer:  5\r\n\r\n30 seconds after a user joins #southpole, %N will wait 30\r\nseconds before setting a limit of N+5, where N is the current\r\nnumber of users in the channel.\r\n\r\nIf a user joins #southpole 30 seconds after the first user,\r\n%N will wait an additional 30 seconds before setting the\r\nnew limit.\r\n\r\n%BExample:%B  set #southpole autolimit on\r\n          set #southpole autolimit off'),
('CS',0,'set autolimitbuffer','%BSyntax:%B set <channel> autolimitbuffer <number>\r\n\r\nSets the auto limit buffer, or the number that will be added\r\nto the current channel user count when %N sets an automatic\r\nlimit. The buffer must be between %MIN_CHAN_AUTOLIMIT_BUFFER% and %MAX_CHAN_AUTOLIMIT_BUFFER%.\r\n\r\n%BExample:%B  set #southpole autolimitbuffer 5\r\n\r\nFor more information, use %Bhelp set autolimit%B.'),
('CS',0,'set autolimitwait','%BSyntax:%B set <channel> autolimitwait <seconds>\r\n\r\nSets the number of seconds that %N will wait before\r\nsetting an auto limit. The number of seconds must\r\nbe between %MIN_CHAN_AUTOLIMIT_WAIT% and %MAX_CHAN_AUTOLIMIT_WAIT% seconds.\r\n\r\n%BExample:%B  set #southpole autolimitwait 60\r\n\r\nFor more information, use %Bhelp set autolimit%B.'),
('CS',0,'set strictop','%BSyntax:%B set <channel> strictop [on|off]\r\n\r\nToggles strict ops. If enabled, %N will always deop any\r\nusers that do not have channel access.\r\n\r\n%BExample:%B  set #southpole strictop on\r\n          set #southpole strictop off'),
('CS',0,'set strictvoice','%BSyntax:%B set <channel> strictvoice [on|off]\r\n\r\nToggles strict voices. If enabled, %N will always devoice\r\nany users that do not have channel access.\r\n\r\n%BExample:%B  set #southpole strictvoice on\r\n          set #southpole strictvoice off'),
('CS',0,'set stricttopic','%BSyntax:%B set <channel> stricttopic [on|off]\r\n\r\nToggles strict topic. If enabled, %N will reset the \r\ntopic if anyone tries to change it without using the %Btopic%B\r\ncommand.\r\n\r\n%BExample:%B  set #southpole stricttopic on\r\n          set #southpole stricttopic off'),
('CS',0,'set strictmodes','%BSyntax:%B set <channel> strictmodes [on|off]\r\n\r\nToggles strict modes. If enabled, %N will undo any channel\r\nmode changes that are not performed with the %Bmode%B command.\r\n\r\n%BExample:%B  set #southpole strictmodes on\r\n          set #southpole strictmodes off'),
('CS',0,'register','Registers a channel with the specified purpose. You may \r\nregister up to %MAX_CHAN_REGS% channels.');
/*!40000 ALTER TABLE `help` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Table structure for table `os_glines`
--

DROP TABLE IF EXISTS `os_glines`;
CREATE TABLE `os_glines` (
  `gline_id` int(10) unsigned NOT NULL auto_increment,
  `set_ts` int(11) NOT NULL default '0',
  `expire_ts` int(11) NOT NULL default '0',
  `mask` varchar(100) NOT NULL default '',
  `reason` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`gline_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

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
  `start_date` datetime default NULL,
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
  `signon_date` datetime default NULL,
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
