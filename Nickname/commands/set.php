<?php
	
	$account = $this->get_account( $user->get_account_name() );
	$option = strtoupper( $pargs[1] );
	$value = '';
	$who = 'your';
	$Who = ucfirst($who);

	if( $cmd_num_args > 1 )
		$value = assemble( $pargs, 2 );
	
	if($user_level >= 500 && $cmd_num_args > 1)
	{
		$tmp_user = $this->get_account($pargs[1]);
		if($tmp_user != null)
		{
			$option = strtoupper($pargs[2]);
			$value = '';
			$account = $tmp_user;
			
			if($cmd_num_args > 2)
				$value = assemble($pargs, 3);
			
			$who = $Who = $account->get_name() ."'s";
		}
	}
	
	if( $option == 'EMAIL' )
	{
		if( strlen($value) >= MAXLEN_USEREMAIL )
		{
			$bot->notice( $user, 'That email address is too long. Please try something shorter.' );
			return false;
		}
		
		$account->set_email( $value );
		$bot->notice( $user, '%s e-mail address has been updated.', $Who );
	}
	else if( $option == 'INFO' )
	{
		if( strlen($value) >= MAXLEN_INFOLINE )
		{
			$bot->notice( $user, 'That infoline is too long. Please try something shorter.' );
			return false;
		}
		
		$account->set_info_line( $value );
		$bot->noticef( $user, '%s info line has been %s.', $Who,
			empty($value) ? 'cleared' : 'updated' );
	}
	else if( $option == 'AUTOOP' )
	{
		if( empty($value) )
		{
			$value = !$account->auto_ops();
		}
		else
		{
			$value = strtoupper($value);
			if ( $value == 'ON' ) $value = true;
			else if( $value == 'OFF' ) $value = false;
			else {
				$bot->notice( $user, 'Value must either be ON or OFF.' );
				return false;
			}
		}
		
		$account->set_auto_op( $value );
		$bot->noticef( $user, 'Switched %s global auto op to %s.', $who,
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'AUTOVOICE' )
	{
		if( empty($value) )
		{
			$value = !$account->auto_voices();
		}
		else
		{
			$value = strtoupper($value);
			if ( $value == 'ON' ) $value = true;
			else if( $value == 'OFF' ) $value = false;
			else {
				$bot->notice( $user, 'Value must either be ON or OFF.' );
				return false;
			}
		}
		
		$account->set_auto_voice( $value );
		$bot->noticef( $user, 'Switched %s global auto voice to %s.', $who,
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'ENFORCE' )
	{
		if( empty($value) )
		{
			$value = !$account->enforces_nick();
		}
		else
		{
			$value = strtoupper($value);
			if ( $value == 'ON' ) $value = true;
			else if( $value == 'OFF' ) $value = false;
			else {
				$bot->notice( $user, 'Value must either be ON or OFF.' );
				return false;
			}
		}
		
		$account->set_enforce_nick( $value );
		$bot->noticef( $user, 'Toggled %s %s nickname enforcement.', $who,
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'NOPURGE' && $user_level >= 500 )
	{
		if( empty($value) )
		{
			$value = !$account->is_permanent();
		}
		else
		{
			$value = strtoupper($value);
			if ( $value == 'ON' ) $value = true;
			else if( $value == 'OFF' ) $value = false;
			else {
				$bot->notice( $user, 'Value must either be ON or OFF.' );
				return false;
			}
		}
		
		$account->set_permanent( $value );
		$bot->noticef( $user, 'Toggled %s %s nopurge flag.', $who,
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'PASSWORD' )
	{
		$bot->noticef( $user, 'Please use the %sNEWPASS%s command to change %s password.',
			BOLD_START, BOLD_END, $who );
		return false;
	}
	else
	{
		$bot->noticef( $user, '%s%s%s is not a valid option!',
			BOLD_START, $option, BOLD_END );
		return false;
	}
	
	$account->save();
	
?>