<?php

	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, 'Nobody is in %s.', $chan_name );
		return false;
	}
	
	foreach( $chan->users as $numeric => $chanuser )
	{
		$tmpuser = $this->get_user( $numeric );
		
		if( $chanuser->is_op() && !$tmpuser->is_bot() )
		{
			$chan->remove_op( $numeric );
			$numerics[] = $numeric;
		}
	}
	
	$this->deop( $chan->get_name(), $numerics );
	
?>