<?php

	$uplink = $args[0];
	$name = $args[2];
	$start_ts = $args[5];
	$numeric = substr( $args[7], 0, BASE64_SERVLEN );
	$max_users = base64_to_int( substr($args[7], BASE64_SERVLEN) );
	$desc = $args[$num_args - 1];
	$modes = '';
	
	if( $args[$num_args - 2][0] == '+' )
		$modes = $args[$num_args - 2];
	
//	debug("this->add_server( $uplink, $numeric, $name, $desc, $start_ts, $max_users, $modes );");
	$this->add_server( $uplink, $numeric, $name, $desc, $start_ts, $max_users, $modes );
	
?>