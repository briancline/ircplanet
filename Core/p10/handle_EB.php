<?php
	
	if( $args[0] == UPLINK_NUM )
		$this->sendf( FMT_ENDOFBURST_ACK, SERVER_NUM );
	
?>