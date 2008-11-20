<?php

	db_queryf("insert into stats_servers (server_name, `desc`, start_date, max_users, is_service) ".
		"values ('%s', '%s', '%s', '%s', '%s')",
		$server->get_name(),
		$server->get_desc(),
		db_date($server->get_start_ts()),
		$server->get_max_users(),
		$server->is_service()
	);

?>