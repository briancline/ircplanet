<?php
	
	$user = $this->get_user( $args[0] );
	
	if( $num_args > 2 )
		$user->set_away( $args[2] );
	else
		$user->set_away();
	
?>