<?php

	$nicks = array();
	
	$chan_name = BOT_CHAN;
	
	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	if( $chan->is_on( $user->get_numeric()) )
	{
		$bot->noticef( $user, "You're already on %s...", $chan->get_name() );
		return false;
	}
	
	$bot->invite( $user->get_nick(), $chan->get_name() );
	
?>