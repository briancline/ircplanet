<?php
	
	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}

	$text = "\001ACTION ". assemble( $pargs, 2 ) ."\001";
	$bot->message( $chan->get_name(), $text );
	
?>