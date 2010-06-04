<?php
/*
 * ircPlanet Services for ircu
 * Copyright (c) 2005 Brian Cline.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:

 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of ircPlanet nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without 
 *    specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

	require_once('core_globals.php');
	require_once(CORE_DIR .'/util_string.php');
	require_once(CORE_DIR .'/util_array.php');
	require_once(CORE_DIR .'/util_datetime.php');
	require_once(CORE_DIR .'/util_obj.php');
	require_once(CORE_DIR .'/util_db.php');
	
	require_once(CORE_DIR .'/p10.php');
	
	require_once(CORE_DIR .'/server.php');
	require_once(CORE_DIR .'/channel.php');
	require_once(CORE_DIR .'/gline.php');
	require_once(CORE_DIR .'/jupe.php');
	require_once(CORE_DIR .'/user.php');
	require_once(CORE_DIR .'/bot.php');
	require_once(CORE_DIR .'/db_user.php');
	require_once(CORE_DIR .'/timer.php');
	
	$INSTANTIATED_SERVICES = array();
	
	class Service
	{
		var $debug = true;
		
		var $config;
		var $sock;
		var $db;
		var $timers = array();
		
		var $bytes_sent = 0;
		var $bytes_received = 0;
		var $lines_sent = 0;
		var $lines_received = 0;
		
		var $servers = array();
		var $users = array();
		var $channels = array();
		var $accounts = array();
		var $glines = array();
		var $jupes = array();
		
		var $bots = array();
		var $default_bot = null;
		
		var $command_info = array();
		var $command_list = array();
		
		var $numeric_count = -1;
		
		var $finished_burst = false;
		
		
		function __construct()
		{
			if (!defined('SERVICE_DIR'))
				die("The service class cannot run by itself.\n");
			
			if (!defined('SERVICE_CONFIG_FILE'))
				define('SERVICE_CONFIG_FILE', 'service.ini');
				
			/**
			 * The following methods are required for child classes. A service cannot exist
			 * if it does not implement these methods.
			 */
			if (!method_exists($this, 'serviceConstruct'))
				die("You have not defined a service constructor (serviceConstruct).");
			if (!method_exists($this, 'serviceDestruct'))
				die("You have not defined a service destructor (serviceDestruct).");
			if (!method_exists($this, 'serviceLoad'))
				die("You have not defined a service data loader method (serviceLoad).");
			if (!method_exists($this, 'servicePreburst'))
				die("You have not defined a pre-burst method (servicePreburst).");
			if (!method_exists($this, 'servicePreread'))
				die("You have not defined a pre-read method (servicePreread).");
			if (!method_exists($this, 'serviceClose'))
				die("You have not defined a service close method (serviceClose).");
			if (!method_exists($this, 'serviceMain'))
				die("You have not defined a main service method (serviceMain).");
			
			define('START_TIME', time());
			define('SERVICE_VERSION', SERVICE_NAME .' v'.
				SERVICE_VERSION_MAJOR .'.'. SERVICE_VERSION_MINOR .'.'. SERVICE_VERSION_REV);
			
			$this->addTimer(true, 5, 'expire_glines.php');
			$this->addTimer(true, 5, 'expire_jupes.php');
			$this->addTimer(true, 5, 'refresh_data.php');
			
			$this->serviceConstruct();
			$this->loadConfig();
			$this->connect();
			$this->main();
		}
		
		function __destruct()
		{
			if ($this->sock)
				$this->close();
			
			if ($this->db)
				mysql_close($this->db);
			
			$this->serviceDestruct();
		}
		
		
		function db_connect()
		{
			if ($this->db > 0) {
				mysql_close();
				$this->db = 0;
			}

			if (!($this->db = @mysql_connect(DB_HOST, DB_USER, DB_PASS))) {
				debug("MySQL Error: ". mysql_error());
				debug("Cannot run without a database!");
				exit();
			}
			
			if (!mysql_select_db(DB_NAME)) {
				debug("MySQL Error: ". mysql_error());
				debug("Cannot run without a database!");
				exit();
			}
		}
		
		
		function loadConfig()
		{
			if (!file_exists(SERVICE_CONFIG_FILE)) {
				die('Cannot find service configuration file.');
			}
			
			$this->config = parse_ini_file(SERVICE_CONFIG_FILE);
			
			foreach ($this->config as $conf_var => $conf_val) {
				$conf_var = strtolower($conf_var);
				$def_var = strtoupper($conf_var);
				
				if (!defined($def_var)) {
					if ($conf_var == 'server_num')
						$conf_val = irc_intToBase64($conf_val, BASE64_SERVLEN);
					
					define(strtoupper($def_var), $conf_val);
				}
			}
			
			$this->db_connect();
			
			$this->loadCommandInfo();
			$this->loadAccounts();
			$this->serviceLoad();
			
			$this->addServer('', SERVER_NUM, SERVER_NAME, SERVER_DESC, START_TIME, SERVER_MAXUSERS, SERVER_MODES);
			$this->default_bot = $this->addBot(BOT_NICK, BOT_IDENT, BOT_HOST, BOT_DESC, START_TIME, BOT_IP, BOT_MODES);

			if (defined('REPORT_EVENTS') && REPORT_EVENTS && defined('EVENT_CHANNEL')) {
				$this->addChannel(EVENT_CHANNEL, START_TIME, EVENT_CHANMODES);
				$this->addChannelUser(EVENT_CHANNEL, $this->default_bot->getNumeric(), 'o');
			}

			if (defined('REPORT_COMMANDS') && REPORT_COMMANDS && defined('COMMAND_CHANNEL')) {
				$this->addChannel(COMMAND_CHANNEL, START_TIME, COMMAND_CHANMODES);
				$this->addChannelUser(COMMAND_CHANNEL, $this->default_bot->getNumeric(), 'o');
			}
		}
		
		
		function loadAccounts()
		{
			$n = 0;
			$res = db_query('select * from accounts order by lower(name) asc');
			while ($row = mysql_fetch_assoc($res)) {
				$account_key = strtolower($row['name']);
				$account = new DB_User($row);
				
				$this->accounts[$account_key] = $account;
				$n++;
			}
			
			debug("Loaded $n account records.");
		}


		function loadSingleAccount($name_or_id)
		{
			$name_or_id = addslashes($name_or_id);
			
			if (is_numeric($name_or_id))
				$criteria = "account_id = '$name_or_id'";
			else
				$criteria = "name = '$name_or_id'";

			$res = db_query('select * from accounts where '. $criteria);
			if ($row = mysql_fetch_assoc($res)) {
				$account_key = strtolower($row['name']);
				$account = new DB_User($row);

				$this->accounts[$account_key] = $account;
			}

			if (isset($account))
				debug("Loaded single account record for {$account->getName()}.");

			return isset($account);
		}
		
		
		function loadCommandInfo()
		{
			$commands_file = SERVICE_DIR .'/commands.php';
			
			if (file_exists($commands_file)) {
				$this->command_info = array();
				include($commands_file);
			}


			/**
			 * Set up the array we use for the SHOWCOMMANDS command. It's very
			 * expensive to sort this list and hunt/peck based on a user's level
			 * every time the command is issued.
			 */
			$tmp_commands = array();
			$this->commands_list = array();

			foreach ($this->command_info as $command_key => $command_info) {
				$level = $command_info['level'];
				$tmp_commands[$level][] = $command_key;
			}

			krsort($tmp_commands);
			foreach ($tmp_commands as $level => $commands) {
				asort($commands);
				$this->commands_list[$level] = implode(' ', $commands);
			}
		}
		
		
		function setCommandInfo($command_name, $level = 0, $min_arg_count = 0, $hidden = false, $syntax = '')
		{
			$command_name = strtolower($command_name);
			
			$this->command_info[$command_name] = array(
				'level'         =>  $level,
				'syntax'        =>  $syntax,
				'arg_count'     =>  $min_arg_count,
				'hidden'        =>  $hidden
			);
			
			return $this->command_info[$command_name];
		}


		function commandExists($command_name)
		{
			$command_name = strtolower($command_name);
			return array_key_exists($command_name, $this->command_info);
		}


		function getCommandLevel($command_name)
		{
			$command_name = strtolower($command_name);
			
			if (array_key_exists($command_name, $this->command_info))
				return $this->command_info[$command_name]['level'];
			
			return 0;
		}
		
		
		function getCommandSyntax($command_name)
		{
			$command_name = strtolower($command_name);
			
			if (array_key_exists($command_name, $this->command_info))
				return $this->command_info[$command_name]['syntax'];
			
			return '';
		}
		
		
		function getCommandArgCount($command_name)
		{
			$command_name = strtolower($command_name);
			
			if (array_key_exists($command_name, $this->command_info))
				return $this->command_info[$command_name]['arg_count'];
			
			return 0;
		}
		
		
		function connect()
		{
			$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			
			if (!socket_connect($this->sock, UPLINK_HOST, UPLINK_PORT)) {
				die('Could not connect to '. UPLINK_HOST .':'. UPLINK_PORT);
				return false;
			}
			
			socket_set_block($this->sock);
			socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => SOCKET_TIMEOUT, 'usec' => 0));
			socket_set_option($this->sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => SOCKET_TIMEOUT, 'usec' => 0));

			$maxusers = irc_intToBase64(SERVER_MAXUSERS, BASE64_MAXUSERLEN);
			
			$this->sendf(FMT_PASS, UPLINK_PASS);
			$this->sendf(FMT_SERVER_SELF, SERVER_NAME, time(), time(), SERVER_NUM, $maxusers, SERVER_MODES, SERVER_DESC);
		}
		

		function close($reason = 'Services terminating.')
		{
			$this->serviceClose($reason);
			usleep(5000);
			$this->sendf(FMT_SQ, SERVER_NUM, SERVER_NAME, 0, $reason);
			usleep(5000);
			@socket_shutdown($this->sock);
			@socket_close($this->sock);
			$this->sock = 0;
		}
		
		
		function sendf($format)
		{
			$args = func_get_args();
			$format = array_shift($args);
			
			$buffer = vsprintf($format, $args);
			$buffer = rtrim($buffer, " ");
			$buffer .= "\n";
			
			socket_write($this->sock, $buffer);
			$this->bytes_sent += strlen($buffer);
			$this->lines_sent++;
			
			if (!preg_match("/^.. [GZ] /", $buffer))
				debug("[SEND] ". trim($buffer));
		}

		
		function burstGlines()
		{
		}
		
		
		function burstServers()
		{
			foreach ($this->servers as $num => $s) {
				if (!$s->isJupe() || $s->getNumeric() == SERVER_NUM)
					continue;
				
				$b64_maxusers = irc_intToBase64($s->getMaxUsers(), BASE64_MAXUSERLEN);
				debugf('Server %s: max users is %d (%s) (max %s)', $s->getName(), $s->getMaxUsers(), $b64_maxusers, BASE64_MAXUSERLEN);

				$this->sendf(FMT_SERVER, SERVER_NUM,
					$s->getName(),
					1,
					$s->get_start_time(),
					$s->getNumeric(),
					irc_intToBase64($s->getMaxUsers(), BASE64_MAXUSERLEN),
					$s->getModes(),
					$s->getDesc());
			}
		}
		
		
		function burstUsers()
		{
			foreach ($this->users as $num => $b) {
				if (!$b->isBot())
					continue;
				
				$this->sendf(FMT_NICK, SERVER_NUM, 
					$b->nick, 
					1, 
					START_TIME, 
					$b->ident, 
					$b->host,
					$b->getModes(),
					irc_ipToBase64($b->ip),
					$num,
					$b->desc);
			}
		}


		function burstChannels()
		{
			foreach ($this->channels as $key => $c) {
				$userlist = $c->getBurstUserlist();
				$banlist = $c->getBurstBanlist();
				$topic = $c->getTopic();
				
				if (empty($userlist))
					continue;

				if ($c->modes > 0 && !empty($banlist)) {
					$this->sendf(FMT_BURST_MODES_BANS, SERVER_NUM, 
						$c->getName(),
						$c->getTs(),
						$c->getModes(),
						$c->getBurstUserlist(),
						$banlist);
				}
				elseif ($c->modes > 0) {
					$this->sendf(FMT_BURST_MODES, SERVER_NUM, 
						$c->getName(),
						$c->getTs(),
						$c->getModes(),
						$c->getBurstUserlist());
				}
				elseif (!empty($banlist)) {
					$this->sendf(FMT_BURST_BANS, SERVER_NUM, 
						$c->getName(),
						$c->getTs(),
						$c->getBurstUserlist(),
						$banlist);
				}
				else {
					$this->sendf(FMT_BURST, SERVER_NUM, 
						$c->getName(),
						$c->getTs(),
						$c->getBurstUserlist());
				}
				
				if (!empty($topic))
					$this->topic($c->getName(), $topic, $c->getTs());
			}
		}
		
		
		function getNextNumeric()
		{
			$strnum = '';
			
			do {
				if ($this->numeric_count++ == BASE64_USERMAX)
					$this->numeric_count = 0;
				$strnum = SERVER_NUM . irc_intToBase64($this->numeric_count, BASE64_USERLEN);
			
			} while(array_key_exists($strnum, $this->users));
			
			return $strnum;
		}
		
		
		function addServer($uplink, $num, $name, $desc, $start_ts, $max_users, $modes = "")
		{
			$this->servers[$num] = new Server($uplink, $num, $name, $desc, $start_ts, $max_users, $modes);
			return $this->servers[$num];
		}
		
		
		function removeServer($numeric)
		{
			if (!array_key_exists($numeric, $this->servers))
				return false;
			
			foreach ($this->servers[$numeric]->users as $user_numeric)
				$this->removeUser($user_numeric);
			
			foreach ($this->servers as $downlink_numeric => $server) {
				if ($server->getUplinkNumeric() == $numeric)
					$this->removeServer($downlink_numeric);
			}
			
			unset($this->servers[$numeric]);
		}
		
		
		function addBot($nick, $ident, $host, $desc, $start_ts, $ip = "0.0.0.0", $modes = "i")
		{
			$num = $this->getNextNumeric();
			$this->users[$num] = new Bot($num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes, $this);
			$this->servers[SERVER_NUM]->addUser($num);
			return $this->users[$num];
		}
		
		
		function addUser($num, $nick, $ident, $host, $desc, $start_ts, $ip = "0.0.0.0", $modes = "i", $account = "", $account_ts = 0)
		{
			$server = substr($num, 0, BASE64_SERVLEN);
			$this->users[$num] = new User($num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes, $account, $account_ts);
			$this->servers[$server]->addUser($num);
			return $this->users[$num];
		}
		
		
		function addAccount(&$account_obj)
		{
			$account_key = strtolower($account_obj->getName());
			$this->accounts[$account_key] = $account_obj;
			
			return $this->accounts[$account_key];
		}
		
		
		function isNumericService($num)
		{
			return ($user = $this->getUser($num)) && $user->isBot();
		}
		
		
		function removeUser($num)
		{
			if (!$this->users[$num])
				return false;
			
			foreach ($this->users[$num]->channels as $chan_index)
				$this->removeChannelUser($chan_index, $num);
			
			$server_num = substr($num, 0, BASE64_SERVLEN);
			$this->servers[$server_num]->removeUser($num);
			
			unset($this->users[$num]);
		}
		
		
		function addGline($host, $duration, $lastmod, $reason = "")
		{
			$gline_key = strtolower($host);
			$this->glines[$gline_key] = new GLine($host, $duration, $lastmod, $reason);
			
			if (method_exists($this, 'serviceAddGline'))
				$this->serviceAddGline($host, $duration, $lastmod, $reason);

			return $this->glines[$gline_key];
		}


		function getGline($host)
		{
			$gline_key = strtolower($host);
			if (array_key_exists($gline_key, $this->glines))
				return $this->glines[$gline_key];
			
			return false;
		}
		
		
		function enforceGline($gline)
		{
			if (!isGline($gline) && !($gline = $this->getGline($gline)))
				return false;
			
			$this->sendf(FMT_GLINE_ADD, SERVER_NUM, $gline->getMask(), 
				$gline->getDuration(), $gline->getLastmodTs(), 
				$gline->getReason());
			return true;
		}
		
		
		function removeGline($host)
		{
			$gline_key = strtolower($host);
			if (!array_key_exists($gline_key, $this->glines))
				return;
			
			unset($this->glines[$gline_key]);

			if (method_exists($this, 'serviceRemoveGline'))
				$this->serviceRemoveGline($host);
		}
		
		
		function addJupe($server, $duration, $last_mod, $reason)
		{
			$jupe_key = strtolower($server);
			$this->jupes[$jupe_key] = new Jupe($server, $duration, $last_mod, $reason);

			if (method_exists($this, 'serviceAddJupe'))
				$this->serviceAddJupe($server, $duration, $last_mod, $reason);

			return $this->jupes[$jupe_key];
		}
		

		function getJupe($server)
		{
			$jupe_key = strtolower($server);
			if (array_key_exists($jupe_key, $this->jupes))
				return $this->jupes[$jupe_key];

			return false;
		}


		function removeJupe($server)
		{
			$jupe_key = strtolower($server);
			if (!array_key_exists($jupe_key, $this->jupes))
				return;

			unset($this->jupes[$jupe_key]);

			if (method_exists($this, 'serviceRemoveJupe'))
				$this->serviceRemoveJupe($server);
		}

		
		function getMatchingUserhostCount($mask)
		{
			$match_count = 0;
			
			foreach ($this->users as $numeric => $user) {
				if (fnmatch($mask, $user->getFullMask()) && !$user->isBot())
					$match_count++;
			}
			
			return $match_count;
		}


		function getCloneCount($ip)
		{
			$count = 0;

			foreach ($this->users as $numeric => $user) {
				if ($user->getIp() == $ip)
					$count++;
			}

			return $count;
		}
		
		
		function addChannel($name, $ts, $modes = "", $key = "", $limit = 0)
		{
			$index = strtolower($name);
			$this->channels[$index] = new Channel($name, $ts, $modes, $key, $limit);
			return $this->channels[$index];
		}
		
		
		function removeChannel($name)
		{
			$chan_key = strtolower($name);
			foreach ($this->channels[$chan_key]->users as $numeric)
				$this->users[$numeric]->removeChannel($chan_key);
			
			unset($this->channels[$chan_key]);
		}
		
		
		function addChannelUser($name, $numeric, $modes = "")
		{
			$index = strtolower($name);
			$this->channels[$index]->addUser($numeric, $modes);
			$this->users[$numeric]->addChannel($index);
		}
		
		
		function getChannelNumericsByMask($name, $mask = '*')
		{
			$chan = $this->getChannel($name);
			$numerics = array();
			
			if (!$chan)
				return false;
			
			foreach ($chan->users as $numeric => $chan_user) {
				$user = $this->getUser($numeric);
				if ($user && ($mask == '*' || fnmatch($mask, $user->getFullMask())))
					$numerics[] = $numeric;
			}
			
			if (count($numerics) == 0)
				return false;
			
			return $numerics;
		}
		
		
		function getChannelUsersByMask($name, $mask = '*')
		{
			$chan = $this->getChannel($name);
			$numerics = array();
			
			if (!$chan)
				return false;
			
			foreach ($chan->users as $numeric => $chan_user) {
				$user = $this->getUser($numeric);
				if ($user && ($mask == '*' || fnmatch($mask, $user->getFullMask())))
					$numerics[$numeric] = $user;
			}
			
			if (count($numerics) == 0)
				return false;
			
			return $numerics;
		}
		
		
		function removeChannelUser($chan_name, $num)
		{
			$chan_key = strtolower($chan_name);

			if (array_key_exists($num, $this->users)) {
				$this->users[$num]->removeChannel($chan_key);
			}
			
			if (array_key_exists($chan_key, $this->channels)) {
				$this->channels[$chan_key]->removeUser($num);
				
				if ($this->channels[$chan_key]->getUserCount() == 0)
					$this->removeChannel($chan_key);
			}
		}


		function removeUserFromAllChannels($num)
		{
			if (!($u = $this->getUser($num)))
				return false;
			
			$channels = $u->channels;
			
			foreach ($channels as $chan_key)
				$this->removeChannelUser($chan_key, $num);
			
			$u->removeAllChannels();
			
			return true;
		}
		
		
		function addBan($name, $mask)
		{
			$index = strtolower($name);
			$this->channels[$index]->addBan($mask);
		}
		
		
		function getServer($numeric)
		{
			if (array_key_exists($numeric, $this->servers))
				return $this->servers[$numeric];
			
			return false;
		}
		
		
		function getServerByName($name)
		{
			$name = strtolower($name);
			foreach ($this->servers as $numeric => $server)
				if (strtolower($server->getName()) == $name)
					return $server;
			
			return false;
		}
		
		
		function getChannel($chan_name)
		{
			$chan_key = strtolower($chan_name);
			if (array_key_exists($chan_key, $this->channels))
				return $this->channels[$chan_key];
			
			return false;
		}
		
		
		function getUser($numeric)
		{
			if (array_key_exists($numeric, $this->users))
				return $this->users[$numeric];
			
			return false;
		}
		
		
		function getUserByNick($nick)
		{
			$nick = strtolower($nick);
			foreach ($this->users as $numeric => $user)
				if (strtolower($user->getNick()) == $nick)
					return $user;
			
			return false;
		}
		
		
		function getAccount($account_name)
		{
			$account_key = strtolower($account_name);
			if (array_key_exists($account_key, $this->accounts))
				return $this->accounts[$account_key];
			
			return false;
		}
		

		function getAccountById($account_id)
		{
			foreach ($this->accounts as $account_key => $account) {
				if ($account->getId() == $account_id)
					return $this->accounts[$account_key];
			}
			
			return false;
		}
		

		function getAccountByEmail($email)
		{
			$email = strtolower($email);
			
			foreach ($this->accounts as $account_key => $account) {
				if (strtolower($account->getEmail()) == $email)
					return $this->accounts[$account_key];
			}
			
			return false;
		}


		function removeAccount($account)
		{
			if (isAccount($account))
				$account = $account->getName();
			elseif (is_object($account))
				return false; // What kind of object are you giving me?!

			$account_key = strtolower($account);

			if (!array_key_exists($account_key, $this->accounts))
				return false;

			unset($this->accounts[$account_key]);
			return true;
		}
		
		
		function main()
		{
			$err_no = 0;
			$err_str = '';
			$iter = 0;
			$timeout = 5;
			$buffer = '';
			
			$noncritical_socket_errors = array(
				0,    // no error
				11,   // no data to read (resource temporarily unavailable),
				35    // no data to read (resource temporarily unavailable - BSD)
			);
			
			$GLOBALS['INSTANTIATED_SERVICES'][] = $this;
			
			while (is_resource($this->sock) && in_array($err_no, $noncritical_socket_errors)) {
				$iter++;
				
				$timeout = 5;
				foreach ($this->timers as $n => $timer) {
					$secs_til_run = $timer->getNextRun() - time();
					if ($timeout > $secs_til_run && $secs_til_run >= 0)
						$timeout = $timer->getNextRun() - time();

					// debug("Timer {$timer->include_file} has {$timeout} seconds left");
				}
				
				socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $timeout, "usec" => 0));
				socket_set_option($this->sock, SOL_SOCKET, SO_SNDTIMEO, array("sec" => $timeout, "usec" => 0));
				
				$this->servicePreread();
				$buffer .= @socket_read($this->sock, 1024);
				$this->bytes_received += strlen($buffer);
				
				$break_time = time();
				foreach ($this->timers as $n => $timer) {
					if ($timer->getNextRun() <= $break_time)
						$this->runTimer($n);
				}
				
				if (!empty($buffer)) {
					$endpos = strpos($buffer, "\n");
					
					while ($endpos !== false) {
						$line = substr($buffer, 0, $endpos - 1);
						$buffer = substr($buffer, $endpos + 1);
						
						if (!preg_match("/^.. [GZ] /", $line))
							debug("[RECV] $line");
						
						$this->parse($line);
						$this->lines_received++;
						
						$endpos = strpos($buffer, "\n");
					}
				}
				else {
					$err_no = socket_last_error($this->sock);
					$err_str = socket_strerror($err_no);
				}
			}
			
			debug("SOCKET STATUS [$err_no]: $err_str");
		}
		
		
		function parse($line)
		{
			$num_args = lineNumArgs($line);
			$args = lineGetArgs($line);
			
			if ($args[0] == 'PASS' || $args[0] == 'SERVER' || $args[0] == 'NOTICE' || $args[0] == 'ERROR')
				$token = $args[0];
			else
				$token = $args[1];
			
			$handler_file = "handle_$token.php";
			$core_handler = P10_DIR . $handler_file;
			$service_handler = SERVICE_HANDLER_DIR . $handler_file;
			
			$chan_key = '';
			$chan_name = '';
			$bot = false;
			
			for ($i = 0; $i < count($args); ++$i) {
				if ($args[$i][0] == '#') {
					$chan_name = $args[$i];
					$chan_key = strtolower($chan_name);
					break;
				}
			}
			
			if ($num_args >= 3 && !(($bot = $this->getUser($args[2])) && $bot->isBot()))
				$bot = $this->default_bot;
			
			if (file_exists($core_handler))
				include($core_handler);
			if (file_exists($service_handler))
				include($service_handler);
			
			// TODO: Breaks everything; slated for 1.3 or 2.0
			//$core_result = $this->handlerContainer($core_handler, true, $num_args, $args, $chan_name, $chan_key, $bot);
			//$service_result = $this->handlerContainer($service_handler, false, $num_args, $args, $chan_name, $chan_key, $bot);
			//return $core_result && $service_result;

			return true;
		}
		
		
		function handlerContainer($handler_file, $is_core_handler, $num_args, $args, $chan_name, $chan_key, $bot)
		{
			if (file_exists($handler_file))
				include($handler_file);
			elseif ($is_core_handler)
				debug("*** Core handler file $handler_file does not exist!");
			
			return true;
		}
		
		
		/**
		 * cleanModes: Returns a clean and parsed version of the MODE string passed
		 * in the first argument.
		 * 
		 * This method does NOT parse a full MODE line; only a channel mode string line
		 * that is structured like so:
		 *     +ntxyzkl key limit
		 * Then, returns a cleaned string with valid and accepted modes:
		 *     +ntkl key limit
		 * If $accept_user_modes is set to true, it will not remove any +o/+v/+b mode
		 * changes. Otherwise, they will be cleansed.
		 * 
		 * This is currently useful for services which accept a mode change from users,
		 * but where a subset of modes should not be accepted; for instance, setting the
		 * default mode string on a registered channel in the channel service. In such 
		 * a case, you wouldn't want users setting -R, -A, -o CS, and so on.
		 */
		function cleanModes($modes, $accept_user_modes = false)
		{
			$cleanModes = '+';
			$clean_mode_args = '';
			$disallowed_modes = array('R', 'd', 'A', 'U');
			$param_modes_add = array('l', 'k', 'U', 'A', 'o', 'v', 'b');
			$param_modes_sub = array('k', 'U', 'A', 'o', 'v', 'b');
			
			if (!$accept_user_modes) {
				$disallowed_modes = array_merge($disallowed_modes, 
					array('o', 'v', 'b'));
			}
			
			$in_arg = 0;
			$in_args = split(' ', $modes);
			if (count($in_args) > 1) {
				$modes = array_shift($in_args);
			}
			
			$is_sub = false;
			for ($i = 0; $i < strlen($modes); $i++) {
				$arg = '';
				$mode = $modes[$i];
				
				if ($mode == '-')
					$is_sub = true;
				if ($mode == '+')
					$is_sub = false;
				
				if ((!$is_sub && in_array($mode, $param_modes_add)) 
						|| ($is_sub && in_array($mode, $param_modes_sub)))
					$arg = $in_args[$in_arg++];
				
				if (!Channel::isValidMode($mode) 
						|| in_array($mode, $disallowed_modes) 
						|| $is_sub 
						|| preg_match('/'. $mode .'/', $cleanModes)) {
					continue;
				}
				
				$cleanModes .= $mode;
				
				if (!empty($arg)) {
					$clean_mode_args .= ' '. $arg;
				}
			}
			
			return $cleanModes . $clean_mode_args;
		}
		
		
		function parseMode($full_line)
		{
			/**
			 * AEBIO M brian :-i
			 * AEBIO M brian :+id
			 * AEBIO M brian :-d
			 * Vs M #radio +o AEBIO 0
			 * AEBIO M #radio +v AEBIO
			 * AEBIO M #radio -v AEBIO
			 * AEBIO M #radio -o+smv AEBIO AEBIO
			 * AEBIO M #coder-com +ilk 50 haha
			 */

			$args = explode(' ', $full_line);
			
			$target = $args[2];
			$is_chan = ($target[0] == '#');
			$readable_args = array();

			if ($is_chan) {
				$modes = $args[3];
				$mode_arg = 4;
				$chan = $this->getChannel($target);
				$add = '';

				for ($i = 0; $i < strlen($modes); ++$i) {
					$mode = $modes[$i];

					if ($mode == '+') {
						$add = true;
					}
					elseif ($mode == '-') {
						$add = false;
					}
					elseif ($mode == 'l') {
						if ($add) {
							$limit = $args[$mode_arg++];
							$chan->addMode($mode);
							$chan->setLimit($limit);
							$readable_args[] = $limit;
						}
						else {
							$chan->removeMode($mode);
							$chan->setLimit(0);
						}
					}
					elseif ($mode == 'k') {
						if ($add) {
							$key = $args[$mode_arg++];
							$chan->addMode($mode);
							$chan->setKey($key);
							$readable_args[] = $key;
						}
						else {
							$key = $args[$mode_arg++];
							$chan->removeMode($mode);
							$chan->setKey('');
							$readable_args[] = $key;
						}
					}
					elseif ($mode == 'A') {
						if ($add) {
							$apass = $args[$mode_arg++];
							$chan->addMode($mode);
							$chan->setAdminPass($apass);
							$readable_args[] = $apass;
						}
						else {
							$apass = $args[$mode_arg++];
							$chan->removeMode($mode);
							$chan->setAdminPass('');
							$readable_args[] = $apass;
						}
					}
					elseif ($mode == 'U') {
						if ($add) {
							$upass = $args[$mode_arg++];
							$chan->addMode($mode);
							$chan->setUserPass($upass);
							$readable_args[] = $upass;
						}
						else {
							$upass = $args[$mode_arg++];
							$chan->removeMode($mode);
							$chan->setUserPass('');
							$readable_args[] = $upass;
						}
					}
					elseif ($mode == 'o') {
						$numeric = $args[$mode_arg++];
						$oplevel = 0;
						$has_oplevel = (strlen($numeric) > 5 && $numeric[5] == ':');
						
						if ($has_oplevel) {
							$oplevel = substr($numeric, 6);
							$numeric = substr($numeric, 0, 5);
						}
						
						if ($add) {
							$chan->addOp($numeric);
							
							if ($has_oplevel)
								$chan->setOplevel($numeric, $oplevel);
						}
						else
							$chan->removeOp($numeric);

						$user = $this->getUser($numeric);
						$readable_args[] = $user->getNick();
					} 
					elseif ($mode == 'v') {
						$numeric = $args[$mode_arg++];
						if ($add)
							$chan->addVoice($numeric);
						else
							$chan->removeVoice($numeric);

						$user = $this->getUser($numeric);
						$readable_args[] = $user->getNick();
					}
					elseif ($mode == 'b') {
						$mask = $args[$mode_arg++];
						if ($add)
							$chan->addBan($mask, time());
						else
							$chan->removeBan($mask);

						$readable_args[] = $mask;
					}
					else {
						if ($add)
							$chan->addMode($mode);
						else
							$chan->removeMode($mode);
					}
				}
			}
			else {
				$user = $this->getUserByNick($target);
				$modes = $args[3];

				$add = '';

				for ($i = 0; $i < strlen($modes); ++$i) {
					$mode = $modes[$i];

					if ($mode == ':') {
						continue;
					}
					if ($mode == '+') {
						$add = true;
					}
					elseif ($mode == '-') {
						$add = false;
					}
					else {
						if ($add)
							$user->addMode($mode);
						else
							$user->removeMode($mode);
					}
				}
			}
		}
		
		
		/**
		 * sendModeLine
		 * This method is responsible for accepting a mode change string performed
		 * against either a user or channel and sending it to the server as either
		 * one or several different mode change strings if it exceeds the maximum
		 * number of mode changes per line. This rule does not apply to user mode
		 * changes, only channel mode changes.
		 * 
		 * For instance, the following changes would be sent:
		 *      In:   AEBIm M brian :+owg
		 *      Out:  AEBIm M brian :+owg
		 * 
		 *      In:   AEBIm M #dev +ntooovvv AEBf6 AEBf7 AEBf8 AEBf9 AEBfa Cm6Dq 112603551
		 *      Out:  AEBIm M #dev +ntooov AEBf6 AEBf7 AEBf8 AEBf9 112603551
		 *            AEBIm M #dev +vv AEBfa CM6Dq
		 * 
		 *            Note that two lines are actually sent here, since ircu limits
		 *            mode changes to six per line.
		 */
		function sendModeLine($proto_str)
		{
			$args = explode(' ', $proto_str);
			$source = $args[0];
			$target = $args[2];
			$outgoing = array();
			$param_modes = array('l', 'k', 'A', 'U', 'b', 'v', 'o');
			$mode_count = 0;
			
			$is_chan = ($target[0] == '#');
			
			if ($is_chan) {
				$chan = $this->getChannel($target);
				$modes = $args[3];
				$arg_num = 4;
				$tmp_modes = '';
				$tmp_pol = '';
				$tmp_args = array();
				$rem_args = array_copy($args, $arg_num);
				
				for ($i = 0; $i < strlen($modes); $i++) {
					$mode = $modes[$i];
					$tmp_modes .= $mode;
					
					if ($mode == '+' || $mode == '-') {
						$tmp_pol = $mode;
						continue;
					}
					
					if (in_array($mode, $param_modes)) {
						$tmp_args[] = $args[$arg_num++];
						array_shift($rem_args);
					}
					
					if (++$mode_count == MAX_MODES_PER_LINE || $i == strlen($modes) - 1) {
						$outgoing[] = irc_sprintf("%s M %s %s%s%A", $source, $target, $tmp_modes, 
							count($tmp_args) > 0 ? ' ' : '', 
							$tmp_args);
						$mode_count = 0;
						$tmp_modes = $tmp_pol;
						$tmp_args = array();
					}
				}
				
				foreach ($outgoing as $tmp_line) {
					/**
					 * If our remaining arguments array has one numeric value left over
					 * that did not correspond to a mode, then it is probably a mode
					 * hack timestamp. Append it to the previously generated line.
					 */
					if (count($rem_args) == 1 && is_numeric($rem_args[0]))
						$chan_ts = $rem_args[0];
					else
						$chan_ts = $chan->getTs();

					$tmp_line .= ' '. $chan_ts;
					
					$this->sendf($tmp_line);
					$this->parseMode($tmp_line);
				}
			}
			else {
				$outgoing[] = $proto_str;
				// TODO: This portion isn't actually used anywhere yet... but isn't there something missing here?
			}
		}


		/**
		 * sendMode
		 * This method simply formats and sends along a mode change string
		 * to sendModeLine for further processing.
		 */
		function sendMode($source, $chan_name, $modes)
		{
			if (isServer($source) || isUser($source))
				$source = $source->getNumeric();
			if (isChannel($chan_name))
				$chan_name = $chan_name->getName();

			$mode_line = irc_sprintf(FMT_MODE_NOTS, $source, $chan_name, $modes);
			$this->sendModeLine($mode_line);
		}
		
		
		function addTimer($repeats, $ts_interval, $include_file)
		{
			$data = array();
			
			for ($i = 3; $i < func_num_args(); ++$i)
				$data[] = func_get_arg($i);
			
			$this->timers[] = new Timer($repeats, $ts_interval, $include_file, $data);
		}
		
		
		function runTimer($timer_num)
		{
			if (!array_key_exists($timer_num, $this->timers))
				return;
			
			// debug("Running timer {$timer_num}");
			$this->executeTimer($timer_num);
			
			$timer = $this->timers[$timer_num];
			if ($timer->isRecurring()) {
				$timer->update();
			}
			else {
				// debug("Removing timer {$timer_num}");
				unset($this->timers[$timer_num]);
			}
		}
		
		
		function executeTimer($timer_num)
		{
			if (!array_key_exists($timer_num, $this->timers))
				return;
			
			$timer = $this->timers[$timer_num];
			$core_script = CORE_TIMER_DIR . $timer->getInclude();
			$service_script = SERVICE_TIMER_DIR . $timer->getInclude();
			
			$bot = $this->default_bot;
			$timer_data = $timer->getDataElements();
			
			if (file_exists($core_script))
				include($core_script);
			if (file_exists($service_script))
				include($service_script);
			
			$timer->setDataElements($timer_data);
			
			return true;
		}

		
		function performChanUserMode($source_num, $chan_name, $mode_pol, $mode_char, $arg_list)
		{
			$args = array();
			$chan = $this->getChannel($chan_name);
			
			if (!$chan)
				return;
			
			if (!is_array($arg_list)) {
				$arg_list = array();
				$arg_count = func_num_args();

				for ($i = 4; $i < $arg_count; ++$i)
					$arg_list[] = func_get_arg($i);
			}

			$mode_str = $mode_pol . str_repeat($mode_char, count($arg_list));
			$mode_args = implode(' ', $arg_list);
			$mode_line = irc_sprintf(FMT_MODE_HACK, $source_num, $chan->getName(), $mode_str, $mode_args, $chan->getTs());
			return $this->sendModeLine($mode_line);
		}
		
		function op($chan_name, $num_list)       { return $this->performChanUserMode(SERVER_NUM, $chan_name, '+', 'o', $num_list); }
		function deop($chan_name, $num_list)     { return $this->performChanUserMode(SERVER_NUM, $chan_name, '-', 'o', $num_list); }
		function voice($chan_name, $num_list)    { return $this->performChanUserMode(SERVER_NUM, $chan_name, '+', 'v', $num_list); }
		function devoice($chan_name, $num_list)  { return $this->performChanUserMode(SERVER_NUM, $chan_name, '-', 'v', $num_list); }
		function ban($chan_name, $num_list)      { return $this->performChanUserMode(SERVER_NUM, $chan_name, '+', 'b', $num_list); }
		function unban($chan_name, $num_list)    { return $this->performChanUserMode(SERVER_NUM, $chan_name, '-', 'b', $num_list); }

		function topic($chan_name, $topic, $chan_ts = 0)
		{
			if (TOPIC_BURSTING && $chan_ts == 0)
				return false;
			
			if (TOPIC_BURSTING)
				$this->sendf(FMT_TOPIC, SERVER_NUM, $chan_name, $chan_ts, time(), $topic);
			else
				$this->sendf(FMT_TOPIC, SERVER_NUM, $chan_name, $topic);
			
			$chan = $this->getChannel($chan_name);
			$chan->setTopic($topic);
		}
		
		
		function clearModes($chan_name)
		{
			$chan = $this->getChannel($chan_name);
			$chan->clearModes();
			$this->sendf(FMT_MODE, SERVER_NUM, $chan_name, '-psmntirlk *', $chan->getTs());
		}
		
		function mode($chan_name, $modes)
		{
			$chan = $this->getChannel($chan_name);
			$this->sendf(FMT_MODE, SERVER_NUM, $chan_name, $modes, $chan->getTs());
		}
		
		function kick($chan_name, $numerics, $reason)
		{
			if (!is_array($numerics))
				$numerics = array($numerics);
			
			foreach ($numerics as $numeric) {
				$this->removeChannelUser($chan_name, $numeric);
				$this->sendf(FMT_KICK, SERVER_NUM, $chan_name, $numeric, $reason);
			}
		}
		
		function kill($user_num, $reason = 'So long...')
		{
			if (isUser($user_num))
				$user_num = $user_num->getNumeric();
			
			if (!($user = $this->getUser($user_num)))
				return false;
			
			$this->sendf(FMT_KILL, SERVER_NUM, $user_num, SERVER_NAME, $reason);
			$this->removeUser($user_num);
		}

		function notifyServices($type, $request, $primary_id, $secondary_id = 0)
		{
			$text = '|IPSVC|'. $type .'|'. $request .'|'. $primary_id .'|';

			if ($type == NOTIFY_CHANNEL_ACCESS && $secondary_id > 0)
				$text .= $secondary_id .'|';
			elseif ($type == NOTIFY_CHANNEL_ACCESS)
				return false;

			debugf('Notify text: %s', $text);

			foreach ($this->users as $numeric => $user) {
				if (!$user->isService()) {
					debugf('%s is not a service, skipping (%s)', $user->getNick(), $user->getModes());
					continue;
				}

				debugf('%s *IS* a service, sending notice (%s)', $user->getNick(), $user->getModes());

				$this->default_bot->notice($user, $text);
			}
		}

		function reportCommand($user, $args)
		{
			if (!defined('REPORT_COMMANDS') || !REPORT_COMMANDS || !defined('COMMAND_CHANNEL'))
				return;

			$command = array_shift($args);
			$log_msg = irc_sprintf('[%'. NICK_LEN .'H] %s%s%s %A',
				$user, BOLD_START, $command, BOLD_END, $args);

			$this->default_bot->message(COMMAND_CHANNEL, $log_msg);
		}
	}
	

