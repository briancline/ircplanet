<?php
	
	$numeric = $args[0];
	
	/**
	 * /join 0 is the same as /parting all channels.
	 */
	if( $args[2] == '0' )
	{
		$this->remove_user_from_all_channels( $numeric );
	}
	else
	{
		$channels = explode( ',', $chan_name );
		foreach( $channels as $chan_name )
		{
			$this->add_channel_user( $chan_name, $numeric );
		}
	}
	
?>
