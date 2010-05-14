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

	$chan_name = $pargs[1];
	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, 'Nobody is on channel %s.', $chan_name );
		return false;
	}
	
	$chan_users = array();
	$chan_nums = $chan->get_user_list();
	
	foreach( $chan_nums as $numeric )
	{
		$tmp_user = $this->get_user( $numeric );
		$tmp_nick = $tmp_user->get_nick();
		$tmp_key = strtolower( $tmp_nick );
		$flags = '';
		
		if( $chan->is_voice($numeric) )
			$flags .= '+';
		if( $chan->is_op($numeric) )
			$flags .= '@';
		
		$chan_users[$tmp_key] = $flags . $tmp_nick;
	}
	
	ksort( $chan_users );
	
	$max_line_users = 8;
	$line_users = 0;
	$user_num = 0;
	$line = '';
	
	foreach( $chan_users as $key => $nick )
	{
		$line .= $nick .' ';
		$line_users++;
		$user_num++;
		
		if( $line_users == $max_line_users || $user_num == count($chan_users) )
		{
			$bot->noticef( $user, '   %s', $line );
			$line_users = 0;
			$line = '';
		}
	}
	

