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
		{
			$mask = $chan_user->get_host_mask();

			$ban = new DB_Ban( $chan_reg->get_id(), $user->get_account_id(), $mask );
			$ban->set_reason( $reason );
			$chan_reg->add_ban( $ban );
			
			$bot->mode( $chan->get_name(), "-o+b $numeric $mask" );
			$bot->kick( $chan->get_name(), $numeric, $reason );
			$chan->add_ban( $mask );
		}
	}

	$chan_reg->save();

?>