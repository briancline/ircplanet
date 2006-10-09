<?php

	$option = strtoupper( $pargs[2] );
	$value = '';
	
	if( $cmd_num_args >= 3 )
		$value = assemble( $pargs, 3 );
	
	
	if( $option == 'PURPOSE' )
	{
		if( strlen($value) >= MAXLEN_CHAN_PURPOSE )
		{
			$bot->notice( $user, 'The channel purpose you provided is too long. Please try something shorter.' );
			return false;
		}
		
		$chan_reg->set_purpose( $value );
		$bot->notice( $user, 'Updated channel purpose.' );
	}
	
	
	
	else if( $option == 'URL' )
	{
		if( strlen($value) >= MAXLEN_CHAN_URL )
		{
			$bot->notice( $user, 'The channel URL you provided is too long. Please try something shorter.' );
			return false;
		}
		
		$chan_reg->set_url( $value );
		$bot->notice( $user, 'Updated channel URL.' );
	}
	
	
	
	else if( $option == 'DEFTOPIC' )
	{
		if( strlen($value) >= MAXLEN_CHAN_DEFAULT_TOPIC )
		{
			$bot->notice( $user, 'The default channel topic you provided is too long. Please try something shorter.' );
			return false;
		}
		
		$chan_reg->set_default_topic( $value );
		$bot->topic( $chan_name, $value );
		$bot->notice( $user, 'Updated default channel topic.' );
	}
	
	
	
	else if( $option == 'DEFMODES' )
	{
		if( strlen($value) >= MAXLEN_CHAN_DEFAULT_MODES )
		{
			$bot->notice( $user, 'The default channel modes you provided were too long. Please try something shorter.' );
			return false;
		}
		
		$chan_reg->set_default_modes( $value );
		$bot->mode( $chan_name, $value );
		$bot->notice( $user, 'Updated default channel modes.' );
	}
	
	
	
	else if( $option == 'INFOLINES' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->shows_info_lines();
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
		
		$chan_reg->set_info_lines( $value );
		$bot->noticef( $user, 'Toggled display of channel info lines to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'AUTOOP' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->auto_ops();
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
		
		$chan_reg->set_auto_op( $value );
		$bot->noticef( $user, 'Toggled channel auto op to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'AUTOOPALL' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->auto_ops_all();
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
		
		$chan_reg->set_auto_op_all( $value );
		$bot->noticef( $user, 'Toggled channel auto op everyone to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'AUTOVOICE' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->auto_voices();
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
		
		$chan_reg->set_auto_voice( $value );
		$bot->noticef( $user, 'Toggled channel auto voice to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'AUTOVOICEALL' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->auto_voices_all();
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
		
		$chan_reg->set_auto_voice_all( $value );
		$bot->noticef( $user, 'Toggled channel auto voice everyone to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'AUTOLIMIT' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->auto_limits();
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
		
		$chan_reg->set_auto_limit( $value );
		$bot->noticef( $user, 'Toggled channel auto user limit to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'AUTOLIMITBUFFER' )
	{
		if( !is_numeric($value) || $value < MIN_CHAN_AUTOLIMIT_BUFFER || $value > MAX_CHAN_AUTOLIMIT_BUFFER )
		{
			$bot->noticef( $user, 'The user count buffer must be an integer ranging from %d to %d.',
				MIN_CHAN_AUTOLIMIT_BUFFER, MAX_CHAN_AUTOLIMIT_BUFFER );
			return false;
		}
		
		$chan_reg->set_auto_limit_buffer( $value );
		$bot->noticef( $user, 'Updated auto limit usercount buffer to %d users.', $value );
	}
	
	
	
	else if( $option == 'AUTOLIMITWAIT' )
	{
		if( !is_numeric($value) || $value < MIN_CHAN_AUTOLIMIT_WAIT || $value > MAX_CHAN_AUTOLIMIT_WAIT )
		{
			$bot->noticef( $user, 'The auto limit delay must be an integer ranging from %d to %d.',
				MIN_CHAN_AUTOLIMIT_WAIT, MAX_CHAN_AUTOLIMIT_WAIT );
			return false;
		}
		
		$chan_reg->set_auto_limit_wait( $value );
		$bot->noticef( $user, 'Updated auto limit delay to %d seconds.', $value );
	}
	
	
	
	else if( $option == 'STRICTOP' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->strict_ops();
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
		
		$chan_reg->set_strict_op( $value );
		$bot->noticef( $user, 'Toggled channel strict ops to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'STRICTVOICE' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->strict_voices();
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
		
		$chan_reg->set_strict_voice( $value );
		$bot->noticef( $user, 'Toggled channel strict voices to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'STRICTTOPIC' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->strict_topic();
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
		
		$chan_reg->set_strict_topic( $value );
		$bot->noticef( $user, 'Toggled channel strict topic to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else if( $option == 'STRICTMODES' )
	{
		if( empty($value) )
		{
			$value = !$chan_reg->strict_modes();
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
		
		$chan_reg->set_strict_mode( $value );
		$bot->noticef( $user, 'Toggled channel strict modes to %s.',
			$value ? 'ON' : 'OFF' );
	}
	
	
	
	else
	{
		$bot->noticef( $user, '%s%s%s is not a valid channel option!',
			BOLD_START, $option, BOLD_END );
		return false;
	}
	
	
	$chan_reg->save();	
	
?>