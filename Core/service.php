<?php

	require_once( 'core_globals.php' );
	require_once( CORE_DIR .'/stringutils.php' );
	require_once( CORE_DIR .'/dbutils.php' );
	
	require_once( CORE_DIR .'/p10.php' );
	
	require_once( CORE_DIR .'/server.php' );
	require_once( CORE_DIR .'/channel.php' );
	require_once( CORE_DIR .'/gline.php' );
	require_once( CORE_DIR .'/user.php' );
	require_once( CORE_DIR .'/bot.php' );
	require_once( CORE_DIR .'/db_user.php' );
	require_once( CORE_DIR .'/timer.php' );
	
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
		
		var $bots = array();
		
		var $command_info = array();
		var $command_list = array();
		
		var $numeric_count = -1;
		
		var $finished_burst = false;
		
		
		function __construct()
		{
			if( !defined('SERVICE_DIR') )
				die( "The service class cannot run by itself.\n" );
			
			if( !defined('SERVICE_CONFIG_FILE') )
				define( 'SERVICE_CONFIG_FILE', 'service.ini' );
				
			/**
			 * The following methods are required for child classes. A service cannot exist
			 * if it does not implement these methods.
			 */
			if( !method_exists($this, 'service_construct') )
				die( "You have not defined a service constructor (service_construct)." );
			if( !method_exists($this, 'service_destruct') )
				die( "You have not defined a service destructor (service_destruct)." );
			if( !method_exists($this, 'service_load') )
				die( "You have not defined a service data loader method (service_load)." );
			if( !method_exists($this, 'service_preburst') )
				die( "You have not defined a pre-burst method (service_preburst)." );
			if( !method_exists($this, 'service_preread') )
				die( "You have not defined a pre-read method (service_preread)." );
			if( !method_exists($this, 'service_close') )
				die( "You have not defined a service close method (service_close)." );
			if( !method_exists($this, 'service_main') )
				die( "You have not defined a main service method (service_main)." );
			
			define( 'START_TIME', time() );
			define( 'SERVICE_VERSION', SERVICE_NAME .' v'.
				SERVICE_VERSION_MAJOR .'.'. SERVICE_VERSION_MINOR .'.'. SERVICE_VERSION_REV );
			
			$this->add_timer( true, 5, 'expire_glines.php' );
			$this->add_timer( true, 5, 'refresh_data.php' );
			
			$this->service_construct();
			$this->load_config();
			$this->connect();
			$this->main();
		}
		
		function __destruct()
		{
			if( $this->sock )
				$this->close();
			
			if( $this->db )
				mysql_close( $this->db );
			
			$this->service_destruct();
		}
		
		
		function db_connect()
		{
			if( $this->db > 0 )
			{
				mysql_close();
				$this->db = 0;
			}

			if( !($this->db = @mysql_connect(DB_HOST, DB_USER, DB_PASS)) )
			{
				debug( "MySQL Error: ". mysql_error() );
				debug( "Cannot run without a database!" );
				exit();
			}
			
			if( !mysql_select_db(DB_NAME) )
			{
				debug( "MySQL Error: ". mysql_error() );
				debug( "Cannot run without a database!" );
				exit();
			}
		}
		
		
		function load_config()
		{
			if( !file_exists(SERVICE_CONFIG_FILE) )
			{
				die('Cannot find service configuration file.');
			}
			
			$this->config = parse_ini_file( SERVICE_CONFIG_FILE );
			
			foreach($this->config as $conf_var => $conf_val)
			{
				$conf_var = strtolower( $conf_var );
				$def_var = strtoupper( $conf_var );
				
				if( !defined($def_var) )
				{
					if( $conf_var == 'server_num')
						$conf_val = int_to_base64( $conf_val, BASE64_SERVLEN );
					
					define( strtoupper($def_var), $conf_val );
				}
			}
			
			$this->db_connect();
			
			$this->load_command_info();
			$this->load_accounts();
			$this->service_load();
			
			$this->add_server( '', SERVER_NUM, SERVER_NAME, SERVER_DESC, START_TIME, SERVER_MAXUSERS, SERVER_MODES );
			$this->default_bot = $this->add_bot( BOT_NICK, BOT_IDENT, BOT_HOST, BOT_DESC, START_TIME, BOT_IP, BOT_MODES );
			$this->add_channel( BOT_CHAN, START_TIME, 'nts' );
			$this->add_channel_user( BOT_CHAN, $this->default_bot->get_numeric(), 'o' );
		}
		
		
		function load_accounts()
		{
			$n = 0;
			$res = db_query( 'select * from accounts order by lower(name) asc' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$account_key = strtolower( $row['name'] );
				$account = new DB_User( $row );
				
				$this->accounts[$account_key] = $account;
				$n++;
			}
			
			debug( "Loaded $n account records." );
		}
		
		
		function load_command_info()
		{
			$commands_file = SERVICE_DIR .'/commands.php';
			
			if( file_exists($commands_file) )
			{
				$this->command_info = array();
				include( $commands_file );
			}


			/**
			 * Set up the array we use for the SHOWCOMMANDS command. It's very
			 * expensive to sort this list and hunt/peck based on a user's level
			 * every time the command is issued.
			 */
			$tmp_commands = array();
			$this->commands_list = array();

			foreach( $this->command_info as $command_key => $command_info )
			{
				$level = $command_info['level'];
				$tmp_commands[$level][] = $command_key;
			}

			krsort( $tmp_commands );
			foreach( $tmp_commands as $level => $commands )
			{
				asort( $commands );
				$this->commands_list[$level] = implode( ' ', $commands );
			}
		}
		
		
		function set_command_info( $command_name, $level = 0, $min_arg_count = 0, $hidden = false, $syntax = '' )
		{
			$command_name = strtolower( $command_name );
			
			$this->command_info[$command_name] = array(
				'level'         =>  $level,
				'syntax'        =>  $syntax,
				'arg_count'     =>  $min_arg_count,
				'hidden'        =>  $hidden
			);
			
			return $this->command_info[$command_name];
		}


		function command_exists( $command_name )
		{
			$command_name = strtolower( $command_name );
			return array_key_exists( $command_name, $this->command_info );
		}


		function get_command_level( $command_name )
		{
			$command_name = strtolower( $command_name );
			
			if( array_key_exists($command_name, $this->command_info) )
				return $this->command_info[$command_name]['level'];
			
			return 0;
		}
		
		
		function get_command_syntax( $command_name )
		{
			$command_name = strtolower( $command_name );
			
			if( array_key_exists($command_name, $this->command_info) )
				return $this->command_info[$command_name]['syntax'];
			
			return '';
		}
		
		
		function get_command_arg_count( $command_name )
		{
			$command_name = strtolower( $command_name );
			
			if( array_key_exists($command_name, $this->command_info) )
				return $this->command_info[$command_name]['arg_count'];
			
			return 0;
		}
		
		
		function connect()
		{
			$this->sock = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
			
			if( !socket_connect($this->sock, UPLINK_HOST, UPLINK_PORT) )
			{
				die( 'Could not connect to '. UPLINK_HOST .':'. UPLINK_PORT );
				return false;
			}
			
			socket_set_block( $this->sock );
			socket_set_option( $this->sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => SOCKET_TIMEOUT, 'usec' => 0) );
			socket_set_option( $this->sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => SOCKET_TIMEOUT, 'usec' => 0) );
			
			$this->sendf( FMT_PASS, UPLINK_PASS );
			$this->sendf( FMT_SERVER_SELF, SERVER_NAME, SERVER_NUM, SERVER_MODES, SERVER_DESC );
		}
		

		function close( $reason = 'Services terminating.' )
		{
			$this->service_close( $reason );
			usleep( 5000 );
			$this->sendf( FMT_SQ, SERVER_NUM, SERVER_NAME, 0, $reason );
			usleep( 5000 );
			@socket_shutdown( $this->sock );
			@socket_close( $this->sock );
			$this->sock = 0;
		}
		
		
		function sendf( $format )
		{
			$buffer = '';
			$args = array();
			
			$format = str_replace( '[TS]', time(), $format );
			$format = addslashes( $format );
			
			for( $i = 1; $i < func_num_args(); ++$i )
				$args[] = addslashes( func_get_arg($i) );
			
			$arglist = join( "', '", $args );
			eval( "\$buffer = sprintf('$format', '$arglist');" );
			
			$buffer = stripslashes( $buffer );
			$buffer = rtrim( $buffer, " " );
			$buffer .= "\n";
			
			socket_write( $this->sock, $buffer );
			$this->bytes_sent += strlen( $buffer );
			$this->lines_sent++;
			
			if( !eregi("^.. [GZ] ", $buffer) )
				debug( "[SEND] ". trim($buffer) );
		}

		
		function burst_glines()
		{
		}
		
		
		function burst_servers()
		{
			foreach( $this->servers as $num => $s )
			{
				if( !$s->is_jupe() || $s->get_numeric() == SERVER_NUM )
					continue;
				
				$this->sendf( FMT_SERVER, SERVER_NUM,
					$s->get_name(),
					1,
					$s->get_start_time(),
					$s->get_numeric(),
					int_to_base64( $s->get_max_users(), BASE64_MAXUSERLEN ),
					$s->get_modes(),
					$s->get_desc() );
			}
		}
		
		
		function burst_users()
		{
			foreach( $this->users as $num => $b )
			{
				if( !$b->is_bot() )
					continue;
				
				$this->sendf( FMT_NICK, SERVER_NUM, 
					$b->nick, 
					1, 
					START_TIME, 
					$b->ident, 
					$b->host,
					$b->get_modes(),
					ip_to_base64( $b->ip ),
					$num,
					$b->desc );
			}
		}


		function burst_channels()
		{
			foreach( $this->channels as $key => $c )
			{
				$userlist = $c->get_burst_userlist();
				$banlist = $c->get_burst_banlist();
				$topic = $c->get_topic();
				
				if( empty($userlist) )
					continue;

				if( $c->modes > 0 && !empty($banlist) )
				{
					$this->sendf( FMT_BURST_MODES_BANS, SERVER_NUM, 
						$c->get_name(),
						$c->get_ts(),
						$c->get_modes(),
						$c->get_burst_userlist(),
						$banlist );
				}
				else if( $c->modes > 0 )
				{
					$this->sendf( FMT_BURST_MODES, SERVER_NUM, 
						$c->get_name(),
						$c->get_ts(),
						$c->get_modes(),
						$c->get_burst_userlist() );
				}
				else if( !empty($banlist) )
				{
					$this->sendf( FMT_BURST_BANS, SERVER_NUM, 
						$c->get_name(),
						$c->get_ts(),
						$c->get_burst_userlist(),
						$banlist );
				}
				else
				{
					$this->sendf( FMT_BURST, SERVER_NUM, 
						$c->get_name(),
						$c->get_ts(),
						$c->get_burst_userlist() );
				}
				
				if( !empty($topic) )
					$this->topic( $c->get_name(), $topic, $c->get_ts() );
			}
		}
		
		
		function get_next_numeric()
		{
			$strnum = '';
			
			do {
				if( $this->numeric_count++ == BASE64_USERMAX )
					$this->numeric_count = 0;
				$strnum = SERVER_NUM . int_to_base64( $this->numeric_count, BASE64_USERLEN );
			
			} while( array_key_exists($strnum, $this->users) );
			
			return $strnum;
		}
		
		
		function add_server( $uplink, $num, $name, $desc, $start_ts, $max_users, $modes = "" )
		{
			$this->servers[$num] = new Server( $uplink, $num, $name, $desc, $start_ts, $max_users, $modes );
			return $this->servers[$num];
		}
		
		
		function remove_server( $numeric )
		{
			if( !array_key_exists($numeric, $this->servers) )
				return false;
			
			foreach( $this->servers[$numeric]->users as $user_numeric )
				$this->remove_user( $user_numeric );
			
			foreach( $this->servers as $downlink_numeric => $server )
			{
				if( $server->get_uplink_numeric() == $numeric )
					$this->remove_server( $downlink_numeric );
			}
			
			unset( $this->servers[$numeric] );
		}
		
		
		function add_bot( $nick, $ident, $host, $desc, $start_ts, $ip = "0.0.0.0", $modes = "i" )
		{
			$num = $this->get_next_numeric();
			$this->users[$num] = new Bot( $num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes, $this );
			$this->servers[SERVER_NUM]->add_user( $num );
			return $this->users[$num];
		}
		
		
		function add_user( $num, $nick, $ident, $host, $desc, $start_ts, $ip = "0.0.0.0", $modes = "i", $account = "" )
		{
			$server = substr( $num, 0, BASE64_SERVLEN );
			$this->users[$num] = new User( $num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes, $account );
			$this->servers[$server]->add_user( $num );
			return $this->users[$num];
		}
		
		
		function add_account( &$account_obj )
		{
			$account_key = strtolower( $account_obj->get_name() );
			$this->accounts[$account_key] = $account_obj;
			
			return $this->accounts[$account_key];
		}
		
		
		function is_numeric_service( $num )
		{
			return ($user = $this->get_user($num)) && $user->is_bot();
		}
		
		
		function remove_user( $num )
		{
			if( !$this->users[$num] )
				return false;
			
			foreach( $this->users[$num]->channels as $chan_index )
				$this->remove_channel_user( $chan_index, $num );
			
			$server_num = substr( $num, 0, BASE64_SERVLEN );
			$this->servers[$server_num]->remove_user( $num );
			
			unset( $this->users[$num] );
		}
		
		
		function add_gline( $host, $duration, $reason = "" )
		{
			$gline_key = strtolower( $host );
			$this->glines[$gline_key] = new GLine( $host, $duration, $reason );
			
			if( method_exists($this, 'service_add_gline') )
				$this->service_add_gline( $host, $duration, $reason );

			return $this->glines[$gline_key];
		}
		
		
		function get_gline( $host )
		{
			$gline_key = strtolower( $host );
			if( array_key_exists($gline_key, $this->glines) )
				return $this->glines[$gline_key];
			
			return false;
		}
		
		
		function enforce_gline( $gline )
		{
			if( get_class($gline) != 'Gline' && !($gline = $this->get_gline($gline)) )
				return false;
			
			$this->sendf( FMT_GLINE_ADD, SERVER_NUM, $gline->get_mask(), 
				$gline->get_duration(), $gline->get_reason() );
			return true;
		}
		
		
		function remove_gline( $host )
		{
			$gline_key = strtolower( $host );
			if( !array_key_exists($gline_key, $this->glines) )
				return;
			
			unset( $this->glines[$gline_key] );

			if( method_exists($this, 'service_remove_gline') )
				$this->service_remove_gline( $host );
		}
		
		
		function get_matching_userhost_count( $mask )
		{
			$match_count = 0;
			
			foreach( $this->users as $numeric => $user )
			{
				if( fnmatch($mask, $user->get_full_mask()) && !$user->is_bot() )
					$match_count++;
			}
			
			return $match_count;
		}
		
		
		function add_channel( $name, $ts, $modes = "", $key = "", $limit = 0 )
		{
			$index = strtolower($name);
			$this->channels[$index] = new Channel( $name, $ts, $modes, $key, $limit );
			return $this->channels[$index];
		}
		
		
		function remove_channel( $name )
		{
			$chan_key = strtolower( $name );
			foreach( $this->channels[$chan_key]->users as $numeric )
				$this->users[$numeric]->remove_channel( $chan_key );
			
			unset( $this->channels[$chan_key] );
		}
		
		
		function add_channel_user( $name, $numeric, $modes = "" )
		{
			$index = strtolower($name);
			$this->channels[$index]->add_user( $numeric, $modes );
			$this->users[$numeric]->add_channel( $index );
		}
		
		
		function get_channel_numerics_by_mask( $name, $mask = '*' )
		{
			$chan = $this->get_channel( $name );
			$numerics = array();
			
			if( !$chan )
				return false;
			
			foreach( $chan->users as $numeric => $chan_user )
			{
				$user = $this->get_user( $numeric );
				if( $user && ($mask == '*' || fnmatch($mask, $user->get_full_mask())) )
					$numerics[] = $numeric;
			}
			
			if( count($numerics) == 0 )
				return false;
			
			return $numerics;
		}
		
		
		function get_channel_users_by_mask( $name, $mask = '*' )
		{
			$chan = $this->get_channel( $name );
			$numerics = array();
			
			if( !$chan )
				return false;
			
			foreach( $chan->users as $numeric => $chan_user )
			{
				$user = $this->get_user( $numeric );
				if( $user && ($mask == '*' || fnmatch($mask, $user->get_full_mask())) )
					$numerics[$numeric] = $user;
			}
			
			if( count($numerics) == 0 )
				return false;
			
			return $numerics;
		}
		
		
		function remove_channel_user( $chan_name, $num )
		{
			$chan_key = strtolower( $chan_name );

			if( array_key_exists($num, $this->users) )
			{
				$this->users[$num]->remove_channel( $chan_key );
			}
			
			if( array_key_exists($chan_key, $this->channels) )
			{
				$this->channels[$chan_key]->remove_user( $num );
				
				if( $this->channels[$chan_key]->get_user_count() == 0 )
					$this->remove_channel( $chan_key );
			}
		}


		function remove_user_from_all_channels( $num )
		{
			if( !($u = $this->get_user($num)) )
				return false;
			
			$channels = $u->channels;
			
			foreach( $channels as $chan_key )
				$this->remove_channel_user( $chan_key, $num );
			
			$u->remove_all_channels();
			
			return true;
		}
		
		
		function add_ban( $name, $mask )
		{
			$index = strtolower($name);
			$this->channels[$index]->add_ban( $mask );
		}
		
		
		function get_server( $numeric )
		{
			if( array_key_exists($numeric, $this->servers) )
				return $this->servers[$numeric];
			
			return false;
		}
		
		
		function get_server_by_name( $name )
		{
			$name = strtolower( $name );
			foreach( $this->servers as $numeric => $server )
				if( strtolower($server->get_name()) == $name )
					return $server;
			
			return false;
		}
		
		
		function get_channel( $chan_name )
		{
			$chan_key = strtolower( $chan_name );
			if( array_key_exists($chan_key, $this->channels) )
				return $this->channels[$chan_key];
			
			return false;
		}
		
		
		function get_user( $numeric )
		{
			if( array_key_exists($numeric, $this->users) )
				return $this->users[$numeric];
			
			return false;
		}
		
		
		function get_user_by_nick( $nick )
		{
			$nick = strtolower( $nick );
			foreach( $this->users as $numeric => $user )
				if( strtolower($user->get_nick()) == $nick )
					return $user;
			
			return false;
		}
		
		
		function get_account( $account_name )
		{
			$account_key = strtolower( $account_name );
			if( array_key_exists($account_key, $this->accounts) )
				return $this->accounts[$account_key];
			
			return false;
		}
		

		function get_account_by_id( $account_id )
		{
			foreach( $this->accounts as $account_key => $account )
			{
				if( $account->get_id() == $account_id )
					return $this->accounts[$account_key];
			}
			
			return false;
		}
		

		function get_account_by_email( $email )
		{
			$email = strtolower( $email );
			
			foreach( $this->accounts as $account_key => $account )
			{
				if( strtolower($account->get_email()) == $email )
					return $this->accounts[$account_key];
			}
			
			return false;
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
				11    // no data to read (resource temporarily unavailable)
			);
			
			$GLOBALS['INSTANTIATED_SERVICES'][] = $this;
			
			while( is_resource($this->sock) && in_array($err_no, $noncritical_socket_errors) )
			{
				$iter++;
				
				$timeout = 5;
				foreach( $this->timers as $n => $timer )
				{
					$secs_til_run = $timer->get_next_run() - time();
					if( $timeout > $secs_til_run && $secs_til_run >= 0 )
						$timeout = $timer->get_next_run() - time();

					// debug("Timer {$timer->include_file} has {$timeout} seconds left");
				}
				
				socket_set_option( $this->sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $timeout, "usec" => 0) );
				socket_set_option( $this->sock, SOL_SOCKET, SO_SNDTIMEO, array("sec" => $timeout, "usec" => 0) );
				
				$this->service_preread();
				$buffer .= @socket_read( $this->sock, 1024 );
				$this->bytes_received += strlen( $buffer );
				
				$break_time = time();
				foreach( $this->timers as $n => $timer )
				{
					if( $timer->get_next_run() <= $break_time )
						$this->run_timer( $n );
				}
				
				if( !empty($buffer) )
				{
					$endpos = strpos( $buffer, "\n" );
					
					while( $endpos !== false )
					{
						$line = substr( $buffer, 0, $endpos - 1 );
						$buffer = substr( $buffer, $endpos + 1 );
						
						if( !eregi("^.. [GZ] ", $line) )
							debug("[RECV] $line");
						
						$this->parse( $line );
						$this->lines_received++;
						
						$endpos = strpos( $buffer, "\n" );
					}
				}
				else
				{
					$err_no = socket_last_error( $this->sock );
					$err_str = socket_strerror( $err_no );
				}
			}
			
			debug( "SOCKET STATUS [$err_no]: $err_str" );
		}
		
		
		function parse( $line )
		{
			$num_args = line_num_args( $line );
			$args = line_get_args( $line );
			
			if( $args[0] == 'PASS' || $args[0] == 'SERVER' || $args[0] == 'NOTICE' || $args[0] == 'ERROR' )
				$token = $args[0];
			else
				$token = $args[1];
			
			$handler_file = "handle_$token.php";
			$core_handler = P10_DIR . $handler_file;
			$service_handler = SERVICE_HANDLER_DIR . $handler_file;
			
			$chan_key = '';
			$chan_name = '';
			
			for( $i = 0; $i < count($args); ++$i )
			{
				if( $args[$i][0] == '#' )
				{
					$chan_name = $args[$i];
					$chan_key = strtolower( $chan_name );
					break;
				}
			}
			
			if( $num_args >= 3 && !(($bot = $this->get_user($args[2])) && $bot->is_bot()) )
				$bot = $this->default_bot;
			
			// construct a useless for loop so we can use 'break' in handler files.
			for( $FAKE_ITERATOR = 0; $FAKE_ITERATOR == 0; ++$FAKE_ITERATOR )
			{
				if( file_exists($core_handler) )
					include( $core_handler );
				else
					debug( "*** No core handler for $token" );
				
				if( file_exists($service_handler) )
					include( $service_handler );
			}
			
			return true;
		}
		
		
		function add_timer( $repeats, $ts_interval, $include_file )
		{
			$data = array();
			
			for( $i = 3; $i < func_num_args(); ++$i )
				$data[] = func_get_arg( $i );
			
			$this->timers[] = new Timer( $repeats, $ts_interval, $include_file, $data );
		}
		
		
		function run_timer( $timer_num )
		{
			if( !array_key_exists($timer_num, $this->timers) )
				return;
			
			// debug("Running timer {$timer_num}");
			$this->execute_timer( $timer_num );
			
			$timer = $this->timers[$timer_num];
			if( $timer->is_recurring() )
			{
				$timer->update();
			}
			else
			{
				// debug("Removing timer {$timer_num}");
				unset( $this->timers[$timer_num] );
			}
		}
		
		
		function execute_timer( $timer_num )
		{
			if( !array_key_exists($timer_num, $this->timers) )
				return;
			
			$timer = $this->timers[$timer_num];
			$core_script = CORE_TIMER_DIR . $timer->get_include();
			$service_script = SERVICE_TIMER_DIR . $timer->get_include();
			
			$bot = $this->default_bot;
			$timer_data = $timer->get_data_elements();
			
			if( file_exists($core_script) )
				include( $core_script );
			if( file_exists($service_script) )
				include( $service_script );
			
			$timer->set_data_elements($timer_data);
			
			return true;
		}

		
		function perform_chan_user_mode( $chan_name, $mode_pol, $mode_char, $arg_list )
		{
			$args = array();
			$chan = $this->get_channel( $chan_name );
			
			if( !$chan )
				return;
			
			if( !is_array($arg_list) )
			{
				$arg_list = array();
				$arg_count = func_num_args();

				for( $i = 3; $i < $arg_count; ++$i )
					$arg_list[] = func_get_arg($i);
			}
			
			for( $i = 0; $i < count($arg_list); ++$i )
			{
				$args[] = $arg_list[$i];
				
				if($mode_pol == '+' && $mode_char == 'o')
					$chan->add_op( $arg_list[$i] );
				else if($mode_pol == '-' && $mode_char == 'o')
					$chan->remove_op( $arg_list[$i] );
				else if($mode_pol == '+' && $mode_char == 'v')
					$chan->add_voice( $arg_list[$i] );
				else if($mode_pol == '-' && $mode_char == 'v')
					$chan->remove_voice( $arg_list[$i] );
				else if($mode_pol == '+' && $mode_char == 'b')
					$chan->add_ban( $arg_list[$i] );
				else if($mode_pol == '-' && $mode_char == 'b')
					$chan->remove_ban( $arg_list[$i] );
				
				if( count($args) == MAX_MODES_PER_LINE || $i == (count($arg_list) - 1) )
				{
					$this->sendf( FMT_MODE_HACK_NOTS, SERVER_NUM, 
						$chan->get_name(), 
						$mode_pol . str_repeat($mode_char, count($args)),
						join(" ", $args) );
				}
			}
		}
		
		function op( $chan_name, $num_list )       { return $this->perform_chan_user_mode($chan_name, '+', 'o', $num_list); }
		function deop( $chan_name, $num_list )     { return $this->perform_chan_user_mode($chan_name, '-', 'o', $num_list); }
		function voice( $chan_name, $num_list )    { return $this->perform_chan_user_mode($chan_name, '+', 'v', $num_list); }
		function devoice( $chan_name, $num_list )  { return $this->perform_chan_user_mode($chan_name, '-', 'v', $num_list); }
		function ban( $chan_name, $num_list )      { return $this->perform_chan_user_mode($chan_name, '+', 'b', $num_list); }
		function unban( $chan_name, $num_list )    { return $this->perform_chan_user_mode($chan_name, '-', 'b', $num_list); }

		function topic( $chan_name, $topic, $chan_ts = 0 )
		{
			if( TOPIC_BURSTING && $chan_ts == 0 )
				return false;
			
			if( TOPIC_BURSTING )
				$this->sendf( FMT_TOPIC, SERVER_NUM, $chan_name, $chan_ts, time(), $topic );
			else
				$this->sendf( FMT_TOPIC, SERVER_NUM, $chan_name, $topic );
			
			$chan = $this->get_channel( $chan_name );
			$chan->set_topic( $topic );
		}
		
		
		function clear_modes( $chan_name )
		{
			$chan = $this->get_channel( $chan_name );
			$chan->clear_modes();
			$this->sendf( FMT_MODE, SERVER_NUM, $chan_name, '-psmntirlk *', $chan->get_ts() );
		}
		
		function mode( $chan_name, $modes )
		{
			$chan = $this->get_channel( $chan_name );
			$this->sendf( FMT_MODE, SERVER_NUM, $chan_name, $modes, $chan->get_ts() );
		}
		
		function kick( $chan_name, $numerics, $reason )
		{
			if( !is_array($numerics) )
				$numerics = array( $numerics );
			
			foreach( $numerics as $numeric )
			{
				$this->remove_channel_user( $chan_name, $numeric );
				$this->sendf( FMT_KICK, SERVER_NUM, $chan_name, $numeric, $reason );
			}
		}
		
		function kill( $user_num, $reason = 'So long...')
		{
			if( get_class($user_num) == 'User' )
				$user_num = $user_num->get_numeric();
			
			if( !($user = $this->get_user($user_num)) )
				return false;
			
			$this->sendf( FMT_KILL, SERVER_NUM, $user_num, SERVER_NAME, $reason );
			$this->remove_user( $user_num );
		}
	}
	
?>
