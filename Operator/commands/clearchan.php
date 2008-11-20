<?php

	$chan_name = $pargs[1];
	$flags = strtolower( $pargs[2] );
	
	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, 'Nobody is on channel %s.', $chan_name );
		return false;
	}
	
	$clear_modes = $kick_users = $deop_users = false;
	$devoice_users = $clear_bans = $gline_users = false;
	
	for( $c = 0; $c < strlen($flags); $c++ )
	{
		switch( $flags[$c] )
		{
			case 'm':
				$clear_modes = true;
				break;
			case 'k':
				$kick_users = true;
				break;
			case 'o':
				$deop_users = true;
				break;
			case 'v':
				$devoice_users = true;
				break;
			case 'b':
				$clear_bans = true;
				break;
			case 'g':
				$gline_users = true;
				break;
		}
	}
	
	if( $deop_users )
		$this->deop( $chan->get_name(), $chan->get_op_list() );
	
	if( $devoice_users )
		$this->devoice( $chan->get_name(), $chan->get_voice_list() );
	
	if( $clear_bans )
		$this->unban( $chan->get_name(), $chan->get_matching_bans() );
	
	if( $clear_modes )
		$this->clear_modes( $chan->get_name() );
	
	if( $gline_users )
	{
		$users = $chan->get_user_list();
		$gline_duration = '1h';
		
		if( $cmd_num_args >= 3 && convert_duration($pargs[3]) !== false )
			$gline_duration = $pargs[3];
		
		$gline_duration = convert_duration( $gline_duration );
		
		foreach( $users as $numeric )
		{
			$tmp_user = $this->get_user( $numeric );
			if( $tmp_user != $user && !$tmp_user->is_bot() )
			{
				$gline = $this->add_gline( $tmp_user->get_gline_mask(), $gline_duration, 
					"Channel g-line for ". $chan->get_name() );
				$this->enforce_gline( $gline );
			}
		}
	}
	
	if( $kick_users )
	{
		$users = $chan->get_user_list();
		
		foreach( $users as $numeric )
		{
			$tmp_user = $this->get_user( $numeric );
			if( $tmp_user != $user && !$tmp_user->is_bot() )
				$this->kick( $chan->get_name(), $numeric,
					"Clearing channel ". $chan->get_name() );
		}
	}
	
?>