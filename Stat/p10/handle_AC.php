<?php


	db_queryf("update stats_users set account = '%s' where nick = '%s'", 
		$user->get_account_name(),
		$user->get_nick());


?>