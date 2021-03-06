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

	if (!($reg = $this->getChannelReg($chan_name))) {
		$bot->noticef($user, '%s is not registered!', $chan_name);
		return false;
	}
	
	$new_uid = $pargs[2];
	
	if ($new_user = $this->getAccount($new_uid)) {
		$level = 100;
		if ($cmd_num_args > 2)
			$level = $pargs[3];
		
		if ($level < 1 || $level > 500) {
			$bot->notice($user, 'Level must range from 1 to 500.');
			return false;
		}
		
		if ($level >= $user_channel_level) {
			$bot->noticef($user, 'You cannot add someone with access equal to or higher than your own (%s).',
				$user_channel_level);
			return false;
		}

		$existing_level = $this->getChannelLevelByName($chan_name, $new_uid);
		if ($existing_level > 0) {
			$bot->noticef($user, '%s already has level %d access on %s.',
				$new_user->getName(), $existing_level, $reg->getName());
			return false;
		}
		
		$new_access = new DB_Channel_Access();
		$new_access->setChanId($reg->getId());
		$new_access->setUserId($new_user->getId());
		$new_access->setLevel($level);
		$new_access->save();
		$reg->addAccess($new_access);
		
		$bot->noticef($user, '%s has been added to the %s access list at level %d.',
			$new_user->getName(), $reg->getName(), $level);
		
		foreach ($this->users as $numeric => $tmp_user) {
			if ($tmp_user->isLoggedIn() 
					&& $tmp_user->getAccountId() == $new_user->getId()
					&& $tmp_user->getNick() != $user->getNick())
			{
				$bot->noticef($tmp_user, '%s has given you level %d access on %s.',
					$user->getNick(), $level, $reg->getName());
			}
		}
	}
	else {
		$bot->noticef($user, 'Account %s does not exist.', $new_uid);
		return false;
	}
	

