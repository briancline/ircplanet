<?php

	foreach( $this->db_channels as $chan_key => $chan_reg )
	{
		$chan = $this->get_channel( $chan_key );
		if( !$chan_reg->is_suspended() && $chan->is_on($bot->get_numeric()) )
			$this->op( $chan->get_name(), $bot->get_numeric() );
	}

?>