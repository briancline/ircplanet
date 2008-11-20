<?php

	if( !($reg = $this->get_channel_reg($chan_name)) ) {
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, 'Nobody is in %s.', $chan_name );
		return false;
	}
	
//	$bot->mode( $chan_name, '+m' );
	$this->sendf( FMT_MODE_NOTS, $bot->get_numeric(), $chan->get_name(), '+m' );
	$chan->add_mode( 'm' );
	
	foreach( $chan->users as $numeric => $chanuser )
	{
		if( !$chanuser->is_voice() && !$chanuser->is_op() )
		{
			$chan->add_voice( $numeric );
			$numerics[] = $numeric;
		}
	}
	
	$bot->voice( $chan->get_name(), $numerics );
	
?>