<?php
	
	define( 'SERVICE_NAME',           'Operator Service' );
	define( 'SERVICE_VERSION_MAJOR',  1 );
	define( 'SERVICE_VERSION_MINOR',  1 );
	define( 'SERVICE_VERSION_REV',    4 );
	
	define( 'SERVICE_DIR',            dirname(__FILE__) );
	define( 'SERVICE_CONFIG_FILE',    SERVICE_DIR .'/os.ini' );
	define( 'SERVICE_HANDLER_DIR',    SERVICE_DIR .'/p10/' );
	define( 'SERVICE_TIMER_DIR',      SERVICE_DIR .'/timers/' );
	define( 'CMD_HANDLER_DIR',        SERVICE_DIR .'/commands/' );
	
	define( 'TOR_HOSTS_FILE',         SERVICE_DIR .'/tor-hosts.txt' );
	
?>
