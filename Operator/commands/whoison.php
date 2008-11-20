<?php

	$chan_name = $pargs[1];
	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, 'Nobody is on channel %s.', $chan_name );
		return false;
	}
	
	$chan_users = array();
	$chan_nums = $chan->get_user_list();
	
	foreach( $chan_nums as $numeric )
	{
		$tmp_user = $this->get_user( $numeric );
		$tmp_nick = $tmp_user->get_nick();
		$tmp_key = strtolower( $tmp_nick );
		$flags = '';
		
		if( $chan->is_voice($numeric) )
			$flags .= '+';
		if( $chan->is_op($numeric) )
			$flags .= '@';
		
		$chan_users[$tmp_key] = $flags . $tmp_nick;
	}
	
	ksort( $chan_users );
	
	$max_line_users = 8;
	$line_users = 0;
	$user_num = 0;
	$line = '';
	
	foreach( $chan_users as $key => $nick )
	{
		$line .= $nick .' ';
		$line_users++;
		$user_num++;
		
		if( $line_users == $max_line_users || $user_num == count($chan_users) )
		{
			$bot->noticef( $user, '   %s', $line );
			$line_users = 0;
			$line = '';
		}
	}
	
?>