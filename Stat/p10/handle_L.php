<?php

	$user = $this->get_user($numeric);
	
	foreach($channels as $chan_name)
	{
		db_queryf("delete from stats_channel_users where channel_name = '%s' and nick = '%s'", 
			$chan_name, $user->get_nick());
	}
	
?>
