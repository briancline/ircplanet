<?php

	$numeric = $args[0];
	$flag = $args[2];
	$server_num = $args[3];
	
	if( $flag == 'u' )
	{
		$uptime = get_uptime_info();
		$this->sendf( FMT_STATS_U_REPLY, $server_num, $numeric, $uptime['stats'] );
	}
	
	$this->sendf( FMT_STATS_END, $server_num, $numeric, $flag );

?>