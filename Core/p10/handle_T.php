<?php
	
	$topic_arg = 3;
	
	if( TOPIC_BURSTING )
		$topic_arg = 5;
	
	$topic = assemble( $args, $topic_arg );
	
	if( $chan = $this->get_channel($chan_key) )
		$chan->set_topic( $topic );

?>