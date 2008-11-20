<?php
	
	$src_numeric = $args[0];
	if(strlen($src_numeric) == 2)
		$source = $this->get_server($src_numeric);
	else 
		$source = $this->get_user($src_numeric);
	
	$this->report_event('KILL', $source, $kill_user, $args[3]);

?>