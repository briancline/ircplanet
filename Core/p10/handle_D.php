<?php
	
	$kill_numeric = $args[2];
	$kill_user = $this->get_user($kill_numeric);
	$this->remove_user( $kill_numeric );
	
?>