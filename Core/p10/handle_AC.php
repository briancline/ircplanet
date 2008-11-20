<?php
	
	include( CORE_TIMER_DIR .'refresh_data.php' );

	$user = $this->get_user( $args[2] );
	$user->set_account_name( $args[3] );

	if( $account = $this->get_account($args[3]) )
	{
		$user->set_account_id( $account->get_id() );
		$account->update_lastseen();
		$account->save();
	}

?>
