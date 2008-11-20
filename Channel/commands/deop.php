<?php

	$numerics = array();

	$chan = $this->get_channel( $chan_name );
	if( !$chan )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	if( $cmd_num_args == 1 )
	{
		$numerics[] = $user->get_numeric();
	}
	else
	{
		for( $i = 2; $i < count($pargs); ++$i )
		{
			$nick = $pargs[$i];
			$tmp_user = $this->get_user_by_nick($nick);
			
			if( !$tmp_user || !$chan->is_on($tmp_user->get_numeric()) )
			{
				$bot->noticef( $user, "The user %s%s%s was not found on channel %s.",
					BOLD_START, $nick, BOLD_END, $chan->get_name() );
				continue;
			}
			
			if( $tmp_user->is_bot() )
			{
				$bot->noticef( $user, "The %s%s%s service cannot be deopped.",
					BOLD_START, $tmp_user->get_nick(), BOLD_END );
				continue;
			}
			
			$numerics[] = $tmp_user->get_numeric();
			$chan->remove_op( $tmp_user->get_numeric() );
		}
	}
	
	$bot->deop( $chan->get_name(), $numerics );

?>
