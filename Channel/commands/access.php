<?php

	if( !($reg = $this->get_channel_reg($chan_name)) ) {
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	
	$n = 0;
	$users = array();
	$user_mask = $pargs[2];
	
	foreach( $reg->get_levels() as $user_id => $access )
	{
		$tmpuser = $this->get_account_by_id( $user_id );
		
		if(!$tmpuser)
			continue;
		
		$tmpname = $tmpuser->get_name();
		
		if( $tmpname == $user_mask || fnmatch($user_mask, $tmpname) )
		{
			$users[$user_id] = $access;
			$n++;
		}
	}
	
	if( count($users) > 0 )
	{
		$user_num = 0;
		for( $i = 500; $i > 0; $i-- )
		{
			foreach( $users as $user_id => $access )
			{
				$level = $access->get_level();
				if( $level == $i )
				{
					$tmpuser = $this->get_account_by_id( $user_id );
					$last_ts = $tmpuser->get_lastseen_ts();

					$bot->noticef( $user, '%3d) User:  %s%-20s%s     Level: %s%3d%s', 
						++$user_num,
						BOLD_START, $tmpuser->get_name(), BOLD_END,
						BOLD_START, $level, BOLD_END );
					$bot->noticef( $user, '     Auto-op: %-3s   Auto-voice: %-3s   Protect: %-3s', 
						$access->auto_ops() ? 'ON' : 'OFF',
						$access->auto_voices() ? 'ON' : 'OFF',
						$access->is_protected() ? 'ON' : 'OFF' );
					$bot->noticef( $user, '     Last login: %s',
						date('D j M Y H:i:s', $last_ts) );
					$bot->notice( $user, ' ' );
				}
			}
		}
	}
		
	$bot->noticef( $user, 'Found %d records matching your search.', $n );
	
?>
