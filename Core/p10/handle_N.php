<?php
	
	if( $num_args == 4 )
	{
		// This is an existing user changing their nick
		$numeric = $args[0];
		$new_nick = $args[2];
		$old_nick = $this->users[$numeric]->get_nick();
		$this->users[$numeric]->set_nick( $new_nick );
	}
	else
	{
		// This is a new user
		$nick = $args[2];
		$start_ts = $args[4];
		$ident = $args[5];
		$host = $args[6];
		$ip = base64_to_ip( $args[$num_args - 3] );
		$numeric = $args[$num_args - 2];
		$desc = $args[$num_args - 1];
		$account = '';
		$modes = '';
		
		if( $num_args == 12 )
		{
			$modes = $args[7];
			$account = $args[8];
		}
		if( $num_args == 11 )
		{
			if( $args[7][0] == '+')
				$modes = $args[7];
			else
				$account = $args[7];
		}
		
		$this->add_user( $numeric, $nick, $ident, $host, $desc, $start_ts, $ip, $modes, $account );
	}

	$user = $this->get_user( $numeric );
	$account_name = $user->get_account_name();
	
	if( $account = $this->get_account($account_name) )
	{
		$user->set_account_id( $account->get_id() );
		debug( "Updated account id for $nick to ". $account->get_id() );
		$account->update_lastseen();
		$account->save();
	}
		
?>