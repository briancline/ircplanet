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
	
	$ban = $chan_reg->get_ban( $mask );
	$active = $chan->has_ban( $mask );

	if( !$ban && !$active )
	{
		$bot->noticef( $user, 'There is no ban for %s on %s.', $mask, $chan_reg->get_name() );
		return false;
	}
	
	if( $ban )
	{
		if( $ban->get_level() > $user_level )
		{
			$bot->noticef( $user, 'You cannot remove a ban with a level higher than your own.' );
			return false;
		}
		
		$mask = $ban->get_mask();
		$bot->unban( $chan->get_name(), $mask );
		$chan_reg->remove_ban( $mask );
		$chan_reg->save();
	}
	
	if( $active )
	{
		$bot->unban( $chan->get_name(), $mask );
	}
	else
	{
		$bot->noticef( $user, 'The ban for %s has been removed.', $mask );
	}
	
?>