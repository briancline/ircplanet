<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$reason = assemble( $pargs, 2 );
	$users = $this->get_channel_users_by_mask( $chan_name );
	
	foreach( $users as $numeric => $chan_user )
	{
		if( !$chan_user->is_bot() && $chan_user != $user )
			$bot->kick( $chan->get_name(), $numeric, $reason );
	}
	
?>