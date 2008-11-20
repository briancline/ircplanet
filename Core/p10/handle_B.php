<?php

	$ts = $args[3];
	$chan_key = strtolower( $args[2] );
	$modes = '';
	$key = '';
	$limit = 0;
	$has_banlist = false;
	$userlist_pos = 4;
	$cleared_local_modes = false;

	/**
	 * AE B #log.oracle 1131900616
	 * AE B #testchan 1131291938 AEBFo:o
	 * AE B #support 1105674755 +tnl 14 AEBFh,M[AAC:ov
	 * AE B #opers 1100986985 +smtin M[AAD:o
	 * AE B #coder-com 1113336997 +tn AEBFh:o,M[AAC :%*!*user@*.fucker.com
	 * AE B #testchan 1131291938 +stinlk 69 w00t3rz AEBFo:o
	 */
	
	$has_modes = ($args[4][0] == '+');
	if( $has_modes )
	{
		$userlist_pos++;
		$modes_pos = 4;
		
		if( eregi('l', $args[$modes_pos]) )
		{
			$userlist_pos++;
			$limit = $args[$userlist_pos - 1];
		}
		if( eregi('k', $args[$modes_pos]) )
		{
			$userlist_pos++;
			$key = $args[$userlist_pos - 1];
		}
		
		$modes = $args[$modes_pos];
	}

	if( ($chan = $this->get_channel($chan_key)) )
	{
		if( $ts < $chan->get_ts() )
		{
			debugf("%s ts is %d secs older than mine", $chan_key, ($chan->get_ts() - $ts));
			$chan->clear_bans();
			$chan->clear_modes();
			$chan->clear_user_modes();
			
			$chan->set_name( $chan_name );
			$chan->set_ts( $ts );
			$chan->add_modes( $modes );
			$chan->set_limit( $limit );
			$chan->set_key( $key );
			
			$cleared_local_modes = true;
		}
	}
	else
	{
		$chan = $this->add_channel( $chan_name, $ts, $modes, $key, $limit );
	}
	
	/**
	 * ircu once sent me a burst line with no users during services testing, 
	 * so handle it's retardation appropriately here...
	 */
	$userlist = array();
	$has_userlist = $userlist_pos < $num_args;
	if( $has_userlist )
	{
		$userlist = explode( ',', $args[$userlist_pos] );
		
		foreach( $userlist as $user )
		{
			$user_modes = '';
			$numeric = substr( $user, 0, 5 );
			if( strlen($user) > 5 )
				$user_modes = substr( $user, 6 );
			
			$this->add_channel_user( $chan_name, $numeric, $user_modes );
		}
	}
	
	$banlist = array();
	$banlist_pos = $userlist_pos + 1;
	$has_banlist = $banlist_pos < $num_args;
	if( $has_banlist )
	{
		// skip the % character
		$ban_string = substr( $args[$banlist_pos], 1 );
		$banlist = explode( ' ', $ban_string );
		
		foreach( $banlist as $ban )
			$chan->add_ban( $ban );
	}
	
	$user_count = count( $userlist );
	$ban_count = count( $banlist );

?>
