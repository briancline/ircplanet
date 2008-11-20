<?php

	$chan_name = $pargs[1];
	$chan = $this->get_channel( $chan_name );
	
	$bot->noticef( $user, "Channel:  %s (%d users, %d ops, %d voices)", 
		$chan->get_name(), $chan->get_user_count(),
		$chan->get_op_count(), $chan->get_voice_count() );
	$bot->noticef( $user, "Modes:    +%s", $chan->get_modes() );
	$bot->noticef( $user, "Created:  %s", date('D j M Y g:i:sa T', $chan->get_ts()) );
	
	$topic = $chan->get_topic();
	if(!empty($topic))
		$bot->noticef( $user, "Topic:    %s", $topic );
	
?>