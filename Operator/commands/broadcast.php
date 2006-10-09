<?php

	$message = assemble( $pargs, 1 );
	
	foreach( $this->users as $tmp_numeric => $tmp_user )
	{
		if($tmp_user->is_bot())
			continue;
		
		$bot->noticef( $tmp_user, '%s[Global Broadcast]:%s %s',
			BOLD_START, BOLD_END, $message );
	}

?>