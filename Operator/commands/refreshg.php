<?php

	$gline_count = 0;
	foreach( $this->glines as $gline_key => $gline )
	{
		$this->enforce_gline( $gline );
		$gline_count++;
	}
	
	if( $gline_count > 0 )
		$bot->noticef( $user, 'Enforced %s g-lines.', $gline_count );
	else 
		$bot->notice( $user, 'No g-lines to enforce.' );
	
?>