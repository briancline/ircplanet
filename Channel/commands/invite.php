<?php

	$nicks = array();

	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
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
	
	foreach( $nicks as $nick )
		$bot->invite( $nick, $chan->get_name() );
	
?>