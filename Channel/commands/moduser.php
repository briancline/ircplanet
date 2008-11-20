<?php

	if( !($reg = $this->get_channel_reg($chan_name)) ) {
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	
	$mod_uid = $pargs[2];
	$option = strtoupper( $pargs[3] );
	$value = '';
	
	if( $cmd_num_args >= 4 )
		$value = assemble( $pargs, 4 );
	
	if( $mod_user = $this->get_account($mod_uid) )
	{
		if( (!$access = $this->get_channel_access_account($chan_name, $mod_user)) )
		{
			$bot->noticef( $user, '%s is not in the %s access list.',
				$mod_user->get_name(), $reg->get_name() );
			return false;
		}
		
		if( $option == 'LEVEL' )
		{
			$new_level = $value;
			if( $new_level < 1 || $new_level > 500 )
			{
				$bot->notice( $user, 'Access level must range from 1 to 500.' );
				return false;
			}
			
			$access->set_level( $new_level );
			$bot->noticef( $user, '%s\'s level on %s has has been changed to %d.',
				$mod_user->get_name(), $reg->get_name(), $new_level );
		}
		else if( $option == 'AUTOOP' )
		{
			if( empty($value) )
			{
				$value = !$access->auto_ops();
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
			
			$access->set_auto_op( $value );
			$bot->noticef( $user, '%s\'s auto-op on %s has been toggled %s.',
				$mod_user->get_name(), $reg->get_name(), $value ? 'ON' : 'OFF' );
		}
		else if( $option == 'AUTOVOICE' )
		{
			if( empty($value) )
			{
				$value = !$access->auto_voices();
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
			
			$access->set_auto_voice( $value );
			$bot->noticef( $user, '%s\'s auto-voice on %s has been toggled %s.',
				$mod_user->get_name(), $reg->get_name(), $value ? 'ON' : 'OFF' );
		}
		else
		{
			$bot->noticef( $user, '%s%s%s is not a valid setting!', BOLD_START, $option, BOLD_END );
			return false;
		}
		
		$access->save();
	}
	else
	{
		$bot->noticef( $user, 'Account %s does not exist.', $mod_uid );
		return false;
	}
	
?>
