<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$mask = '*';
	$show_active = false;
	$show_inactive = false;
	
	if( $cmd_num_args > 1 )
		$mask = $pargs[2];
	else
		$mask = '*';
	
	$bans = $chan->get_matching_bans( $mask );
	
	if( !$bans )
	{
		if( $mask == '*' )
			$bot->noticef( $user, 'The ban list for %s is empty.', $chan->get_name() );
		else
			$bot->noticef( $user, 'There are no bans on %s matching %s.', $chan->get_name(), $mask );
		
		return false;
	}
	
	$ban_num = 0;
	foreach( $bans as $mask )
		$bot->noticef( $user, '%3d) %s%s%s', ++$ban_num, BOLD_START, $mask, BOLD_END );
	
	$bot->notice( $user, 'End of ban list.' );

?>