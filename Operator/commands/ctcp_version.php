<?php
	
	$bot->noticef( $user->numeric, "%sVERSION %s (%s)%s",
		CTCP_START,
		SERVICE_VERSION,
		CORE_VERSION,
		CTCP_END );

?>