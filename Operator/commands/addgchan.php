<?php

	$channel = $pargs[1];
	$duration = $pargs[2];
	$reason = assemble( $pargs, 3 );
	
	if( !eregi('^#', $channel) )
	{
		$bot->noticef( $user, '%s is not a valid channel name.', $channel );
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
	
	$gline = $this->add_gline( $channel, $duration_secs, $reason );
	$this->enforce_gline( $gline );
	
?>
