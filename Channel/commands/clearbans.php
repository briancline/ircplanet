<?php

	$chan = $this->get_channel( $chan_name );
	
	if( $chan )
	{
		$active_bans = $chan->get_matching_bans( '*' );

		if( count($active_bans) > 0 )
			$bot->unban( $chan->get_name(), $active_bans );
	}
	
	$chan_reg->clear_bans();
	
?>