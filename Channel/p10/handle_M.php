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
	
	$source = $args[0];
	$target = $args[2];
	$target_is_chan = ( $target[0] == '#' );
	
	if( strlen($source) == 5 )
		$source = $this->get_user( $source );
	
	if( is_user($source) && !$source->is_service() && $target_is_chan && $this->is_chan_registered($target) )
	{
		$bans_to_check = array();
		$users_to_check = array();
		$users_to_reop = array();
		$deop_source = false;
		$mode_sub = false;
		$arg_index = 4;
		
		for( $i = 0; $i < strlen($modes); $i++ )
		{
			$mode = $modes[$i];
			
			if( $mode == 'o' || $mode == 'v' || $mode == 'b' || $mode == 'l' || $mode == 'k' )
				$arg_index++;
			
			if( $mode == '-' )
				$mode_sub = true;
			
			if( !$mode_sub && $mode == 'b' )
				$bans_to_check[] = $args[$arg_index];
				
			if( $mode_sub && ($mode == 'o' || $mode == 'v') )
				$users_to_check[] = $this->get_user( $args[$arg_index] );
		}
		
		$source_access = $this->get_channel_access( $target, $source );
		$act_users = $this->get_active_channel_users( $target );
		
		foreach( $act_users as $tmp_user )
		{
			$tmp_access = $this->get_channel_access( $target, $tmp_user );

			foreach( $bans_to_check as $tmp_mask )
			{
				if( fnmatch($tmp_mask, $tmp_user->get_full_mask()) 
					|| fnmatch($tmp_mask, $tmp_user->get_full_ip_mask()) )
				{
					$deop_source = true;
					$bot->unban( $target, $tmp_mask );
				}
			}
		}
		
		
		foreach( $users_to_check as $tmp_target )
		{
			if( !is_user($tmp_target) || !$tmp_user->is_logged_in() )
				continue;
			
			$tmp_access = $this->get_channel_access( $target, $tmp_target );
			if( $tmp_access == false )
				continue;
			
			if( $tmp_access->is_protected() && 
				(!$source_access || $source_access->get_level() <= $tmp_target->get_level()) )
			{
				$users_to_reop[] = $tmp_target;
				$deop_source = true;
			}
		}
		
		if( !empty($users_to_reop) )
		{
			$mode_buf = '';
			$mode_arg_buf = '';
			
			if( $deop_source )
			{
				$mode_buf = '-ov';
				$mode_arg_buf = $source->get_numeric() .' ';
			}
			
			$mode_buf .= '+';
			foreach( $users_to_reop as $tmp_user )
			{
				$mode_buf .= 'o';
				$mode_arg_buf .= $tmp_user->get_numeric() .' ';
			}
			
			$mode_change = $mode_buf .' '. $mode_arg_buf;
			
			$this->default_bot->mode( $target, $full_mode_change )
		}
	}

?>