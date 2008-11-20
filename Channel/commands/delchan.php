<?php
	
	if( $reg = $this->get_channel_reg($chan_name) )
	{
		$this->remove_channel_reg( $reg );
		$reg->delete();
		
		$reason = 'So long, and thanks for all the fish!';		
		if( $cmd_num_args > 1 )
			$reason = assemble( $pargs, 2 );
		
		if( ($chan = $this->get_channel($chan_name)) && $chan->is_on($bot->get_numeric()) )
		{
			$this->sendf( FMT_PART_REASON, $bot->get_numeric(), $chan_name, $reason );
			$this->remove_channel_user( $chan_name, $bot->get_numeric() );
		}
	}

?>