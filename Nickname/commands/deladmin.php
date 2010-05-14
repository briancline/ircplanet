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

	$new_uid = $pargs[1];

	if( $new_user = $this->get_account($new_uid) )
	{
		if( $new_user->get_id() == $user->get_account_id() )
		{
			$bot->noticef( $user, 'You cannot remove your own access!' );
			return false;
		}

		$current_level = $this->get_user_level( $new_user );

		if( $current_level <= 1 )
		{
			$bot->noticef( $user, '%s does not have administrator access.', 
				$new_user->get_name() );
			return false;
		}

		if( $current_level > $user_level )
		{
			$bot->noticef( $user, '%s has higher access than you and cannot be removed.', 
				$new_user->get_name() );
			return false;
		}

		db_queryf( "delete from ns_admins where user_id = '%d'",
			$new_user->get_id() );
		
		$bot->noticef( $user, '%s no longer has administrator access.',
			$new_user->get_name(), $new_level );
	}
	else
	{
		$bot->noticef( $user, 'Account %s does not exist.', $new_uid );
		return false;
	}
	

