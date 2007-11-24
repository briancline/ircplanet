<?php

	if( !($chan = $this->get_channel($chan_name)) ) {
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	if( $cmd_num_args < 2 )
	{
		$bot->noticef( $user, 'Topic on %s%s%s is currently: %s',
			BOLD_START, $chan->get_name(), BOLD_END,
			$chan->get_topic() );
	}
	else
	{
		$new_topic = assemble( $pargs, 2 );
		$bot->topic( $chan->get_name(), $new_topic, $chan->get_ts() );
		$chan->topic = $new_topic;
//		$chan->set_topic( $new_topic );
	}
	
?>