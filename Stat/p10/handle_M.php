<?php

	if( $is_chan )
	{
		db_queryf("update stats_channels set modes = '%s' where channel_name = '%s'",
			$chan->get_modes(),
			$chan->get_name());
	}
	else 
	{
		db_queryf("update stats_users set modes = '%s' where nick = '%s'",
			$user->get_modes(),
			$user->get_nick());
	}
	
?>