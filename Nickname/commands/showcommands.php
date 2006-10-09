<?php
	
	$commands = array();
	
	foreach( $this->command_info as $command_key => $command_info )
	{
		$level = $command_info['level'];
		$commands[$level][] = $command_key;
		asort( $commands[$level] );
	}
	
	krsort( $commands );
	
	foreach( $commands as $level => $command_array )
	{
		if( $level > $user_level )
			continue;
		
		asort( $command_array );
		$command_list = join( " ", $command_array );
		
		$bot->noticef( $user, "%sLevel %". strlen($user_level) ."s:%s %s",
			BOLD_START,
			$level,
			BOLD_END,
			$command_list
		);
	}
	
?>