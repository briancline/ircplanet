<?php

/*	Logging bursts on a larger network can flood the channel. Enable at your own risk...
	
	$chan = $this->get_channel( $chan_name );
	$server = $this->get_server( $args[0] );
	$modes = '';
	
	$this->report_event( 'BURST', $server, $chan, "[+". $chan->get_modes() ."]", "$user_count users, $ban_count bans" );
*/
	db_queryf("delete from stats_channels where channel_name = '%s'", $chan->get_name());
	
	db_queryf("insert into stats_channels (channel_name, topic, modes) values ('%s', '%s', '%s')",
		$chan->get_name(),
		$chan->get_topic(),
		$chan->get_modes()
	);
	
	foreach($chan->get_user_list() as $numeric)
	{
		$user = $this->get_user($numeric);
		
		db_queryf("insert into stats_channel_users (channel_name, nick, is_op, is_voice) values 
			('%s', '%s', '%s', '%s')",
			$chan->get_name(),
			$user->get_nick(),
			$chan->is_op($numeric),
			$chan->is_voice($numeric)
		);
	}

?>