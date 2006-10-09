<?php

	$uplink = SERVER_NUM;
	$name = $args[1];
	$start_ts = $args[4];
	$numeric = substr( $args[6], 0, BASE64_SERVLEN );
	$max_users = base64_to_int( substr($args[6], BASE64_SERVLEN) );
	$desc = $args[$num_args - 1];
	$modes = '';
	
	if( $args[$num_args - 2][0] == '+' )
		$modes = $args[$num_args - 2];
	
	$this->add_server( $uplink, $numeric, $name, $desc, $start_ts, $max_users, $modes );

	if( !defined('UPLINK_NUM') )
	{
		define( 'UPLINK_NUM', $numeric );
	}
	else
	{
		debug( "*** FATAL ERROR :: Received a second uplink... I'm confused!" );
		exit();
	}
	
	$this->service_preburst();
	$this->burst_glines();
	$this->burst_servers();	
	$this->burst_users();
	$this->burst_channels();
	$this->sendf( FMT_ENDOFBURST, SERVER_NUM );
	
?>