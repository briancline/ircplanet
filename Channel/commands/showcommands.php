<?php

	$high_level = 0;

	foreach( $this->commands_list as $level => $command_list )
	{
		if( $level > $user_level )
			continue;
		if( empty($high_level) )
			$high_level = $level;
		
		$bot->noticef( $user, "%sLevel %". strlen($high_level) ."s:%s %s",
			BOLD_START,
			$level,
			BOLD_END,
			$command_list
		);
	}
	
?>
