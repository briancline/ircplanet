<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$reason = assemble( $pargs, 2 );
	$users = $this->get_channel_users_by_mask( $chan_name );
	
	$deops = $masks = $kicks = array();
	
	foreach( $users as $numeric => $chan_user )
	{
		if( !$chan_user->is_bot() && $chan_user != $user )
		{
			if( $chan->is_op($numeric) )
				$deops[] = $numeric;
			
			$masks[] = $chan_user->get_host_mask();
			$kicks[] = $numeric;
		}
	}
	
	$this->deop( $chan->get_name(), $deops );
	$this->ban( $chan->get_name(), $masks );
	
	foreach( $kicks as $kick_numeric )
		$this->kick( $chan->get_name(), $kick_numeric, $reason );

?>