<?php
	
	$numeric = $args[0];
	$user = $this->get_user( $numeric );
	$user_name = $pargs[1];
	$password = $pargs[2];
	
	if( $account = $this->get_account($user_name) )
	{
		$password_md5 = md5( $password );
		
		if( $account->get_password() == $password_md5 )
		{
			if( $user->is_logged_in() )
			{
				$bot->notice( $numeric, "You are already logged in as ". $user->get_account_name() ."!" );
			}
			else
			{
				$user_name = $account->get_name();
				$bot->notice( $numeric, "Authentication successful as $user_name!" );
				$this->sendf( FMT_ACCOUNT, SERVER_NUM, $numeric, $user_name );
				$user->set_account_name( $user_name );
			}
		}
		else
		{
			$bot->notice( $numeric, "Invalid password!" );
		}
	}
	else
	{
		$bot->notice( $numeric, "No such account!" );
	}

?>