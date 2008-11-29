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

	$uplink = SERVER_NUM;
	$name = $args[1];
	$start_ts = $args[4];
	$numeric = substr( $args[6], 0, BASE64_SERVLEN );
	$max_users = base64_to_int( substr($args[6], BASE64_SERVLEN) );
	$desc = $args[$num_args - 1];
	$modes = '';
	
	if( $args[$num_args - 2][0] == '+' )
		$modes = $args[$num_args - 2];
	
	$server = $this->add_server( $uplink, $numeric, $name, $desc, $start_ts, $max_users, $modes );

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
