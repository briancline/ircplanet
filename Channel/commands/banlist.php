<?php

	$mask = '*';
	$show_active = false;
	$show_inactive = false;
	
	if( $cmd_num_args > 1 )
		$mask = $pargs[2];

	if( $mask == 'active' )
	{
		$mask = '*';
		$show_active = true;
	}
	if( $mask == 'inactive' )
	{
		$mask = '*';
		$show_inactive = true;
	}
	
	$chan = $this->get_channel($chan_name);
	$bans = $chan_reg->get_matching_bans( $mask );
	
	if( !$bans )
	{
		if( $mask == '*' )
			$bot->noticef( $user, 'The ban list for %s is empty.', $chan_reg->get_name() );
		else
			$bot->noticef( $user, 'There are no bans on %s matching %s.', $chan_reg->get_name(), $mask );
		
		return false;
	}
	
	foreach( $bans as $mask => $ban )
	{
		$active = $chan && $chan->has_ban( $mask );
		$status = $active ? 'active' : 'inactive';
		$user_acct = $this->get_account_by_id( $ban->get_user_id() );
		$reason = $ban->get_reason();
		
		if( ($show_active && !$active) || ($show_inactive && $active) )
			continue;
		
		$bot->noticef( $user, '%3d) Mask: %s%s%s (currently %s)', 
			++$ban_num, BOLD_START, $ban->get_mask(), BOLD_END, $status );
		$bot->noticef( $user, '     Set by: %s%s%s   Level: %s%s%s   Set on: %s', 
			BOLD_START, $user_acct->get_name(), BOLD_END,
			BOLD_START, $ban->get_level(), BOLD_END,
			date('D j M Y H:i:s', $ban->get_set_ts()) );
		$bot->noticef( $user, '     Expires: %s',
			date('D j M Y H:i:s', $ban->get_expire_ts()) );
		if( !empty($reason) )
			$bot->noticef( $user, '     Reason: %s',  $reason );
		$bot->notice( $user, ' ' );
	}
	
	$bot->notice( $user, 'End of ban list.' );
?>