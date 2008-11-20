<?php
	
	// We don't do anything in the core with invites.
	
	$user = $this->get_user( $args[0] );
	$target = $this->get_user_by_nick( $args[2] );
	$chan_name = $args[3];

?>