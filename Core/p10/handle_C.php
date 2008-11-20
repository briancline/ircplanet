<?php

	$numeric = $args[0];
	$ts = $args[3];
	$chan_list = $args[2];
	$channels = explode( ',', $chan_list );
	
	/**
	 * AEBHx C #ayva 1131745531
	 */
	
	foreach( $channels as $chan_name )
	{
		$chan_key = strtolower( $chan_name );
		
		$this->add_channel( $chan_name, $ts );
		$this->add_channel_user( $chan_name, $numeric, 'o' );
	}

?>
