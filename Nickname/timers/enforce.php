<?php

	$numeric = $timer_data[0];
	$account_name = $timer_data[1];
	
	$user = $this->get_user( $numeric );
	$account = $this->get_account( $account_name );
	
	if( !$user || !$account )
		return false;
	
	$nick_c = strtolower( $user->get_nick() );
	$account_c = strtolower( $account->get_name() );
	
	if( $nick_c == $account_c && $user->get_account_id() != $account->get_id() )
	{
		$this->kill( $user, 'Enforcing registered nick' );
		return true;
	}
	
	return false;
	
?>