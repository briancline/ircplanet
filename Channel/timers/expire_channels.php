<?php
	
	/*
	$res = mysql_query("
		select
			c.id,
			c.name,
			u.id,
			u.name,
			($ts - u.lastseen_ts) as dead_time
		from
			channels c
		inner join channel_access l
			on l.chan_id = c.id
		inner join accounts u
			on u.id = l.user_id
		where
			l.level = 500
		order by
			dead_time desc
	");

	while($row = mysql_fetch_assoc($res))
	{
		extract($row);
		$dead_days = $dead_time / 86400;
		
	}
	*/
	
	if( !defined('MAX_CHAN_AGE') || MAX_CHAN_AGE == 0 )
		return;
	
	
	foreach($this->db_channels as $chan_key => $reg)
	{
		if($reg->is_permanent())
			continue;
		
		$youngest_ts = 0;
		foreach($reg->levels as $user_id => $level)
		{
			$user = $this->get_account_by_id($user_id);
			
			if($level->get_level() == 500 && $youngest_ts < $user->get_lastseen_ts())
				$youngest_ts = $user->get_lastseen_ts();
		}
		
		if($youngest_ts == 0)
			continue;
		
		$age_days = (time() - $youngest_ts) / 86400;
		
		if($age_days > MAX_CHAN_AGE)
		{
			debug("Channel ". $reg->get_name() ." age is $age_days days");
			
			$this->remove_channel_reg( $reg );
			$reg->delete();

			$reason = 'So long, and thanks for all the fish!';		
			if( $cmd_num_args > 1 )
				$reason = assemble( $pargs, 2 );
			
			if( ($chan = $this->get_channel($chan_key)) && $chan->is_on($bot->get_numeric()) )
			{
				$this->sendf( FMT_PART_REASON, $bot->get_numeric(), $chan->get_name(), $reason );
				$this->remove_channel_user( $chan_name, $bot->get_numeric() );
			}
		}
	}
	

?>