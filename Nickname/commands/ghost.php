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
	
	if( $user->is_logged_in() )
	{
		$user_name = $user->get_account_name();
		$account = $this->get_account( $user_name );
		$password_md5 = $account->get_password();
	}
	else if( $cmd_num_args < 2 )
	{
		$bot->noticef( $user, "%sSyntax:%s %s %s",
			BOLD_START, BOLD_END, strtolower($pargs[0]),
			$this->get_command_syntax($pargs[0]) );
		return false;
	}
	else
	{
		$user_name = $pargs[1];
		$password_md5 = md5( $pargs[2] );
	}
	
	
	if( strtolower($user_name) == strtolower($user->get_nick()) )
	{
		$bot->notice( $user, "Suicide is not the answer!" );
		return false;
	}
	
	if( !($account = $this->get_account($user_name)) )
	{
		$bot->noticef( $user, "%s is not a registered nick.", $user_name );
		return false;
	}
	
	if( $user->is_logged_in() && $account->get_id() != $user->get_account_id() )
	{
		$bot->notice( $user, "You cannot ghost someone else's nick!" );
		return false;
	}
	
	if( !($target = $this->get_user_by_nick($user_name)) )
	{
		$bot->notice( $user, "No one is using that nick." );
		return false;
	}
	
	$target_nick = $target->get_nick();
	
	if( $account->get_password() != $password_md5 )
	{
		$bot->notice( $user, "Invalid password!" );
		return false;
	}
	
	$user_name = $account->get_name();
	$this->kill( $target->get_numeric(), "GHOST command used by ". $user->get_nick() );
	
	if( $user->is_logged_in() )
	{
		$bot->noticef( $user, "%s has been disconnected.", $target_nick );
	}
	else 
	{
		$bot->noticef( $user, "%s has been disconnected. You are now logged in.", $target_nick );
		$this->sendf( FMT_ACCOUNT, SERVER_NUM, $user->get_numeric(), $user_name, $account->get_register_ts() );
		$user->set_account_name( $user_name );
		$user->set_account_id( $account->get_id() );
		
		if( $account->has_fakehost() )
		{
			$this->sendf( FMT_FAKEHOST, SERVER_NUM, $user->get_numeric(), $account->get_fakehost() );
			
			if( !$user->has_mode(UMODE_HIDDENHOST) ) {
				$bot->noticef( $user, 'Enable usermode +x (/mode %s +x) in order to cloak your host.',
					$user->get_nick() );
			}
		}
	}
	

