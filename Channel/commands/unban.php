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
	
	$mask = $pargs[2];

	if (!preg_match('/[!@\.]/', $mask)) {
		if (($tmp_user = $this->getUserByNick($mask)))
			$mask = $tmp_user->getHostMask();
		else
			$mask = $mask . '!*@*';
	}
	
	$ban = $chan_reg->getBan($mask);
	$active = $chan->hasBan($mask);

	if (!$ban && !$active) {
		$bot->noticef($user, 'There is no ban for %s on %s.', $mask, $chan_reg->getName());
		return false;
	}
	
	if ($ban) {
		if ($ban->getLevel() > $user_level) {
			$bot->noticef($user, 'You cannot remove a ban with a level higher than your own.');
			return false;
		}
		
		$mask = $ban->getMask();
		$bot->unban($chan->getName(), $mask);
		$chan_reg->removeBan($mask);
		$chan_reg->save();
	}
	
	if ($active) {
		$bot->unban($chan->getName(), $mask);
	}
	else {
		$bot->noticef($user, 'The ban for %s has been removed.', $mask);
	}
	

