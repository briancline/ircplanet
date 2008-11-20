<?php
	
	$ping_text = assemble( $pargs, 1 );
	$bot->noticef( $user->numeric, "%sPING %s",
		CTCP_START,
		$ping_text );

?>