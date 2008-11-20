<?php
	
	if( $cmd_num_args == 1 )
	{
		$user_name = $user->get_nick();
		$password = $pargs[1];
	}
	else
	{
		$user_name = $pargs[1];
		$password = $pargs[2];
	}
	
	if( $account = $this->get_account($user_name) )
	{
		$password_md5 = md5( $password );
		
		if( $account->get_password() == $password_md5 )
		{
			if( $user->is_logged_in() )
			{
				$bot->notice( $user, "You are already logged in as ". $user->get_account_name() ."!" );
			}
			else
			{
				$user_name = $account->get_name();
				$bot->notice( $user, "Authentication successful as $user_name!" );
				$this->sendf( FMT_ACCOUNT, SERVER_NUM, $user->get_numeric(), $user_name );
				$user->set_account_name( $user_name );
				$user->set_account_id( $account->get_id() );
			}
		}
		else
		{
			$bot->notice( $user, "Invalid password!" );
		}
	}
	else
	{
		$bot->notice( $user, "No such account!" );
	}

?>