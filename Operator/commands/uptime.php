<?php
	
	$uptime = get_uptime_info();
	$bot->notice( $user, BOLD_START .'Uptime:'. BOLD_END .' '. $uptime['pretty'] );
	$bot->notice( $user, BOLD_START .'Bandwidth:'. BOLD_END .' '.
		get_pretty_size($this->bytes_received) .' received, '.
		get_pretty_size($this->bytes_sent) .' sent' );

?>