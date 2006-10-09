<?php
	
	if( strlen($source) == BASE64_SERVLEN )
		$source = $this->get_server( $source );
	else
		$source = $this->get_user( $source );
	
	$this->report_event( 'SQUIT', $source, $server, "($reason)" );

?>