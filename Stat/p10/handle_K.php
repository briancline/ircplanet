<?php

	$user = $this->get_user($numeric);
	$chan = $this->get_channel($args[2]);
	
	db_queryf("delete from stats_channel_users where channel_name = '%s' and nick = '%s'", 
		$chan->get_name(),
		$user->get_nick());


?>