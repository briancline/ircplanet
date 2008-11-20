<?php

	$user = $this->get_user( $args[0] );
	$reg = $this->get_channel_reg( $chan_name );
	$opped = $voiced = $kicked = false;
	$infoline = '';
	
	if( $reg )
	{
		if( ($ban = $reg->get_matching_ban($user->get_full_mask())) )
		{
			$reason = $ban->get_reason();
			if( empty($reason) )
				$reason = 'Banned';
			
			$bot->ban( $chan_name, $ban->get_mask() );
			$bot->kick( $chan_name, $numeric, $reason );
			$kicked = true;
		}

		if( !$kicked && $reg->auto_limits() && !$reg->has_pending_autolimit() )
		{
			$this->add_timer( false, $reg->get_auto_limit_wait(), 'auto_limit.php', $chan_name );
			$reg->set_pending_autolimit( true );
		}

		if( !$kicked )
		{
			if( $reg->auto_ops_all() )
				$bot->op( $chan_name, $user->get_numeric() );
			else if( $reg->auto_voices_all() )
				$bot->voice( $chan_name, $user->get_numeric() );
		}
	}
	
	if( $user->is_logged_in() && ($account = $this->get_account($user->get_account_name())) && $reg && !$kicked )
	{
		if(	($lev = $this->get_channel_access($chan_name, $user)) )
		{
			$level = $lev->get_level();
			if( $level >= 100 && $account->auto_ops() && $reg->auto_ops() && $lev->auto_ops() )
			{
				$bot->op( $chan_name, $user->get_numeric() );
				$opped = true;
			}
			else if( $level >= 1 && $account->auto_voices() && $reg->auto_voices() && $lev->auto_voices() )
			{
				$bot->voice( $chan_name, $user->get_numeric() );
				$voiced = true;
			}
			
			if( $account->has_info_line() && $reg->shows_info_lines() )
				$infoline = sprintf( '[%s] %s', $account->get_name(), $account->get_info_line() );
		}
		
		if( !empty($infoline) )
			$bot->message( $chan_name, $infoline );
	}

?>