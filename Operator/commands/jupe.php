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

	$server = $pargs[1];
	$duration = $pargs[2];
	$last_mod = time();
	$reason = assemble( $pargs, 3 );

	if( $jupe = $this->get_jupe($server) )
	{
		$bot->noticef( $user, 'A jupe already exists for %s.', $jupe->get_server() );
		return false;
	}
	
	if( !($duration_secs = convert_duration($duration)) )
	{
		$bot->notice( $user, 'Invalid duration specified! See help for more details.' );
		return false;
	}
	
	$max_ts = 2147483647;
	$expire_ts = time() + $duration_secs;
	
	if( $expire_ts > $max_ts || $expire_ts < 0 )
	{
		$bot->noticef( $user, 'The duration you specified is too large. Please try something more sensible.' );
		return false;
	}
	
	$jupe = $this->add_jupe( $server, $duration_secs, $last_mod, $reason );
	
	$this->sendf( FMT_JUPE_ACTIVE, SERVER_NUM, $jupe->get_server(), $duration_secs, 
			$jupe->get_last_mod(), $jupe->get_reason() );
	

