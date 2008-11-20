<?php
	
	$user_name = $pargs[1];

	if( !($account = $this->get_account($user_name)) )
	{
		$bot->noticef( $user, "%s is not a registered nick.", $user_name );
		return false;
	}

	
	$instances = array();
	foreach($this->users as $numeric => $tmp_user)
	{
		if($tmp_user->get_account_id() == $account->get_id())
		{
			$instances[] = $tmp_user->get_full_mask();
		}
	}

	$is_admin = ($this->get_user_level($user) > 1);
	$privileged = $is_admin || $user->is_oper() || ($account->get_id() == $user->get_account_id());
	$logged_in = !empty( $instances );

	$bot->noticef( $user, 'Account information for %s%s%s', BOLD_START, $account->get_name(), BOLD_END );
	$bot->noticef( $user, str_repeat('-', 70) );

	if( $privileged && $logged_in )
	{
		$bot->noticef( $user, 'Logged In:    %s - %s', $logged_in ? 'Yes' : 'No ', $instances[0] );
		unset( $instances[0] );

		foreach($instances as $mask)
			$bot->noticef( $user, '                    %s', $mask );
	}
	else
	{
		$bot->noticef( $user, 'Logged In:    %s', $logged_in ? 'Yes' : 'No ' );
	}

	if( $privileged )
	{
		$bot->noticef( $user, 'E-mail Addr:  %s', $account->get_email() );
		$bot->noticef( $user, 'Enforcement:  %s       Auto Op: %s      Auto Voice: %s',
				$account->enforces_nick() ? 'Yes' : 'No',
				$account->auto_ops()      ? 'Yes' : 'No',
				$account->auto_voices()   ? 'Yes' : 'No'
		);
		$bot->noticef( $user, 'Suspended:    %s       Permanent: %s',
				$account->is_suspended()  ? 'Yes' : 'No',
				$account->is_permanent()  ? 'Yes' : 'No'
		);
	}
	
	if( $account->has_info_line() )
		$bot->noticef( $user, 'Info Line:    %s', $account->get_info_line() );
	
	$bot->noticef( $user, 'Registered:   %s', date('l j F Y h:i:s A T (\G\M\TO)', $account->get_register_ts()) );
	$bot->noticef( $user, 'Last Seen:    %s', date('l j F Y h:i:s A T (\G\M\TO)', $account->get_lastseen_ts()) );

?>
