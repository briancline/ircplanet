<?php

	if( !($reg = $this->get_channel_reg($chan_name)) ) {
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, 'Nobody is in %s.', $chan_name );
		return false;
	}
	
	foreach( $chan->users as $numeric => $chanuser )
	{
		if( !$chanuser->is_op() )
		{
			$chan->add_op( $numeric );
			$numerics[] = $numeric;
		}
	}
	
	$bot->op( $chan->get_name(), $numerics );
	
?>