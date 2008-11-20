<?php

	$add = $args[3][0];
	
	if( $add )
	{
		$mask = substr( $args[3], 1 );
		$duration = $args[4];
		$reason = $args[5];
		
		$this->add_gline( $mask, $duration, $reason );
	}
	else 
	{
		$this->remove_gline( $mask );
	}
	
?>