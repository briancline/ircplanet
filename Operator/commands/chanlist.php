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
	
	$mask = '*';
	$count = 0;
	$matches = array();

	if( $cmd_num_args >= 1 )
		$mask = strtolower( $pargs[1] );
	
	$max_name_len = 12;
	$max_mode_len = 5;
	$max_count_len = 5;
	
	foreach( $this->channels as $chan_key => $chan )
	{
		if( fnmatch($mask, $chan_key) )
		{
			$name = $chan->get_name();
			$name_len = strlen( $name );
			$mode = '+'. $chan->get_modes();
			$mode_len = strlen( $mode );
			$count = $chan->get_user_count();
			$count_len = strlen( $count );
			
			if( $max_name_len < $name_len )
				$max_name_len = $name_len;
			if( $max_mode_len < $mode_len )
				$max_mode_len = $mode_len;
			if( $max_count_len < $count_len )
				$max_count_len = $count_len;
			
			$matches[] = array(
				'name' => $name,
				'mode' => $mode,
				'count' => $count
			);
		}
	}
	
	$h_format = "%-". $max_name_len ."s     %-". $max_mode_len ."s     %". $max_count_len ."s";
	$format = "%-". $max_name_len ."s     %-". $max_mode_len ."s     %". $max_count_len ."d";
	
	$bot->noticef( $user, $h_format, 'CHANNEL NAME', 'MODES', 'USERS' );
	$bot->noticef( $user, str_repeat('-', $max_name_len + $max_mode_len + $max_count_len + 10) );
	
	foreach( $matches as $match )
		$bot->noticef( $user, $format, $match['name'], $match['mode'], $match['count'] );
	
	$bot->noticef( $user, '%d matches found.', count($matches) );
	

