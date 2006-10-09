<?php

	$mask = $pargs[1];
	
	if( $this->get_gline($mask) )
		$this->remove_gline( $mask );
	
	$this->sendf( FMT_GLINE_REMOVE, SERVER_NUM, $mask );
	
?>