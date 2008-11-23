<?php

	$new_uid = $pargs[1];

	if( $new_user = $this->get_account($new_uid) )
	{
		if( $new_user->get_id() == $user->get_account_id() )
		{
			$bot->noticef( $user, 'You cannot remove your own access!' );
			return false;
		}

		$current_level = $this->get_user_level( $new_user );

		if( $current_level <= 1 )
		{
			$bot->noticef( $user, '%s does not have administrator access.', 
				$new_user->get_name() );
			return false;
		}

		if( $current_level > $user_level )
		{
			$bot->noticef( $user, '%s has higher access than you and cannot be removed.', 
				$new_user->get_name() );
			return false;
		}

		db_queryf( "delete from ns_admins where user_id = '%d'",
			$new_user->get_id() );
		
		$bot->noticef( $user, '%s no longer has administrator access.',
			$new_user->get_name(), $new_level );
	}
	else
	{
		$bot->noticef( $user, 'Account %s does not exist.', $new_uid );
		return false;
	}
	
?>
