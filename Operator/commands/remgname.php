<?php

	$realname = sprintf('$R%s', $pargs[1] );
	
	if( $this->get_gline($realname) )
		$this->remove_gline( $realname );
	
	$this->sendf( FMT_GLINE_REMOVE, SERVER_NUM, $realname );
	
?>
