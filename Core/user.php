<?php

	// User mode flags
	$USER_MODES = array(
		'd' => array( 'const' => 'UMODE_DEAF',         'uint' => 0x0001 ),
		'i' => array( 'const' => 'UMODE_INVISIBLE',    'uint' => 0x0002 ),
		'o' => array( 'const' => 'UMODE_OPER',         'uint' => 0x0004 ),
		's' => array( 'const' => 'UMODE_SERVERMSG',    'uint' => 0x0008 ),
		'w' => array( 'const' => 'UMODE_WALLOPS',      'uint' => 0x0010 ),
		'k' => array( 'const' => 'UMODE_SERVICE',      'uint' => 0x0020 ),
		'g' => array( 'const' => 'UMODE_HACKMSG',      'uint' => 0x0040 ),
		'x' => array( 'const' => 'UMODE_HIDDENHOST',   'uint' => 0x0080 )
	);


	class User
	{
		var $numeric;
		var $nick;
		var $account_id = 0;
		var $account_name;
		var $ident;
		var $host;
		var $ip;
		var $start_ts;
		var $desc;
		var $modes = 0;
		var $away_msg;
		var $channels = array();
		
		function __construct( $num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes = "", $account = "" )
		{
			$this->numeric = $num;
			$this->nick = $nick;
			$this->account_name = $account;
			$this->ident = $ident;
			$this->host = $host;
			$this->ip = $ip;
			$this->start_ts = $start_ts;
			$this->desc = $desc;
			$this->add_modes( $modes );
		}
		
		function is_bot()              { return false; }
		function is_oper()             { return $this->has_mode(UMODE_OPER); }
		function is_away()             { return $this->away_msg != ''; }
		function is_logged_in()        { return $this->account_id > 0; }
		function has_account_name()    { return strlen($this->account_name) > 0; }
		
		function get_nick()            { return $this->nick; }
		function get_ident()           { return $this->ident; }
		function get_host()            { return $this->host; }
		function get_name()            { return $this->desc; }
		function get_away()            { return $this->away_msg; }
		function get_numeric()         { return $this->numeric; }
		function get_server_numeric()  { return substr($this->numeric, 0, BASE64_SERVLEN); }
		function get_account_name()    { return $this->account_name; }
		function get_account_id()      { return $this->account_id; }
		
		function set_nick($s)          { $this->nick = $s; }
		function set_account_id($i)    { $this->account_id = $i; }
		function set_account_name($s)  { $this->account_name = $s; }
		function set_away($s = "")     { $this->away_msg = $s; }
		
		
		static function is_valid_mode( $mode )
		{
			global $USER_MODES;
			return in_array( $mode, $USER_MODES );
		}
		
		static function is_valid_mode_int( $mode )
		{
			global $USER_MODES;
			foreach( $USER_MODES as $c => $i )
				if( $i['uint'] == $mode )
					return true;
			
			return false;
		}

		function add_modes( $str )
		{
			global $USER_MODES;
			foreach( $USER_MODES as $c => $i )
				if( strpos($str, $c) !== false ) $this->add_mode( $i['uint'] );
		}
		
		function add_mode( $mode )
		{
			global $USER_MODES;
			if( !is_int($mode) )
				return $this->add_mode( $USER_MODES[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && !$this->has_mode($mode) )
				$this->modes |= $mode;
		}
		
		function remove_mode( $mode )
		{
			global $USER_MODES;
			if( !is_int($mode) )
				return $this->remove_mode( $USER_MODES[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && $this->has_mode($mode) )
				$this->modes &= ~$mode;
		}
		
		function has_mode( $mode )
		{
			global $USER_MODES;
			if( !is_int($mode) )
				return $this->has_mode( $USER_MODES[$mode]['uint'] );
			
			return( ($this->modes & $mode) == $mode );
		}
		
		function get_modes()
		{
			global $USER_MODES;

			$modes = '';
			foreach( $USER_MODES as $c => $i )
				if( $this->has_mode($c) ) $modes .= $c;
			
			return $modes;
		}
		
		function get_full_mask()     { return $this->nick .'!'. $this->ident .'@'. $this->host; }
		function get_full_ip_mask()  { return $this->nick .'!'. $this->ident .'@'. $this->ip; }
		function get_gline_host()    { return $this->ident .'@'. $this->host; }
		function get_gline_ip()      { return $this->ident .'@'. $this->ip; }
		function get_gline_mask()    { return substr( $this->get_host_mask(), 2 ); }
		
		function get_host_mask()
		{
			$mask = '*!*'. right( $this->ident, IDENT_LEN ) .'@';
			$host = $this->host;

			$levels = explode( '.', $host );
			$num_levels = count( $levels );
			
			if( is_ip($host) )
			{
				$host = assemble( $levels, 0, 3, '.' );
				$host .= '.*';
			}
			else if( $num_levels > 2 )
			{
				for( $n = $num_levels - 1; $n > 0; $n-- )
				{
					if( eregi('[0-9]', $levels[$n]) )
						break;
				}
				
				$host = '*.';
				$host .= assemble( $levels, $n + 1, -1, '.' );
			}
			
			$mask = fix_host_mask( $mask );
			
			return $mask . $host;
		}
		
		function add_channel( $name )
		{
			$this->channels[] = $name;
		}
		
		function remove_channel( $name )
		{
			$channels = $this->channels;
			for( $i = 0; $i < count($channels); ++$i )
			{
				if( $channels[$i] == $name )
				{
					unset( $channels[$i] );
					break;
				}
			}
			
			$this->channels = array_copy( $channels );
		}

		function remove_all_channels()
		{
			$this->channels = array();
		}
	}


	foreach( $USER_MODES as $c => $i )
	{
		if( !defined($i['const']) )
			define( $i['const'], $i['uint'] );
	}
	

?>
