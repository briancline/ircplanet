<?php

	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, 'Nobody is in %s.', $chan_name );
		return false;
	}
	
	foreach( $chan->users as $numeric => $chanuser )
	{
		if( $chanuser->is_voice() )
		{
			$chan->add_voice( $numeric );
			$numerics[] = $numeric;
		}
	}
	
	$this->devoice( $chan->get_name(), $numerics );
	
?>