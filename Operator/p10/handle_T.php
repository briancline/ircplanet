<?php

	// Don't log topics during initial burst, as it could flood the log channel.
	if( $this->finished_burst )
	{
		if( strlen($args[0]) == BASE64_SERVLEN )
			$source = $this->get_server( $args[0] );
		else
			$source = $this->get_user( $args[0] );
		
		$this->report_event( 'TOPIC', $source, $chan, $topic );
	}

?>