<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$mask = $pargs[2];
	$reason = '';                     // default reason
	$kick_reason = 'Banned';
	
	if( $cmd_num_args >= 3 )
	{
		$reason = assemble( $pargs, 3 );
		$kick_reason = $reason;
	}
	
	if( !eregi('[!@\.]', $mask) )
	{
		if( ($tmp_user = $this->get_user_by_nick($mask)) )
			$mask = $tmp_user->get_host_mask();
		else
			$mask = $mask . '!*@*';
	}
	
	$mask = fix_host_mask( $mask );
	
	if( ($ban = $chan->has_ban($mask)) )
	{
		$bot->noticef( $user, 'A ban for %s already exists.', $mask );
		return false;
	}
	
	if( ($bans = $chan->get_matching_bans($mask)) )
	{
		$bot->noticef( $user, 'An existing ban for %s supersedes the one you are trying to set.',
			$bans[0] );
		return false;
	}
	
	$this->ban( $chan->get_name(), $mask );

	$kick_users = $this->get_channel_users_by_mask( $chan->get_name(), $mask );
	foreach( $kick_users as $numeric => $chan_user )
	{
		if( !$chan_user->is_bot() && $chan_user != $user )
			$this->kick( $chan->get_name(), $numeric, $kick_reason );
	}
	
	
?>