<?php
	
	$acct_name = $pargs[1];
	$setting = strtoupper( $pargs[2] );
	$param = $pargs[3];
	
	if( !($acct = $this->get_account($acct_name)) )
	{
		$bot->noticef( $user, 'The account %s does not exist.', $acct_name );
		return false;
	}
	
	$curr_level = $this->get_user_level( $acct->get_id() );
	
	if( $curr_level == 0 )
	{
		$bot->noticef( $user, '%s does not have any existing access.', $acct->get_name() );
		return false;
	}
	
	if( $curr_level >= $user_level )
	{
		$bot->noticef( $user, 'You cannot modify someone whose level is greater than or equal to your own.' );
		return false;
	}
	
	
	if( $setting == 'LEVEL' )
	{
		if( !is_numeric($param) )
		{
			$bot->noticef( $user, 'The new level you specified is not numeric.' );
			return false;
		}
		
		if( $param <= 0 )
		{
			$bot->noticef( $user, 'The new level must be greater than zero.' );
			return false;
		}
		
		if( $param >= $user_level )
		{
			$bot->noticef( $user, 'You cannot set %s\'s level higher than or equal to your own.',
				$acct->get_name() );
			return false;
		}
		
		db_query( "update `os_admins` set `level` = '$param' where `user_id` = '". $acct->get_id() ."'" );
		$bot->noticef( $user, '%s\'s level has been changed from %d to %d.', $acct->get_name(), 
			$curr_level, $param );
	
	}
	else
	{
		$bot->noticef( $user, 'Invalid setting. Please refer to help for a list of settings.' );
		return false;
	}
	
?>