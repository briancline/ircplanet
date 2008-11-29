<?php

	$channel = $pargs[1];
	
	if( $this->get_gline($channel) )
		$this->remove_gline( $channel );
	
	$this->sendf( FMT_GLINE_REMOVE, SERVER_NUM, $channel );
	
?>
