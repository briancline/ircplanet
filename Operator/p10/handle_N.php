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
	
	$is_new_user = ($num_args > 4);
	
	if( $is_new_user )
	{
		$gline_host = $user->get_gline_host();
		$gline_ip = $user->get_gline_ip();
		$gline_set = false;
		
		foreach( $this->glines as $gline_key => $gline )
		{
			if( !$gline->is_expired() && ($gline->matches($gline_host) || $gline->matches($gline_ip)) )
			{
				$this->enforce_gline( $gline );
				$gline_set = true;
			}
		}
		
		if( defined('CLONE_GLINE') && CLONE_GLINE == true && !$gline_set
				&& $this->get_clone_count($user->get_ip()) > CLONE_MAX
				&& !is_private_ip($user->get_ip()) )
		{
			$gline_mask = '*@'. $user->get_ip();
			$gline_secs = convert_duration( CLONE_DURATION );
			$new_gl = $this->add_gline( $gline_mask, $gline_secs, CLONE_REASON );
			$this->enforce_gline( $new_gl );
			$gline_set = true;
		}
		
		if( defined('TOR_GLINE') && TOR_GLINE == true && !$gline_set 
				&& $this->is_tor_host($user->get_ip()) )
		{
			$gline_mask = '*@'. $user->get_ip();
			$gline_secs = convert_duration( TOR_DURATION );
			$new_gl = $this->add_gline( $gline_mask, $gline_secs, TOR_REASON );
			$this->enforce_gline( $new_gl );
			$gline_set = true;
		}
		
		if( defined('COMP_GLINE') && COMP_GLINE == true && !$gline_set
				&& $this->is_compromised_host($user->get_ip()) )
		{
			$gline_mask = '*@'. $user->get_ip();
			$gline_secs = convert_duration( COMP_DURATION );
			$new_gl = $this->add_gline( $gline_mask, $gline_secs, COMP_REASON );
			$this->enforce_gline( $new_gl );
			$gline_set = true;
		}
	}

	// Don't log new users during the initial burst, as it could flood the log channel.
	if( $this->finished_burst )
	{
		if( $is_new_user )
		{
			// new user
			$server = $this->get_server( $args[0] );
			$rest = irc_sprintf( '%H (%s@%s) [%s]', $user, $user->get_ident(), 
				$user->get_host(), $user->get_name() );
			$this->report_event( 'NICK', $server, $rest );
		}
		else
		{
			// nick change
			$this->report_event( 'NICK', $old_nick, $new_nick );
		}
	}

?>
