<?php
	
	// Don't log new users during the initial burst, as it could flood the log channel.
	if( $this->finished_burst )
	{
		if( $num_args > 4 )
		{
			// new user
			$server = $this->get_server($args[0]);
			$rest = sprintf("%s (%s@%s) [%s]", $user->get_nick(), $user->get_ident(), $user->get_host(), $user->get_name());
			$this->report_event('NICK', $server, $rest);
			
			$gline_host = $user->get_gline_host();
			$gline_ip = $user->get_gline_ip();
			
			foreach( $this->glines as $gline_key => $gline )
			{
				if( !$gline->is_expired() && ($gline->matches($gline_host) || $gline->matches($gline_ip)) )
					$this->enforce_gline( $gline );
			}
		}
		else
		{
			// nick change
			$this->report_event('NICK', $old_nick, $new_nick);
		}
	}

?>