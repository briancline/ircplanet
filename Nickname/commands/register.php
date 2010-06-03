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
	$user = $this->getUser($numeric);
	$user_name = $user->getNick();
	$password = $pargs[1];
	$email = $pargs[2];
	
	if (!$user->isLoggedIn()) {
		if (!isValidEmail($email)) {
			$bot->notice($user, "You have specified an invalid e-mail address. ".
				"Please try again.");
			return false;
		}
		
		if ($account = $this->getAccountByEmail($email)) {
			$bot->notice($user, "That e-mail address is already associated ".
				"with a registered nickname.");
			return false;
		}
		
		if ($account = $this->getAccount($user_name)) {
			$bot->noticef($user,
				"The nickname %s%s%s has already been registered. Please choose another.",
				BOLD_START, $user_name, BOLD_END);
			return false;
		}

		if ($this->isBadnick($user_name)) {
			$bot->noticef($user, 'You are not allowed to register that nickname.');
			return false;
		}
		
		$password_md5 = md5($password);
		
		$account = new DB_User();
		$account->setName($user->getNick());
		$account->setRegisterTs(time());
		$account->setPassword($password_md5);
		$account->setEmail($email);
		$account->setAutoOp(true);
		$account->setAutoVoice(true);
		$account->updateLastseen();
		$account->save();
		
		$this->addAccount($account);
		
		if (!$user->hasAccountName()) {
			$this->sendf(FMT_ACCOUNT, SERVER_NUM, $numeric, $user_name, $account->getRegisterTs());
			$user->setAccountName($user_name);
			$user->setAccountId($account->getId());
		}
		
		$bot->noticef($user,
			"Your account, %s%s%s, has been registered. You are now logged in.",
			BOLD_START, $user_name, BOLD_END);
	}
	else {
		$bot->notice($user, "You have already registered your nick and logged in.");
	}


