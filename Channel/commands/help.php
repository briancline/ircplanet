<?php
	
	$help_topic = 'help';
	$help_level = 0;
	
	if( $cmd_num_args > 0 )
	{
		$help_topic = assemble( $pargs, 1 );
		$help_topic_first = $pargs[1];
		$help_level = $this->get_command_level( $help_topic_first );
	}
	
	$res = db_query( "select text from help where service = 'CS' and topic = '$help_topic' and minlevel <= $user_level" );
	if( $res && mysql_num_rows($res) > 0 )
	{
		$row = mysql_fetch_assoc( $res );
		$lines = explode( "\n", $row['text'] );
		$spacing = str_repeat( ' ', 30 - strlen($help_topic) );
		$help_syntax = $this->get_command_syntax( $help_topic );
		
		$bot->noticef( $user, "%sHELP on %s %s %10s%s",
			BOLD_START, $help_topic, $spacing, 'Level '. $help_level, BOLD_END );
		$bot->noticef( $user, "" );
		
		if(!eregi('syntax:', $row['text']))
		{
			$bot->noticef( $user, "%sSyntax:%s %s %s", BOLD_START, BOLD_END, $help_topic, $help_syntax );
			$bot->noticef( $user, "" );
		}
				
		foreach( $lines as $line )
		{
			$line = str_replace( "%N", $bot->get_nick(), $line );
			$line = str_replace( "%S", SERVER_NAME, $line );
			$line = str_replace( "%B", BOLD_START, $line );
			$bot->notice( $user, $line );

			while(ereg('(\%[A-Z_]+\%)', $line, $regs))
			{
				eval('$sub_val = '. str_replace('%', '', $regs[1]) .';');
				$line = str_replace($regs[1], $sub_val, $line);
			}
		}
	}
	else
	{
		$bot->noticef( $user, "No help is available for %s%s%s.",
			BOLD_START, $help_topic, BOLD_END );
	}
	
?>