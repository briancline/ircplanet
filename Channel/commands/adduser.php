<?php

	if( !($reg = $this->get_channel_reg($chan_name)) ) {
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	
	$new_uid = $pargs[2];
	
	if( $new_user = $this->get_account($new_uid) )
	{
		$level = 100;
		if( $cmd_num_args > 2 )
			$level = $pargs[3];
		
		if( $level < 1 || $level > 500 )
		{
			$bot->notice( $user, 'Level must range from 1 to 500.' );
			return false;
		}
		
		if( $level >= $user_channel_level )
		{
			$bot->noticef( $user, 'You cannot add someone with access equal to or higher than your own (%s).',
				$user_channel_level );
			return false;
		}

		$existing_level = $this->get_channel_level_by_name( $chan_name, $new_uid );
		if( $existing_level > 0 )
		{
			$bot->noticef( $user, '%s already has level %d access on %s.',
				$new_user->get_name(), $existing_level, $reg->get_name() );
			return false;
		}
		
		$new_access = new DB_Channel_Access();
		$new_access->set_chan_id( $reg->get_id() );
		$new_access->set_user_id( $new_user->get_id() );
		$new_access->set_level( $level );
		$new_access->save();
		$reg->add_access( $new_access );
		
		$bot->noticef( $user, '%s has been added to the %s access list at level %d.',
			$new_user->get_name(), $reg->get_name(), $level );
		
		foreach( $this->users as $numeric => $tmp_user )
		{
			if( $tmp_user->is_logged_in() && $tmp_user->get_account_id() == $new_user->get_id() && 
				$tmp_user->get_nick() != $user->get_nick() )
			{
				$bot->noticef( $tmp_user, '%s has given you level %d access on %s.',
					$user->get_nick(), $level, $reg->get_name() );
			}
		}
	}
	else
	{
		$bot->noticef( $user, 'Account %s does not exist.', $new_uid );
		return false;
	}
	
?>
