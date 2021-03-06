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
	
	if ($cmd_num_args == 1) {
		$user_name = $user->getNick();
		$password = $pargs[1];
	}
	else {
		$user_name = $pargs[1];
		$password = $pargs[2];
	}
	
	if ($account = $this->getAccount($user_name)) {
		$password_md5 = md5($password);
		
		if ($account->getPassword() != $password_md5) {
			$bot->notice($user, "Invalid password!");
			return false;
		}
		elseif ($account->isSuspended()) {
			$bot->noticef($user, "Your account is suspended.");
			return false;
		}
		elseif ($user->isLoggedIn()) {
			$bot->notice($user, "You are already logged in as ". $user->getAccountName() ."!");
			return false;
		}

		$user_name = $account->getName();
		$bot->notice($user, "Authentication successful as $user_name!");
		
		/**
		 * Always send the AC token last as it will activate the default hidden host
		 * unless a fakehost is already set.
		 */
		if ($account->hasFakehost()) {
			$this->sendf(FMT_FAKEHOST, SERVER_NUM, $user->getNumeric(), $account->getFakehost());
			
			if (!$user->hasMode(UMODE_HIDDENHOST)) {
				$bot->noticef($user, 'Enable user mode +x (/mode %s +x) in order to cloak your host.',
					$user->getNick());
			}
		}

		$this->sendf(FMT_ACCOUNT, SERVER_NUM, $user->getNumeric(), $user_name, $account->getRegisterTs());
		$user->setAccountName($user_name);
		$user->setAccountId($account->getId());
	}
	else {
		$bot->notice($user, "No such account!");
	}


