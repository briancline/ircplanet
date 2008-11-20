<?php
	
	$acct_name = $pargs[1];
	$level = $pargs[2];
	
	if( !($acct = $this->get_account($acct_name)) )
	{
		$bot->noticef( $user, 'The account %s does not exist.', $acct_name );
		return false;
	}
	
	if( !is_numeric($level) )
	{
		$bot->noticef( $user, 'The level you specified is not numeric.' );
		return false;
	}
	
	if( $level >= $user_level )
	{
		$bot->noticef( $user, 'You cannot set someone\'s level higher than or equal to your own.' );
		return false;
	}
	
	if( $level <= 0 )
	{
		$bot->noticef( $user, 'The level must be greater than zero.' );
		return false;
	}
	
	$curr_level = $this->get_user_level( $acct->get_id() );
	if( $curr_level > 0 )
	{
		$bot->noticef( $user, '%s already has level %s access.', $acct->get_name(), $curr_level );
		return false;
	}
	
	db_query( "insert into `os_admins` (user_id, level) values ('". $acct->get_id() ."', '$level')", true );
	$bot->noticef( $user, '%s now has level %d access.', $acct->get_name(), $level );

?>