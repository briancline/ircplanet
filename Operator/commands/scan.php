<?php
	
	$count = 0;
	$matches = array();

	$mask = strtolower( $pargs[1] );
	
	$max_user_len = 14;
	$max_server_len = 9;
	
	foreach( $this->users as $numeric => $tmp_user )
	{
		$ip = $tmp_user->get_ip();
		$ip_mask = $tmp_user->get_ident() .'@'. $ip;
		$host_mask = $tmp_user->get_ident() .'@'. $tmp_user->get_host();
		
		if( fnmatch($mask, $host_mask) || fnmatch($mask, $ip_mask) )
		{
			$user_host = $tmp_user->get_nick() .'!'. $host_mask;
			$user_len = strlen( $user_host );
			
			$server = $this->get_server( $tmp_user->get_server_numeric() );
			$server = $server->get_name_abbrev();
			$server_len = strlen( $server );
			
			if( $max_user_len < $user_len )
				$max_user_len = $user_len;
			if( $max_server_len < $server_len )
				$max_server_len = $server_len;
			
			$matches[] = array(
				'user' => $user_host,
				'ip' => $ip,
				'server' => $server
			);
		}
	}
	
	$format = "%-". $max_user_len ."s     %-15s     %-". $max_server_len ."s";
	
	$bot->noticef( $user, $format, 'USER HOST MASK', 'IP ADDRESS', 'ON SERVER' );
	$bot->noticef( $user, str_repeat('-', $max_user_len + $max_server_len + 25) );
	
	foreach( $matches as $match )
		$bot->noticef( $user, $format, $match['user'], $match['ip'], $match['server'] );
	
	$bot->noticef( $user, '%d matches found.', count($matches) );
	
?>