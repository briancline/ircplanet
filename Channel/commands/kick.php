<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$nick = $pargs[2];
	$reason = random_kick_reason();
	
	if( $cmd_num_args > 2 )
		$reason = assemble( $pargs, 3 );
	
	$tmp_user = $this->get_user_by_nick($nick);
	if( !$tmp_user || !$chan->is_on($tmp_user->get_numeric()) )
	{
		$bot->noticef( $user, "The user %s%s%s was not found on channel %s.",
			BOLD_START, $nick, BOLD_END, $chan->get_name() );
		return false;
	}
	
	if( $tmp_user->is_service() )
	{
		$bot->notice( $user, 'You cannot kick a service bot.' );
		return false;
	}
	
	$numeric = $tmp_user->get_numeric();
	$bot->kick( $chan->get_name(), $numeric, $reason );
	
?>