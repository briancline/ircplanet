<?php

	if( !($reg = $this->get_channel_reg($chan_name)) ) {
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	
	$rem_uid = $pargs[2];
	
	if( $rem_user = $this->get_account($rem_uid) )
	{
		if( $this->get_channel_level_by_name($chan_name, $rem_uid) == 0 )
		{
			$bot->noticef( $user, '%s is not in the %s access list.',
				$rem_user->get_name(), $reg->get_name() );
			return false;
		}
		
		$reg->remove_access( $rem_user->get_id() );
		$reg->save();
		
		$bot->noticef( $user, '%s has been removed from the %s access list.',
			$rem_user->get_name(), $reg->get_name() );
		
		foreach( $this->users as $numeric => $tmp_user )
		{
			if( $tmp_user->is_logged_in() && $tmp_user->get_account_id() == $rem_user->get_id() && 
				$tmp_user->get_nick() != $user->get_nick() )
			{
				$bot->noticef( $tmp_user, '%s has removed your access on %s.',
					$user->get_nick(), $reg->get_name() );
			}
		}
	}
	else
	{
		$bot->noticef( $user, 'Account %s does not exist.', $rem_uid );
		return false;
	}
	
?>