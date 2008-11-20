<?php
/*	
	if( $user->get_nick() == 'brian' )
	{
		$botnum = $bot->get_numeric();
		$this->sendf( FMT_JOIN, $botnum, "#southpole", time() );
		$this->add_channel_user( "#southpole", $botnum );
		$this->op( "#southpole", $botnum );
	}
*/

	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$text = assemble( $pargs, 2 );
	$bot->message( $chan->get_name(), $text );
	
?>
