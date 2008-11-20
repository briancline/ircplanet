<?php
	
	$acct_name = $pargs[1];
	
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
		$bot->noticef( $user, 'You cannot remove someone whose level is greater than or equal to your own.' );
		return false;
	}
	
	db_query( "delete from `os_admins` where `user_id` = '". $acct->get_id() ."'" );
	$bot->noticef( $user, '%s\'s level %d access has been revoked.', $acct->get_name(), $curr_level );

?>