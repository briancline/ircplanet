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

	if (!($chan = $this->getChannel($chan_name))) {
		$bot->noticef($user, "Nobody is on channel %s.", $chan_name);
		return false;
	}
	if (!$chan->isOn($bot->getNumeric())) {
		$bot->noticef($user, 'I am not on %s.', $chan->getName());
		return false;
	}
	
	$reason = assemble($pargs, 2);
	$users = $this->getChannelUsersByMask($chan_name);
	
	foreach ($users as $numeric => $chan_user) {
		if (!$chan_user->isBot() && $chan_user != $user) {
			$mask = $chan_user->getHostMask();

			$ban = new DB_Ban($chan_reg->getId(), $user->getAccountId(), $mask);
			$ban->setReason($reason);
			$chan_reg->addBan($ban);
			
			$bot->mode($chan->getName(), "-o+b $numeric $mask");
			$bot->kick($chan->getName(), $numeric, $reason);
			$chan->addBan($mask);
		}
	}

	$chan_reg->save();


