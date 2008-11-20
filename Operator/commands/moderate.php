<?php

	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, 'Nobody is in %s.', $chan_name );
		return false;
	}
	
	$this->mode( $chan->get_name(), '+m' );
	$chan->add_mode( 'm' );
	
	foreach( $chan->users as $numeric => $chanuser )
	{
		if( !$chanuser->is_voice() && !$chanuser->is_op() )
		{
			$chan->add_voice( $numeric );
			$numerics[] = $numeric;
		}
	}
	
	$this->voice( $chan->get_name(), $numerics );
	
?>