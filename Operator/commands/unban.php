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
	if( !$chan->has_ban($mask) )
	{
		$bot->noticef( $user, '%s is not banned on %s.', $mask, $chan_name );
		return false;
	}
	
	$this->unban( $chan->get_name(), $mask );
	
?>