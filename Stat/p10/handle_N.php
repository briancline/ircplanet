<?php
	
	
	if($nick_change)
	{
		db_queryf("update stats_users set nick = '%s' where nick = '%s'", $new_nick, $old_nick);
		db_queryf("update stats_channel_users set nick = '%s' where nick = '%s'", $new_nick, $old_nick);
	}
	else 
	{
		$server = $this->get_server($user->get_server_numeric());
		
		db_queryf("insert into stats_users 
			(nick, ident, host, name, server, modes, account, signon_date)
			values
			('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
			$user->get_nick(),
			$user->get_ident(),
			$user->get_host(),
			$user->get_name(),
			$server->get_name(),
			$user->get_modes(),
			$user->get_account_name(),
			db_date($user->get_signon_ts())
		);
	}

?>