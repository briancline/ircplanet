<?php
	
	$bot->noticef( $user->numeric, "%sTIME %s%s",
		CTCP_START,
		date("D M d H:i:s Y T"),
		CTCP_END );

?>