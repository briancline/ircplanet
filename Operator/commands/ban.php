<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$mask = $pargs[2];
	
	if( !eregi('[!@\.]', $mask) )
	{
		if( ($tmp_user = $this->get_user_by_nick($mask)) )
			$mask = $tmp_user->get_host_mask();
		else
			$mask = $mask . '!*@*';
	}
	
	$mask = fix_host_mask( $mask );
	if( $chan->has_ban($mask) )
	{
		$bot->noticef( $user, 'A ban for %s already exists.', $mask );
		return false;
	}
	
	if( ($bans = $chan->get_matching_bans($mask)) )
	{
		$bot->noticef( $user, 'An existing ban for %s supercedes the one you are trying to set.',
			$bans[0] );
		return false;
	}
	
	$this->ban( $chan->get_name(), $mask );
	return true;
	
?>