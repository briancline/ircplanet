<?php

	$privileged = false;
	$chan_name = $pargs[1];
	
	if($user_admin_level >= 500 || $user_channel_level > 0) {
		$privileged = true;
	}

	if(!($chan = $this->get_channel_reg($chan_name)))
	{
		$bot->noticef($user, '%s is not a registered channel.', $chan_name);
		return false;
	}
	
	$bot->noticef($user, 'Channel Information for %s', $chan->get_name());
	$bot->noticef($user, str_repeat('-', 50));
	$bot->noticef($user, 'Register date:    %s', get_date($chan->get_register_ts()));
	$bot->noticef($user, 'Channel purpose:  %s', $chan->get_purpose());
	$bot->noticef($user, 'Default modes:    +%s', $chan->get_default_modes());
	$bot->noticef($user, 'Default topic:    %s', $chan->get_default_topic());
	$bot->noticef($user, 'Permanent:        %s', $chan->is_permanent() ? 'Yes' : 'No' );
	$bot->noticef($user, 'Auto Op:          %s', $chan->auto_ops() ? 'Yes' : 'No' );
	
?>