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

	$user = $this->getUser($args[0]);
	$reg = $this->getChannelReg($chan_name);
	$opped = $voiced = $kicked = false;
	$infoline = '';
	
	if ($reg) {
		if (($ban = $reg->getMatchingBan($user->getFullMask()))) {
			$reason = $ban->getReason();
			if (empty($reason))
				$reason = 'Banned';
			
			$bot->ban($chan_name, $ban->getMask());
			$bot->kick($chan_name, $numeric, $reason);
			$kicked = true;
		}

		if (!$kicked && $reg->autoLimits() && !$reg->hasPendingAutolimit()) {
			$this->addTimer(false, $reg->getAutoLimitWait(), 'auto_limit.php', $chan_name);
			$reg->setPendingAutolimit(true);
		}

		if (!$kicked) {
			if ($reg->autoOpsAll())
				$bot->op($chan_name, $user->getNumeric());
			elseif ($reg->autoVoicesAll())
				$bot->voice($chan_name, $user->getNumeric());
			
			$reg->setLastActivityTime(time());
			if (time() - $reg->getLastAutoTopicTime() >= (30 * 60)) {
				$bot->topic($chan_name, $reg->getDefaultTopic());
				$reg->setLastTopic($reg->getDefaultTopic());
				$reg->setLastAutoTopicTime(time());
			}
		}
	}
	
	if ($user->isLoggedIn() && ($account = $this->getAccount($user->getAccountName())) && $reg && !$kicked) {
		if (($lev = $this->getChannelAccess($chan_name, $user))) {
			$level = $lev->getLevel();
			if ($level >= 100 && $account->autoOps() && $reg->autoOps() && $lev->autoOps()) {
				$bot->op($chan_name, $user->getNumeric());
				$opped = true;
			}
			elseif ($level >= 1 && $account->autoVoices() && $reg->autoVoices() && $lev->autoVoices()) {
				$bot->voice($chan_name, $user->getNumeric());
				$voiced = true;
			}
			
			if ($account->hasInfoLine() && $reg->showsInfoLines())
				$infoline = irc_sprintf('[%U] %s', $account, $account->getInfoLine());
		}
		
		if (!empty($infoline))
			$bot->message($chan_name, $infoline);
	}


