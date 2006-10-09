<?php

	function get_uptime_info()
	{
		$info = array();
		$secs = time() - START_TIME;
		$pretty_uptime = '';
		$mins = 0;
		$hours = 0;
		$days = 0;
		
		while( $secs >= 86400 )
		{
			$secs -= 86400;
			$days++;
		}
		while( $secs >= 3600 )
		{
			$secs -= 3600;
			$hours++;
		}
		while( $secs >= 60 )
		{
			$secs -= 60;
			$mins++;
		}
		

		if( $days > 0 )
		{
			$pretty_uptime .= $days .' day';
			if( $days > 1 )
				$pretty_uptime .= 's';
		}
		if( $hours > 0 )
		{
			if( !empty($pretty_uptime) )
				$pretty_uptime .= ', ';
			
			$pretty_uptime .= $hours .' hour';
			if( $hours > 1 )
				$pretty_uptime .= 's';
		}
		if( $mins > 0 )
		{
			if( !empty($pretty_uptime) )
				$pretty_uptime .= ', ';
			
			$pretty_uptime .= $mins .' min';
			if( $mins > 1 )
				$pretty_uptime .= 's';
		}
		if( $secs > 0 )
		{
			if( !empty($pretty_uptime) )
				$pretty_uptime .= ', ';
			
			$pretty_uptime .= $secs .' sec';
			if( $secs > 1 )
				$pretty_uptime .= 's';
		}
		
		$short_uptime = sprintf( "%d days, %d:%02d:%02d", $days, $hours, $mins, $secs );
		
		$info = array(
			'days' => $days,
			'hours' => $hours,
			'mins' => $mins,
			'secs' => $secs,
			'pretty' => $pretty_uptime,
			'stats' => $short_uptime );
		
		return $info;
	}

?>