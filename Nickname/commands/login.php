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
	
	if($cmd_num_args == 1)
	{
		$user_name = $user->get_nick();
		$password = $pargs[1];
	}
	else
	{
		$user_name = $pargs[1];
		$password = $pargs[2];
	}
	
	if($account = $this->get_account($user_name))
	{
		$password_md5 = md5($password);
		
		if($account->get_password() != $password_md5)
		{
			$bot->notice($user, "Invalid password!");
			return false;
		}
		elseif($account->is_suspended())
		{
			$bot->noticef($user, "Your account is suspended.");
			return false;
		}
		elseif($user->is_logged_in())
		{
			$bot->notice($user, "You are already logged in as ". $user->get_account_name() ."!");
			return false;
		}

		$user_name = $account->get_name();
		$bot->notice($user, "Authentication successful as $user_name!");
		$this->sendf(FMT_ACCOUNT, SERVER_NUM, $user->get_numeric(), $user_name, $account->get_register_ts());
		$user->set_account_name($user_name);
		$user->set_account_id($account->get_id());
		
		if($account->has_fakehost())
		{
			$this->sendf(FMT_FAKEHOST, SERVER_NUM, $user->get_numeric(), $account->get_fakehost());
			
			if(!$user->has_mode(UMODE_HIDDENHOST)) {
				$bot->noticef($user, 'Enable user mode +x (/mode %s +x) in order to cloak your host.',
					$user->get_nick());
			}
		}
	}
	else
	{
		$bot->notice($user, "No such account!");
	}


