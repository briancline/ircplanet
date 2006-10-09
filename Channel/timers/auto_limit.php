<?php
	
	$chan_name = $timer_data[0];
	$chan_reg = $this->get_channel_reg( $chan_name );
	$chan = $this->get_channel( $chan_name );
	
	if( !$chan_reg || !$chan )
		return false;
	
	$user_count = $chan->get_user_count();
	$user_buffer = $chan_reg->get_auto_limit_buffer();
	$new_limit = $user_count + $user_buffer;
	
	$bot->mode( $chan_name, "+l $new_limit" );
	$chan->set_limit( $new_limit );
	$chan_reg->set_pending_autolimit( false );
	
?>