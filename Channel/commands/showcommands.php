<?php
	
	$commands = array();
	$highest_level = 0;
	
	foreach( $this->command_info as $command_key => $command_info )
	{
/*
		// Only show channel-centric commands if a channel was specified
		if( empty($chan_name) && eregi('<channel>', $command_info['syntax']) 
				&& $command_key != 'register' 
				&& $command_key != 'adminreg' 
				&& $command_info['level'] > 0 )
			continue;
*/	
		if( $level > $highest_level )
			$highest_level = $level;

		$level = $command_info['level'];
		$commands[$level][] = $command_key;
	}
	
	krsort( $commands );
	
	foreach( $commands as $level => $command_array )
	{
		if( $level > $user_level )
			continue;
		
		asort( $command_array );
		$command_list = join( " ", $command_array );
		
		$bot->noticef( $user, "%sLevel %". strlen($highest_level) ."s:%s %s",
			BOLD_START,
			$level,
			BOLD_END,
			$command_list
		);
	}
	
?>