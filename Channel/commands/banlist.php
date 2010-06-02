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

	$mask = '*';
	$show_active = false;
	$show_inactive = false;
	
	if ($cmd_num_args > 1)
		$mask = $pargs[2];

	if ($mask == 'active') {
		$mask = '*';
		$show_active = true;
	}
	if ($mask == 'inactive') {
		$mask = '*';
		$show_inactive = true;
	}
	
	$chan = $this->getChannel($chan_name);
	$bans = $chan_reg->getMatchingBans($mask);
	
	if (!$bans) {
		if ($mask == '*')
			$bot->noticef($user, 'The ban list for %s is empty.', $chan_reg->getName());
		else
			$bot->noticef($user, 'There are no bans on %s matching %s.', $chan_reg->getName(), $mask);
		
		return false;
	}
	
	foreach ($bans as $mask => $ban) {
		$active = $chan && $chan->hasBan($mask);
		$status = $active ? 'active' : 'inactive';
		$user_acct = $this->getAccountById($ban->getUserId());
		$reason = $ban->getReason();
		
		if (($show_active && !$active) || ($show_inactive && $active))
			continue;
		
		$bot->noticef($user, '%3d) Mask: %s%s%s (currently %s)', 
			++$ban_num, BOLD_START, $ban->getMask(), BOLD_END, $status);
		$bot->noticef($user, '     Set by: %s%s%s   Level: %s%s%s   Set on: %s', 
			BOLD_START, $user_acct->getName(), BOLD_END,
			BOLD_START, $ban->getLevel(), BOLD_END,
			date('D j M Y H:i:s', $ban->getSetTs()));
		$bot->noticef($user, '     Expires: %s',
			date('D j M Y H:i:s', $ban->getExpireTs()));
		if (!empty($reason))
			$bot->noticef($user, '     Reason: %s',  $reason);
		$bot->notice($user, ' ');
	}
	
	$bot->notice($user, 'End of ban list.');

