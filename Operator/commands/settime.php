<?php

	$this->sendf( FMT_SETTIME, SERVER_NUM, time(), SERVER_NAME );
	$bot->noticef( $user, 'Set network time to %s.', get_date(time()) );
	
?>