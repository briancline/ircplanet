<?php
	
	$numeric = $args[0];
	
	$channels = explode( ',', $chan_name );
	foreach( $channels as $chan_name )
	{
		$this->remove_channel_user( $chan_name, $numeric );
	}

?>
