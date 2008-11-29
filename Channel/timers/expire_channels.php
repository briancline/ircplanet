<?php
	
	if( !defined('MAX_CHAN_AGE') || MAX_CHAN_AGE == 0 )
		return;
	
	
	foreach($this->db_channels as $chan_key => $reg)
	{
		if($reg->is_permanent())
			continue;
		
		$youngest_ts = 0;
		foreach($reg->get_levels() as $user_id => $level)
		{
			$user = $this->get_account_by_id($user_id);

			if( !$user )
			{
				debugf( 'Found an orphaned access record for user ID %d in %s, deleting',
					$user_id, $reg->get_name(), $reg->remove_access( $user_id ) );
				$level->delete();
				continue;
			}
			
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
			
			if( ($chan = $this->get_channel($chan_key)) && $chan->is_on($bot->get_numeric()) )
			{
				$this->sendf( FMT_PART_REASON, $bot->get_numeric(), $chan->get_name(), $reason );
				$this->remove_channel_user( $chan->get_name(), $bot->get_numeric() );
			}
		}
	}
	

?>
