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
	$flags = strtolower( $pargs[2] );
	
	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, 'Nobody is on channel %s.', $chan_name );
		return false;
	}
	
	$clear_modes = $kick_users = $deop_users = false;
	$devoice_users = $clear_bans = $gline_users = false;
	
	for( $c = 0; $c < strlen($flags); $c++ )
	{
		switch( $flags[$c] )
		{
			case 'm':
				$clear_modes = true;
				break;
			case 'k':
				$kick_users = true;
				break;
			case 'o':
				$deop_users = true;
				break;
			case 'v':
				$devoice_users = true;
				break;
			case 'b':
				$clear_bans = true;
				break;
			case 'g':
				$gline_users = true;
				break;
		}
	}
	
	if( $deop_users )
		$this->deop( $chan->get_name(), $chan->get_op_list() );
	
	if( $devoice_users )
		$this->devoice( $chan->get_name(), $chan->get_voice_list() );
	
	if( $clear_bans )
		$this->unban( $chan->get_name(), $chan->get_matching_bans() );
	
	if( $clear_modes )
		$this->clear_modes( $chan->get_name() );
	
	if( $gline_users )
	{
		$users = $chan->get_user_list();
		$gline_duration = '1h';
		
		if( $cmd_num_args >= 3 && convert_duration($pargs[3]) !== false )
			$gline_duration = $pargs[3];
		
		$gline_duration = convert_duration( $gline_duration );
		
		foreach( $users as $numeric )
		{
			$tmp_user = $this->get_user( $numeric );
			if( $tmp_user != $user && !$tmp_user->is_bot() )
			{
				$gline = $this->add_gline( $tmp_user->get_gline_mask(), $gline_duration, 
					"Channel g-line for ". $chan->get_name() );
				$this->enforce_gline( $gline );
			}
		}
	}
	
	if( $kick_users )
	{
		$users = $chan->get_user_list();
		
		foreach( $users as $numeric )
		{
			$tmp_user = $this->get_user( $numeric );
			if( $tmp_user != $user && !$tmp_user->is_bot() )
				$this->kick( $chan->get_name(), $numeric,
					"Clearing channel ". $chan->get_name() );
		}
	}
	
?>
