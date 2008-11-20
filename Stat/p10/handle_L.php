<?php

	foreach($channels as $chan_name)
	{
		db_queryf("delete from stats_channel_users where channel_name = '%s' and nick = '%s'", 
			$chan->get_name(),
			$user->get_nick());
	}
	
?>