<?php

	$source = $args[0];
	$server = $this->get_server_by_name( $args[2] );
	$reason = $args[4];
	
	if( $server->get_numeric() == UPLINK_NUM )
	{
		debug( "Uplink is squitting me! ($reason)" );
		$this->close();
	}
	
	$this->remove_server( $server->numeric );
	
?>