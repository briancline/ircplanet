<?php
	
	$is_new_user = ($num_args > 4);
	
	if($is_new_user)
	{
		$gline_host = $user->get_gline_host();
		$gline_ip = $user->get_gline_ip();
		$gline_set = false;
		
		foreach( $this->glines as $gline_key => $gline )
		{
			if( !$gline->is_expired() && ($gline->matches($gline_host) || $gline->matches($gline_ip)) )
			{
				$this->enforce_gline( $gline );
				$gline_set = true;
			}
		}
		
		if(defined('TOR_GLINE') && TOR_GLINE == true && 
				!$gline_set && $this->is_tor_host($user->get_ip()))
		{
			$tor_mask = '*@'. $user->get_ip();
			$tor_secs = convert_duration(TOR_DURATION);
			$tor_gl = new Gline($tor_mask, $tor_secs, TOR_REASON);
			$this->enforce_gline($tor_gl);
		}
	}

	// Don't log new users during the initial burst, as it could flood the log channel.
	if( $this->finished_burst )
	{
		if( $is_new_user )
		{
			// new user
			$server = $this->get_server($args[0]);
			$rest = sprintf("%s (%s@%s) [%s]", $user->get_nick(), $user->get_ident(), 
				$user->get_host(), $user->get_name());
			$this->report_event('NICK', $server, $rest);
		}
		else
		{
			// nick change
			$this->report_event('NICK', $old_nick, $new_nick);
		}
	}

?>