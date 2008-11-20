<?php
	
	db_queryf("delete from stats_users where nick = '%s'", $kill_user->get_nick());
	db_queryf("delete from stats_channel_users where nick = '%s'", $kill_user->get_nick());

?>