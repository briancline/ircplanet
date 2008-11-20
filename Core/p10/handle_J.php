<?php
	
	$numeric = $args[0];
	$parted_all_chans = false;
	
	/**
	 * /join 0 is the same as /parting all channels.
	 */
	if( $args[2] == '0' )
	{
		$this->remove_user_from_all_channels( $numeric );
		$parted_all_chans = true;
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
