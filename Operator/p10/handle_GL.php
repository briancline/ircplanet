<?php

	$server = $this->get_server( $args[0] );
	$gline = $this->get_gline( $mask );
	
	if($add)
		$mask = "+$mask";
	else
		$mask = "-$mask";
	
	$this->report_event( 'GLINE', $server, $gline, '('. $gline->get_reason() .')' );

?>