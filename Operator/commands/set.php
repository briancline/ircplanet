<?php
	
	$option = strtoupper( $pargs[1] );
	$value = '';
	
	if( $cmd_num_args > 1 )
		$value = assemble( $pargs, 2 );
	
	$account = $this->get_account( $user->get_account_name() );
	
	debug( "*** $value" );
	
	if( $option == 'EMAIL' )
	{
		if( strlen($value) >= MAXLEN_USEREMAIL )
		{
			$bot->notice( $user, 'Your email address is too long. Please try something shorter.' );
			return false;
		}
		
		$account->set_email( $value );
		$bot->notice( $user, 'Your e-mail address has been updated.' );
	}
	else if( $option == 'INFO' )
	{
		if( strlen($value) >= MAXLEN_INFOLINE )
		{
			$bot->notice( $user, 'Your infoline is too long. Please try something shorter.' );
			return false;
		}
		
		$account->set_info_line( $value );
		$bot->noticef( $user, 'Your info line has been %s.',
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
		$bot->noticef( $user, 'Switched global auto op to %s.',
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
		$bot->noticef( $user, 'Switched global auto voice to %s.',
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
		$bot->noticef( $user, 'Toggled %s nickname enforcement.',
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'PASSWORD' )
	{
		$bot->noticef( $user, 'Please use the %sNEWPASS%s command to change your password.',
			BOLD_START, BOLD_END );
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