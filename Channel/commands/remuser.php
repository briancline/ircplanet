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

	if( !($reg = $this->get_channel_reg($chan_name)) ) {
		$bot->noticef( $user, '%s is not registered!', $chan_name );
		return false;
	}
	
	$rem_uid = $pargs[2];
	
	if( $rem_user = $this->get_account($rem_uid) )
	{
		if( $this->get_channel_level_by_name($chan_name, $rem_uid) == 0 )
		{
			$bot->noticef( $user, '%s is not in the %s access list.',
				$rem_user->get_name(), $reg->get_name() );
			return false;
		}
		
		$reg->remove_access( $rem_user->get_id() );
		$reg->save();
		
		$bot->noticef( $user, '%s has been removed from the %s access list.',
			$rem_user->get_name(), $reg->get_name() );
		
		foreach( $this->users as $numeric => $tmp_user )
		{
			if( $tmp_user->is_logged_in() && $tmp_user->get_account_id() == $rem_user->get_id() && 
				$tmp_user->get_nick() != $user->get_nick() )
			{
				$bot->noticef( $tmp_user, '%s has removed your access on %s.',
					$user->get_nick(), $reg->get_name() );
			}
		}
	}
	else
	{
		$bot->noticef( $user, 'Account %s does not exist.', $rem_uid );
		return false;
	}
	

