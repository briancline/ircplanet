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
	$duration = '60m';                // default duration
	$reason = '';                     // default reason
	$level = $user_channel_level;
	$kick_reason = 'Banned';
	
	if ($level == 0)
		$level = 75;
	if ($cmd_num_args >= 3) {
		$reason = assemble($pargs, 3);
		$kick_reason = $reason;
	}
	
	if ($level > $user_level) {
		$bot->noticef($user, 'The level you specified is too high and must be %s or lower.',
			$user_level);
		return false;
	}
	
	if (!($duration_secs = convertDuration($duration))) {
		$bot->notice($user, 'Invalid duration specified! See help for more details.');
		return false;
	}
	
	if (!preg_match('/[!@\.]/', $mask)) {
		if (($tmp_user = $this->getUserByNick($mask)))
			$mask = $tmp_user->getHostMask();
		else
			$mask = $mask . '!*@*';
	}
	
	$mask = fixHostMask($mask);
	
	if (($ban = $chan_reg->getBan($mask))) {
		$bot->noticef($user, 'A ban for %s already exists.', $ban->getMask());
		return false;
	}
	
	if (($ban = $chan_reg->countMatchingBans($mask))) {
		$bot->noticef($user, 'An existing ban for %s supercedes the one you are trying to set.',
			$ban->getMask());
		return false;
	}
	
	$ban = new DB_Ban($chan_reg->getId(), $user->getAccountId(), $mask, $duration_secs, $level, $reason);
	$chan_reg->addBan($ban);
	$chan_reg->save();
	
	$bot->ban($chan->getName(), $mask);
	$chan->addBan($mask);

	$kick_users = $this->getChannelUsersByMask($chan->getName(), $mask);
	foreach ($kick_users as $numeric => $chan_user) {
		if (!$chan_user->isBot() && $chan_user != $user)
			$bot->kick($chan->getName(), $numeric, $kick_reason);
	}
	
	

