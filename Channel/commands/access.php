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

	$requestedWildcard = ('*' == $chan_name);
	$userIsAdmin = (0 < $user_admin_level);

	if (!($reg = $this->getChannelReg($chan_name)) 
			&& !($userIsAdmin && $requestedWildcard))
	{
		$bot->noticef($user, '%s is not registered!', $chan_name);
		return false;
	}
	
	$n = 0;
	$users = array();
	$userMask = $pargs[2];
	
	$channels = array();
	if ($requestedWildcard && $userIsAdmin) {
		/** Admin user requested a wildcard search. **/
		$tmpAccount = $this->getAccount($userMask);

		if (!$tmpAccount) {
			$bot->noticef($user, 'There is no user account named %s.', $userMask);
			return false;
		}

		$channels = $this->db_channels;
		$searchId = $tmpAccount->getId();
	}
	elseif ($requestedWildcard) {
		/** Normal user requested a wildcard search. **/
		$tmpAccount = $this->getAccount($userMask);
		$userAccount = $this->getAccount($user->getAccountName());
		
		if (!$tmpAccount) {
			$bot->noticef($user, 'There is no user account named %s.', $userMask);
			return false;
		}
		elseif (!$userAccount || $tmpAccount->getId() != $userAccount->getId()) {
			$bot->noticef($user, 'You can only search multiple channels for your own access records.');
			return false;
		}
		
		$channels = $this->db_channels;
		$searchId = $tmpAccount->getId();
	}
	else {
		/**
		 * User didn't request a channel wildcard, so our only channel is
		 * the specific one they wanted.
		 */
		$channels = array($reg);
	}
	
	foreach ($channels as $tmpReg) {
		if ($requestedWildcard && $tmpReg->getLevelById($tmpAccount->getId()) == 0) {
			continue;
		}

		foreach ($tmpReg->getLevels() as $userId => $access) {
			$tmpuser = $this->getAccountById($userId);
			
			if (!$tmpuser) {
				continue;
			}
			
			$tmpname = $tmpuser->getName();
			
			if (strtolower($tmpname) == strtolower($userMask) || fnmatch($userMask, $tmpname)) {
				$users[] = $access;
				$n++;
			}
		}
	}

	if (count($users) > 0) {
		$userNum = 0;
		
		for ($i = 500; $i > 0; $i--) {
			foreach ($users as $access) {
				$level = $access->getLevel();
				
				if ($level == $i) {
					$tmpuser = $this->getAccountById($access->getUserId());
					$last_ts = $tmpuser->getLastseenTs();

					if ($requestedWildcard) {
						$tmpReg = $this->getChannelRegById($access->getChanId());

						$bot->noticef($user, '%3d) Channel: %s%s%s', 
							++$userNum, BOLD_START, $tmpReg->getName(), BOLD_END);
						$bot->noticef($user, '     User:    %s%-20s%s     Level: %s%3d%s', 
							BOLD_START, $tmpuser->getName(), BOLD_END,
							BOLD_START, $level, BOLD_END);
					}
					else {
						$bot->noticef($user, '%3d) User:  %s%-20s%s     Level: %s%3d%s', 
							++$userNum,
							BOLD_START, $tmpuser->getName(), BOLD_END,
							BOLD_START, $level, BOLD_END);
					}

					$bot->noticef($user, '     Auto-op: %-3s   Auto-voice: %-3s   Protect: %-3s', 
						$access->autoOps() ? 'ON' : 'OFF',
						$access->autoVoices() ? 'ON' : 'OFF',
						$access->isProtected() ? 'ON' : 'OFF');
					$bot->noticef($user, '     Last login: %s',
						date('D j M Y H:i:s', $last_ts));
					$bot->notice($user, ' ');
				}
			}
		}
	}
	
	$bot->noticef($user, 'Found %d records matching your search.', $n);
