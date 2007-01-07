<?php

	define( 'CORE_NAME',           'Services Core' );
	define( 'CORE_VERSION_MAJOR',  1 );
	define( 'CORE_VERSION_MINOR',  0 );
	define( 'CORE_VERSION_REV',    10 );
	
	define( 'CORE_VERSION',        CORE_NAME .' v'.
	                               CORE_VERSION_MAJOR .'.'.
	                               CORE_VERSION_MINOR .'.'.
	                               CORE_VERSION_REV );

	define( 'CORE_DIR',            dirname(__FILE__) );
	define( 'P10_DIR',             CORE_DIR .'/p10/' );
	define( 'CORE_TIMER_DIR',      CORE_DIR .'/timers/' );
	
	define( 'NICKLENGTH',          15 );
	
	define( 'SOCKET_TIMEOUT',       5 );
	
	define( 'ACTION_CHAR',         chr(1) );
	define( 'ACTION_START',        ACTION_CHAR );
	define( 'ACTION_END',          ACTION_CHAR );
	
	define( 'CTCP_CHAR',           chr(1) );
	define( 'CTCP_START',          CTCP_CHAR );
	define( 'CTCP_END',            CTCP_CHAR );
	
	define( 'BOLD_CHAR',           chr(2) );
	define( 'BOLD_START',          BOLD_CHAR );
	define( 'BOLD_END',            BOLD_CHAR );
	
	
	function debug( $s )
	{
		$s .= "\n";
		echo( "[". date('D d M H:i:s Y') ."] $s" );
	}
	
	function print_array( $a )
	{
		print_r( $a );
	}
	
	
	require_once( CORE_DIR .'/uptime.php' );
	
?>
