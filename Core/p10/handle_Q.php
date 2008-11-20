<?php
	
	$numeric = $args[0];
	$user = $this->get_user($numeric);
	$this->remove_user( $numeric );
	
?>