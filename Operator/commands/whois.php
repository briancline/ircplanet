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

	if (!($target = $this->getUserByNick($pargs[1]))) {
		$bot->noticef($user, 'There is no user named %s.', $pargs[1]);
		return false;
	}
	
	$acct_str = '';
	if ($target->hasAccountName()) {
		$acct_str = ', logged in as ';
		
		if (!$target->isLoggedIn())
			$acct_str .= 'unknown account ';
		
		$acct_str = $target->getAccountName();
		
		if ($acct = $this->getAccount($target->getAccountName()))
			$acct_str .= ' (Registered on '. irc_getDateTime($acct->getRegisterTs()) .')';
	}
	
	$server = $this->getServer($target->getServerNumeric());
	
	$channels = $target->getChannelList();
	$chan_list = '';
	foreach ($channels as $chan_name) {
		$chan = $this->getChannel($chan_name);
		
		if ($chan->isVoice($target->getNumeric()))
			$chan_list .= '+';
		
		if ($chan->isOp($target->getNumeric()))
			$chan_list .= '@';
		
		$chan_list .= $chan->getName() .' ';
	}
	
	$bot->noticef($user, 'Nick:         %s (User modes +%s)', $target->getNick(), $target->getModes());
	if ($target->isLoggedIn())
		$bot->noticef($user, 'Account:      %s', $acct_str);
	
	if ($target->isHostHidden())
		$bot->noticef($user, 'Hidden host:  %s', $target->getFullMaskSafe());
		
	$bot->noticef($user, 'Full mask:    %s [%s]', $target->getFullMask(), $target->getIp());
	$bot->noticef($user, 'Channels:     %s', $chan_list);
	$bot->noticef($user, 'Server:       %s', $server->getName());
	$bot->noticef($user, 'Signed on:    '. irc_getDateTime($target->getSignonTs()));


