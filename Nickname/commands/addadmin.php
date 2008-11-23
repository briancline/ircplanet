<?php

	$new_uid = $pargs[1];
	$new_level = $pargs[2];
	
	if( $new_user = $this->get_account($new_uid) )
	{
		$current_level = $this->get_user_level( $new_user );

		if( $current_level > 1 )
		{
			$bot->noticef( $user, '%s already has level %d administrator access!', 
				$new_user->get_name(), $current_level );
			return false;
		}

		if( $new_level < 501 || $new_level > $user_level )
		{
			$bot->noticef( $user, 'Level must range from 1 to %d.', $user_level );
			return false;
		}

		db_queryf( "insert into ns_admins (user_id, level) values ('%d', '%d')",
			$new_user->get_id(), $new_level );
		
		$bot->noticef( $user, '%s has been given administrator access at level %d.',
			$new_user->get_name(), $new_level );
	}
	else
	{
		$bot->noticef( $user, 'Account %s does not exist.', $new_uid );
		return false;
	}
	
?>
