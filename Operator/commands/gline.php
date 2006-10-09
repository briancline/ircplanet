<?php

	$mask = $pargs[1];
	$duration = $pargs[2];
	$reason = assemble( $pargs, 3 );
	
	if( $tmp_user = $this->get_user_by_nick($mask) )
	{
		$mask = $tmp_user->get_gline_mask();
	}
	else if( !eregi('[@\.]', $mask) )
	{
		$bot->noticef( $user, 'Gline masks must be in the ident@host form. Nick masks are not allowed.' );
		return false;
	}
	
	$affected_hosts = $this->get_matching_userhost_count( $mask );
	$affected_ratio = ($affected_hosts / count($this->users)) * 100;
	
	if( $mask == '*@*' || $mask == '*@*.*' )
	{
		$bot->noticef( $user, 'Your mask is not restrictive enough.' );
		return false;
	}
	
	if( !($duration_secs = convert_duration($duration)) )
	{
		$bot->notice( $user, 'Invalid duration specified! See help for more details.' );
		return false;
	}
	
	$max_ts = 2147483647;
	$expire_ts = time() + $duration_secs;
	
	if( $expire_ts > $max_ts || $expire_ts < 0 )
	{
		$bot->noticef( $user, 'The duration you specified is too large. Please try something more sensible.' );
		return false;
	}
	
	$gline = $this->add_gline( $mask, $duration_secs, $reason );
	$this->enforce_gline( $gline );
	
	$mask = fix_host_mask( $mask );
	
?>