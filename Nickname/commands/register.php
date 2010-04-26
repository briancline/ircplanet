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
	
	$numeric = $args[0];
	$user = $this->get_user( $numeric );
	$user_name = $user->get_nick();
	$password = $pargs[1];
	$email = $pargs[2];
	
	if( !$user->is_logged_in() )
	{
		if( !is_valid_email($email) )
		{
			$bot->notice( $user, "You have specified an invalid e-mail address. ".
				"Please try again." );
			return false;
		}
		
		if( $account = $this->get_account_by_email($email) )
		{
			$bot->notice( $user, "That e-mail address is already associated ".
				"with a registered nickname." );
			return false;
		}
		
		if( $account = $this->get_account($user_name) )
		{
			$bot->noticef( $user,
				"The nickname %s%s%s has already been registered. Please choose another.",
				BOLD_START, $user_name, BOLD_END );
			return false;
		}

		if( $this->is_badnick($user_name) )
		{
			$bot->noticef( $user, 'You are not allowed to register that nickname.' );
			return false;
		}
		
		$password_md5 = md5( $password );
		
		$account = new DB_User();
		$account->set_name( $user->get_nick() );
		$account->set_register_ts( time() );
		$account->set_password( $password_md5 );
		$account->set_email( $email );
		$account->set_auto_op( true );
		$account->set_auto_voice( true );
		$account->update_lastseen();
		$account->save();
		
		$this->add_account( $account );
		
		if( !$user->has_account_name() )
		{
			$this->sendf( FMT_ACCOUNT, SERVER_NUM, $numeric, $user_name );
			$user->set_account_name( $user_name );
			$user->set_account_id( $account->get_id() );
		}
		
		$bot->noticef( $user,
			"Your account, %s%s%s, has been registered. You are now logged in.",
			BOLD_START, $user_name, BOLD_END );
	}
	else
	{
		$bot->notice( $user, "You have already registered your nick and logged in." );
	}

?>
