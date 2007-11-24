<?php
	
	if($parted_all_chans)
	{
		db_queryf("delete from stats_channel_users where nick = '%s'", $user->get_nick());
	}
	else 
	{
		foreach($channels as $chan_name)
		{
			$chan = $this->get_channel($chan_name);
			
			db_queryf("insert into stats_channel_users (channel_name, nick, is_op, is_voice) values 
				('%s', '%s', '%s', '%s')",
				$chan->get_name(),
				$user->get_nick(),
				$chan->is_op($numeric),
				$chan->is_voice($numeric)
			);
		}
	}

?>