<?php

	$reg = $this->get_channel_reg( $chan_name );
	
	if( $reg && $reg->auto_limits() && !$reg->has_pending_autolimit() )
	{
		$this->add_timer( false, $reg->get_auto_limit_wait(), 'auto_limit.php', $chan_name );
		$reg->set_pending_autolimit( true );
	}
	
?>