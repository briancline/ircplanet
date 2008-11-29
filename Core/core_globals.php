<?php
/*
 * ircPlanet Services for ircu
 * Copyright (c) 2005 Brian Cline.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:

 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of ircPlanet nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without 
 *    specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

	define( 'CORE_VENDOR',         'ircPlanet' );
	define( 'CORE_NAME',           'Services Core' );
	define( 'CORE_VERSION_MAJOR',  1 );
	define( 'CORE_VERSION_MINOR',  1 );
	define( 'CORE_VERSION_REV',    8 );
	
	define( 'CORE_VERSION',        CORE_VENDOR .' '.
	                               CORE_NAME .' v'.
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

	define( 'NOTIFY_ACCOUNT',        'A'  );
	define( 'NOTIFY_CHANNEL',        'C'  );
	define( 'NOTIFY_CHANNEL_ACCESS', 'CA' );

	define( 'NOTIFY_LOAD',           'L'  );
	define( 'NOTIFY_RELOAD',         'R'  );
	define( 'NOTIFY_DELETE',         'D'  );


	function debug( $s )
	{
		$s .= "\n";
		echo( "[". date('D d M H:i:s Y') ."] $s" );
	}
	
	function debugf( $format )
	{
		$args = array();
		$format = addslashes($format);
		for($i = 1; $i < func_num_args(); ++$i)
			$args[] = addslashes(func_get_arg($i));
		
		$arglist = join("', '", $args);
		eval("\$s = sprintf('$format', '$arglist');");
		
		return debug( $s );
	}
	
	function print_array( $a )
	{
		print_r( $a );
	}
	
	
	require_once( CORE_DIR .'/uptime.php' );
	
?>
