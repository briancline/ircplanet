<?php

	if( !($target = $this->get_user_by_nick($pargs[1])) )
	{
		$bot->noticef( $user, 'There is no user named %s.', $pargs[1] );
		return false;
	}
	
	$acct_str = '';
	if( $target->has_account_name() )
	{
		$acct_str = ', logged in as ';
		
		if( !$target->is_logged_in() )
			$acct_str .= 'unknown account ';
		
		$acct_str = $target->get_account_name();
		
		if( $acct = $this->get_account($target->get_account_name()) )
			$acct_str .= ' (Registered on '. get_date($acct->get_register_ts()) .')';
	}
	
	$server = $this->get_server( $target->get_server_numeric() );
	
	$channels = $target->get_channel_list();
	$chan_list = '';
	foreach( $channels as $chan_name )
	{
		$chan = $this->get_channel( $chan_name );
		
		if( $chan->is_voice($target->get_numeric()) )
			$chan_list .= '+';
		
		if( $chan->is_op($target->get_numeric()) )
			$chan_list .= '@';
		
		$chan_list .= $chan->get_name() .' ';
	}
	
	$bot->noticef( $user, 'Nick:       %s (User modes +%s)', $target->get_nick(), $target->get_modes() );
	if( $target->is_logged_in() )
		$bot->noticef( $user, 'Account:    %s', $acct_str );
	
	$bot->noticef( $user, 'Full mask:  %s [%s]', $target->get_full_mask(), $target->get_ip() );
	$bot->noticef( $user, 'Channels:   %s', $chan_list );
	$bot->noticef( $user, 'Server:     %s', $server->get_name() );
	$bot->noticef( $user, 'Signed on:  '. get_date($target->get_signon_ts()) );

?>