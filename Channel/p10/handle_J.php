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

	$user = $this->get_user( $args[0] );
	$reg = $this->get_channel_reg( $chan_name );
	$opped = $voiced = $kicked = false;
	$infoline = '';
	
	if( $reg )
	{
		if( ($ban = $reg->get_matching_ban($user->get_full_mask())) )
		{
			$reason = $ban->get_reason();
			if( empty($reason) )
				$reason = 'Banned';
			
			$bot->ban( $chan_name, $ban->get_mask() );
			$bot->kick( $chan_name, $numeric, $reason );
			$kicked = true;
		}

		if( !$kicked && $reg->auto_limits() && !$reg->has_pending_autolimit() )
		{
			$this->add_timer( false, $reg->get_auto_limit_wait(), 'auto_limit.php', $chan_name );
			$reg->set_pending_autolimit( true );
		}

		if( !$kicked )
		{
			if( $reg->auto_ops_all() )
				$bot->op( $chan_name, $user->get_numeric() );
			else if( $reg->auto_voices_all() )
				$bot->voice( $chan_name, $user->get_numeric() );
		}
	}
	
	if( $user->is_logged_in() && ($account = $this->get_account($user->get_account_name())) && $reg && !$kicked )
	{
		if(	($lev = $this->get_channel_access($chan_name, $user)) )
		{
			$level = $lev->get_level();
			if( $level >= 100 && $account->auto_ops() && $reg->auto_ops() && $lev->auto_ops() )
			{
				$bot->op( $chan_name, $user->get_numeric() );
				$opped = true;
			}
			else if( $level >= 1 && $account->auto_voices() && $reg->auto_voices() && $lev->auto_voices() )
			{
				$bot->voice( $chan_name, $user->get_numeric() );
				$voiced = true;
			}
			
			if( $account->has_info_line() && $reg->shows_info_lines() )
				$infoline = sprintf( '[%s] %s', $account->get_name(), $account->get_info_line() );
		}
		
		if( !empty($infoline) )
			$bot->message( $chan_name, $infoline );
	}

?>
