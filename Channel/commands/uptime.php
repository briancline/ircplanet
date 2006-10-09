<?php
	
	$uptime = get_uptime_info();
	$bot->noticef( $user, '%sUptime:%s    %s',
		BOLD_START, BOLD_END, $uptime['pretty'] );
	
	$bot->noticef( $user, '%sBandwidth:%s  %s received, %s sent',
		BOLD_START, BOLD_END, 
		get_pretty_size($this->bytes_received),
		get_pretty_size($this->bytes_sent) );
	
?>