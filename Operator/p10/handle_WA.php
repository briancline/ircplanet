<?php
	
	$user = $this->get_user($args[0]);
	$this->report_event('OPER-WALLOP', $user, $args[2]);

?>