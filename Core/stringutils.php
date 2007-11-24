<?php
	
	function fix_host_mask( $mask )
	{
		$ex_pos = strpos( $mask, '!' );
		$at_pos = strpos( $mask, '@' );
		$ident = substr( $mask, $ex_pos + 1, $at_pos - $ex_pos - 1 );
		
		if( strlen($ident) > IDENT_LEN )
		{
			$mask = substr($mask, 0, $ex_pos) .'!*'. right($ident, IDENT_LEN - 1) . substr($mask, $at_pos);
		}
		
		return $mask;
	}
	
	function is_ip( $s )
	{
		return eregi( '^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$', $s );
	}
	
	
	function line_num_args( $s )
	{
		$tokens = 1;
		$s = trim($s);
		$len = strlen( $s );
		
		if($len == 0)
			return 0;
		
		for( $i = 0; $i < strlen($s) - 1; ++$i )
		{
			if( $s[$i] == ' ' && $s[$i + 1] == ':' )
			{
				$tokens++;
				break;
			}
			else if( $s[$i] == ' ' )
			{
				$tokens++;
			}
		}
		
		return $tokens;
	}
	
	
	function line_get_args( $s, $stop_at_colon = true )
	{
		$start = 0;
		$tokens = array();
		$s = trim( $s );
		$len = strlen( $s );
		
		if( $len == 0 )
			return 0;
		
		for( $i = 0; $i < $len; ++$i )
		{
			if( $stop_at_colon && ($s[$i] == ' ' && $i < ($len - 1) && $s[$i + 1] == ':') )
			{
				$tokens[] = substr( $s, $start, $i - $start );
				$tokens[] = substr( $s, $i + 2 );
				break;
			}
			else if( $s[$i] == ' ' )
			{
				$tokens[] = substr( $s, $start, $i - $start );
				$start = $i + 1;
			}
			else if( $i == ($len - 1) )
			{
				$tokens[] = substr( $s, $start );
			}
		}
		
		return $tokens;
	}
	
	
	function array_copy( $array, $start = 0, $end = -1 )
	{
		$newarr = array();
		$i = -1;
		$n = 0;
		
		if( $end == -1 )
			$end = count( $array );
		
		foreach( $array as $k => $v )
		{
			$i++;
			if( $i < $start )
				continue;
			if( $i > $end )
				break;
			
			if( !is_numeric($k) )
				$newarr[$k] = $v;
			else
				$newarr[$n++] = $v;
		}
		
		return $newarr;
	}
	
	
	function assemble( $array, $start = 0, $end = -1, $delim = ' ' )
	{
		$newstr = '';
		
		if( $end == -1 )
			$end = count( $array );
		
		for( $i = $start; $i < $end; ++$i )
		{
			if( $i > $start )
				$newstr .= $delim;
			
			$newstr .= $array[$i];
		}
		
		return $newstr;
	}
	
	
	function get_pretty_size( $bytes )
	{
		$units = array( 'bytes', 'KB', 'MB', 'GB', 'TB', 'PB' );
		$precision = 2;
		
		for( $i = 0; $bytes >= 1024; ++$i )
			$bytes /= 1024;
		
		if( $i > 0 )
			$bytes = sprintf( '%0.'. $precision .'f', $bytes );
		
		return ($bytes .' '. $units[$i]);
	}
	
	
	function random_kick_reason()
	{
		$ban_reasons = array(
			"Don't let the door hit you on the way out!",
			"Sorry to see you go... actually no, not really.",
			"This is your wake-up call...",
			"Behave yourself!",
			"Ooh, behave...",
			"Not today, child.",
			"All your base are belong to me",
			"Watch yourself!",
			"Better to remain silent and be thought a fool than to speak out and remove all doubt.",
			"kthxbye."
		);
		
		$index = rand(0, count($ban_reasons) - 1);
		return $ban_reasons[$index];
	}

	
	function convert_duration( $dur )
	{
		$secs = 0;
		$amount = '';
		$found_unit = false;
		$dur = strtolower( $dur );
		$units = array(
			'y' => 31556926,
			'w' =>   604800,
			'd' =>    86400,
			'h' =>     3600,
			'm' =>       60,
			's' =>        1
		);
		
		for( $c = 0; $c < strlen($dur); ++$c )
		{
			$char = $dur[$c];
			if( is_numeric($char) )
			{
				$amount .= $char;
			}
			else if( array_key_exists($char, $units) )
			{
				if( empty($amount) )
					return false;
				
				$found_unit = true;
				$secs += ($amount * $units[$char]);
				$amount = '';
				
				/**
				 * Enforce top-down time durations by removing units
				 * (ex., 5w2d is valid, 2d5w is invalid, 2d4d is invalid)
				 */
				foreach( $units as $key => $val )
				{
					unset( $units[$key] );
					if( $key == $char )
						break;
				}
			}
			else
			{
				return false;
			}
		}
		
		if( !$found_unit )
			$secs *= 60;
		
		if( $secs < 0 )
			return false;
		
		return $secs;
	}
	
	
	function right( $str, $len )
	{
		return substr( $str, (-1 * $len) );
	}
	
	
	function is_valid_email( $email )
	{
		$b = eregi( '^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,4}$', $email );
		
		return $b;
	}
	
	
	function get_date( $ts )
	{
		return date('D j M Y H:i:s', $ts );
	}
	
	
	function db_date($ts)
	{
		return date('Y-m-d H:i:s', $ts);
	}
	

?>