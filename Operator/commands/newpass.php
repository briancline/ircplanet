<?php
	
	$password = $pargs[1];
	$password_md5 = md5( $password );

	$account = $this->get_account( $user->get_account_name() );
	$account->set_password( $password_md5 );
	$account->save();
			
	$bot->notice( $user, "Your password has been changed." );

?>