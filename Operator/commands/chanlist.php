<?php
	
	$mask = '*';
	$count = 0;
	$matches = array();

	if( $cmd_num_args >= 1 )
		$mask = strtolower( $pargs[1] );
	
	$max_name_len = 12;
	$max_mode_len = 5;
	$max_count_len = 5;
	
	foreach( $this->channels as $chan_key => $chan )
	{
		if( fnmatch($mask, $chan_key) )
		{
			$name = $chan->get_name();
			$name_len = strlen( $name );
			$mode = '+'. $chan->get_modes();
			$mode_len = strlen( $mode );
			$count = $chan->get_user_count();
			$count_len = strlen( $count );
			
			if( $max_name_len < $name_len )
				$max_name_len = $name_len;
			if( $max_mode_len < $mode_len )
				$max_mode_len = $mode_len;
			if( $max_count_len < $count_len )
				$max_count_len = $count_len;
			
			$matches[] = array(
				'name' => $name,
				'mode' => $mode,
				'count' => $count
			);
		}
	}
	
	$h_format = "%-". $max_name_len ."s     %-". $max_mode_len ."s     %". $max_count_len ."s";
	$format = "%-". $max_name_len ."s     %-". $max_mode_len ."s     %". $max_count_len ."d";
	
	$bot->noticef( $user, $h_format, 'CHANNEL NAME', 'MODES', 'USERS' );
	$bot->noticef( $user, str_repeat('-', $max_name_len + $max_mode_len + $max_count_len + 10) );
	
	foreach( $matches as $match )
		$bot->noticef( $user, $format, $match['name'], $match['mode'], $match['count'] );
	
	$bot->noticef( $user, '%d matches found.', count($matches) );
	
?>