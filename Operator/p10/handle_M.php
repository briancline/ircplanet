<?php

	if( strlen($args[0]) == BASE64_SERVLEN )
		$source = $this->get_server( $args[0] );
	else
		$source = $this->get_user( $args[0] );
	
	if( $is_chan )
		$target = $chan;
	else 
		$target = $user;
	
	$modes = $args[3];
	
	if(!$is_chan)
		$this->report_event( 'MODE', $source, $target, $modes, $readable_args );

?>