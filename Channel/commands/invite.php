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

	$nicks = array();

	if (!($chan = $this->getChannel($chan_name))) {
		$bot->noticef($user, "Nobody is on channel %s.", $chan_name);
		return false;
	}
	if (!$chan->isOn($bot->getNumeric())) {
		$bot->noticef($user, 'I am not on %s.', $chan->getName());
		return false;
	}
	if ($cmd_num_args == 1) {
		if ($chan->isOn($user->getNumeric())) {
			$bot->noticef($user, "You're already on %s...", $chan->getName());
			return false;
		}
		
		$nicks[] = $user->getNick();
	}
	else {
		for ($i = 2; $i < count($pargs); ++$i) {
			$nick = $pargs[$i];
			$tmp_user = $this->getUserByNick($nick);
			
			if (!$tmp_user) {
				$bot->noticef($user, "The user %s%s%s does not exist.",
					BOLD_START, $nick, BOLD_END);
				continue;
			}
			
			if ($chan->isOn($tmp_user->getNumeric())) {
				$bot->noticef($user, "%s is already on %s.",
					$tmp_user->getNick(), $chan->getName());
				continue;
			}
			
			$nicks[] = $tmp_user->getNick();
		}
	}
	
	foreach ($nicks as $nick)
		$bot->invite($nick, $chan->getName());
	

