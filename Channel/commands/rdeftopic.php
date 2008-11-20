<?php

	$chan = $this->get_channel( $chan_name );
	$def_topic = $chan_reg->get_default_topic();
	$bot->topic( $chan_name, $def_topic, $chan->get_ts() );
	
?>
