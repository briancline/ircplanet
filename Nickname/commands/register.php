<?php
	
	$numeric = $args[0];
	$user = $this->get_user( $numeric );
	$user_name = $user->get_nick();
	$password = $pargs[1];
	$email = $pargs[2];
	
	if( !$user->is_logged_in() )
	{
		if( !is_valid_email($email) )
		{
			$bot->notice( $user, "You have specified an invalid e-mail address. ".
				"Please try again." );
			return false;
		}
		
		if( $account = $this->get_account_by_email($email) )
		{
			$bot->notice( $user, "That e-mail address is already associated ".
				"with a registered nickname." );
			return false;
		}
		
		if( $account = $this->get_account($user_name) )
		{
			$bot->noticef( $numeric,
				"The nickname %s%s%s has already been registered. Please choose another.",
				BOLD_START, $user_name, BOLD_END );
			return false;
		}
		
		$password_md5 = md5( $password );
		
		$account = new DB_User();
		$account->set_name( $user->get_nick() );
		$account->set_password( $password_md5 );
		$account->set_email( $email );
		$account->set_auto_op( true );
		$account->set_auto_voice( true );
		$account->update_lastseen();
		$account->save();
		
		$this->add_account( $account );
		
		if( !$user->has_account_name() )
		{
			$this->sendf( FMT_ACCOUNT, SERVER_NUM, $numeric, $user_name );
			$user->set_account_name( $user_name );
		}
		
		$bot->noticef( $numeric,
			"Your account, %s%s%s, has been registered. You are now logged in.",
			BOLD_START, $user_name, BOLD_END );
	}
	else
	{
		$bot->notice( $numeric, "You have already registered your nick and logged in." );
	}

?>
