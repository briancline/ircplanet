<?php
	
	$user = $this->get_user($args[0]);
	$this->report_event('USER-WALLOP', $user, $args[2]);

?>