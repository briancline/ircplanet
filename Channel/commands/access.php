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

	$req_wildcard = ( '*' == $chan_name );
	$is_admin = ( 0 < $user_admin_level );

	if( !($reg = $this->get_channel_reg($chan_name)) && 
			!($is_admin && $req_wildcard) ) 
	{
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	
	$n = 0;
	$users = array();
	$user_mask = $pargs[2];
	
	$channels = array();
	if( $req_wildcard && $is_admin )
	{
		// If an admin requests all channels, point to db_channels.
		$channels = $this->db_channels;
		$tmp_account = $this->get_account( $user_mask );

		if( !$tmp_account )
		{
			$bot->noticef( $user, 'There is no user account named %s.', $user_mask );
			return false;
		}

		$search_id = $tmp_account->get_id();
	}
	else
	{
		/**
		 * User didn't request a channel wildcard, so our only channel is
		 * the specific one they wanted.
		 */
		$channels = array( $reg );
	}
	
	foreach( $channels as $tmp_reg )
	{
		if( $req_wildcard && $is_admin && $tmp_reg->get_level_by_id($tmp_account->get_id()) == 0 )
			continue;

		foreach( $tmp_reg->get_levels() as $user_id => $access )
		{
			$tmpuser = $this->get_account_by_id( $user_id );
			
			if(!$tmpuser)
				continue;
			
			$tmpname = $tmpuser->get_name();
			
			if( strtolower($tmpname) == strtolower($user_mask) || fnmatch($user_mask, $tmpname) )
			{
				$users[] = $access;
				$n++;
			}
		}
	}

	if( count($users) > 0 )
	{
		$user_num = 0;
		for( $i = 500; $i > 0; $i-- )
		{
			foreach( $users as $access )
			{
				$level = $access->get_level();
				if( $level == $i )
				{
					$tmpuser = $this->get_account_by_id( $access->get_user_id() );
					$last_ts = $tmpuser->get_lastseen_ts();

					if( $req_wildcard && $is_admin )
					{
						$tmp_reg = $this->get_channel_reg_by_id( $access->get_chan_id() );

						$bot->noticef( $user, '%3d) Channel: %s%s%s', 
							++$user_num, BOLD_START, $tmp_reg->get_name(), BOLD_END );
						$bot->noticef( $user, '     User:    %s%-20s%s     Level: %s%3d%s', 
							BOLD_START, $tmpuser->get_name(), BOLD_END,
							BOLD_START, $level, BOLD_END );
					}
					else
					{
						$bot->noticef( $user, '%3d) User:  %s%-20s%s     Level: %s%3d%s', 
							++$user_num,
							BOLD_START, $tmpuser->get_name(), BOLD_END,
							BOLD_START, $level, BOLD_END );
					}

					$bot->noticef( $user, '     Auto-op: %-3s   Auto-voice: %-3s   Protect: %-3s', 
						$access->auto_ops() ? 'ON' : 'OFF',
						$access->auto_voices() ? 'ON' : 'OFF',
						$access->is_protected() ? 'ON' : 'OFF' );
					$bot->noticef( $user, '     Last login: %s',
						date('D j M Y H:i:s', $last_ts) );
					$bot->notice( $user, ' ' );
				}
			}
		}
	}
		
	$bot->noticef( $user, 'Found %d records matching your search.', $n );
	

