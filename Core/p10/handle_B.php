<?php

	$ts = $args[3];
	$chan_key = strtolower( $args[2] );
	$modes = '';
	$key = '';
	$limit = 0;
	$has_banlist = false;
	$userlist_pos = 4;

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

	if( array_key_exists($chan_key, $this->channels) )
	{
		if( $ts < $this->channels[$chan_key]->get_ts() )
		{
			$this->channels[$chan_key]->set_name( $chan_name );
			$this->channels[$chan_key]->set_ts( $ts - 1 );
			$this->channels[$chan_key]->add_modes( $modes );
			$this->channels[$chan_key]->set_limit( $limit );
			$this->channels[$chan_key]->set_key( $key );
			$this->channels[$chan_key]->clear_user_modes();
		}
	}
	else
	{
		$this->add_channel( $chan_name, $ts, $modes, $key, $limit );
	}
	
	// ircu once sent a burst line with no users, so handle it's retardation
	// appropriately...
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
		$chan = $this->get_channel( $chan_name );
		
		foreach( $banlist as $ban )
			$chan->add_ban( $ban );
	}
	
	$user_count = count( $userlist );
	$ban_count = count( $banlist );

?>