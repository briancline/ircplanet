<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$mask = $pargs[2];
	$duration = '60m';                // default duration
	$reason = '';                     // default reason
	$level = $user_channel_level;
	$kick_reason = 'Banned';
	
	if( $level == 0 )
		$level = 75;
	if( $cmd_num_args >= 3 )
	{
		$reason = assemble( $pargs, 3 );
		$kick_reason = $reason;
	}
	
	if( $level > $user_level )
	{
		$bot->noticef( $user, 'The level you specified is too high and must be %s or lower.',
			$user_level );
		return false;
	}
	
	if( !($duration_secs = convert_duration($duration)) )
	{
		$bot->notice( $user, 'Invalid duration specified! See help for more details.' );
		return false;
	}
	
	if( !eregi('[!@\.]', $mask) )
	{
		if( ($tmp_user = $this->get_user_by_nick($mask)) )
			$mask = $tmp_user->get_host_mask();
		else
			$mask = $mask . '!*@*';
	}
	
	$mask = fix_host_mask( $mask );
	
	if( ($ban = $chan_reg->get_ban($mask)) )
	{
		$bot->noticef( $user, 'A ban for %s already exists.', $ban->get_mask() );
		return false;
	}
	
	if( ($ban = $chan_reg->count_matching_bans($mask)) )
	{
		$bot->noticef( $user, 'An existing ban for %s supercedes the one you are trying to set.',
			$ban->get_mask() );
		return false;
	}
	
	$ban = new DB_Ban( $chan_reg->get_id(), $user->get_account_id(), $mask, $duration_secs, $level, $reason );
	$chan_reg->add_ban( $ban );
	$chan_reg->save();
	
	$bot->ban( $chan->get_name(), $mask );
	$chan->add_ban( $mask );

	$kick_users = $this->get_channel_users_by_mask( $chan->get_name(), $mask );
	foreach( $kick_users as $numeric => $chan_user )
	{
		if( !$chan_user->is_bot() && $chan_user != $user )
			$bot->kick( $chan->get_name(), $numeric, $kick_reason );
	}
	
	
?>