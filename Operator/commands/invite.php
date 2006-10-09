<?php

	$nicks = array();

	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	if( strtolower($chan_name) == strtolower(BOT_CHAN) )
	{
		$bot->noticef( $user, "You must use the %sinviteme%s command to join %s.",
			BOLD_START, BOLD_END, $chan->get_name() );
		return false;
	}
	
	if( $cmd_num_args == 1 )
	{
		if( $chan->is_on( $user->get_numeric()) )
		{
			$bot->noticef( $user, "You're already on %s...", $chan->get_name() );
			return false;
		}
		
		$nicks[] = $user->get_nick();
	}
	else
	{
		for( $i = 2; $i < count($pargs); ++$i )
		{
			$nick = $pargs[$i];
			$tmp_user = $this->get_user_by_nick($nick);
			
			if( !$tmp_user )
			{
				$bot->noticef( $user, "The user %s%s%s does not exist.",
					BOLD_START, $nick, BOLD_END );
				continue;
			}
			
			if( $chan->is_on($tmp_user->get_numeric()) )
			{
				$bot->noticef( $user, "%s is already on %s.",
					$tmp_user->get_nick(), $chan->get_name() );
				continue;
			}
			
			$nicks[] = $tmp_user->get_nick();
		}
	}
	
	if(count($nicks) > 0)
	{
		//$bot->join( $chan->get_name() );
		//$this->op( $chan->get_name(), $bot->get_numeric() );
		foreach( $nicks as $nick )
			$bot->invite( $nick, $chan->get_name() );
		//$bot->part( $chan->get_name() );
	}
	
?>