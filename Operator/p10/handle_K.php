<?php

	$user = $this->get_user($numeric);
	$chan = $this->get_channel($args[2]);
	
	$src_numeric = $args[0];
	if(strlen($src_numeric) == 2)
		$source = $this->get_server($src_numeric);
	else 
		$source = $this->get_user($src_numeric);
	
	$reason = ($num_args > 4) ? "($args[4])" : "";
	
	$this->report_event('KICK', $source, $chan, $user, $reason );

?>