<?php
	
	$nick = $args[2];

	if( $num_args > 4 )
	{
	}
	else
	{
		$user = $this->get_user( $args[0] );
	}
	
	
	if( ($account = $this->get_account($nick)) && $account->get_name() != $user->get_account_name() )
	{
		$user = $this->get_user( $numeric );
		$notice = "The nick ". BOLD_START . $nick . BOLD_END ." is registered.";
		
		if( $user->is_logged_in() )
		{
			$bot->notice( $user, "$notice Please change your nick or reconnect and log in as ". 
				BOLD_START . $account->get_name() . BOLD_END . "." );
		}
		else
		{
			$bot->notice( $user, "$notice Please change your nick or log in to account ".
				BOLD_START . $account->get_name() . BOLD_END . "." );
		}

		if( $account->enforces_nick() )
		{
			$this->add_timer( false, 60, 'enforce.php', $user->get_numeric(), $account->get_name() );
		}
	}

?>
