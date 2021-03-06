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
	
	$chan_name = $pargs[1];
	$purpose = assemble($pargs, 2);
	
	if (!$user->isLoggedIn()) {
		$bot->notice($user, 'You must register a user account before you can register a channel.');
		return false;
	}
	
	if ($chan_name[0] != '#') {
		$bot->notice($user, 'Channel names must begin with the # character.');
		return false;
	}
	
	if ($this->getChannelRegCount($user->getAccountId()) >= MAX_CHAN_REGS) {
		$bot->noticef($user, 'You cannot register more than %d channels.', 
			MAX_CHAN_REGS);
		return false;
	}

	$reg = $this->getChannelReg($chan_name);
	$chan = $this->getChannel($chan_name);
	
	if ($reg) {
		$bot->noticef($user, 'Sorry, %s is already registered.',
			$reg->getName());
		return false;
	}

	if ($this->isBadchan($chan_name)) {
		$bot->noticef($user, 'Sorry, but you are not allowed to register %s.',
			$chan_name);
		return false;
	}

	if (!$chan || !$chan->isOp($user->getNumeric())) {
		$bot->noticef($user, 'You must be an op in %s in order to register it.',
			$chan_name);
		return false;
	}

	$create_ts = time();

	if ($chan != NULL)
		$create_ts = $chan->getTs();

	$reg = new DB_Channel($chan_name, $user->getAccountId());
	$reg->setPurpose($purpose);
	$reg->setCreateTs($create_ts);
	$reg->setRegisterDate(db_date());
	$reg->save();
	$reg = $this->addChannelReg($reg);

	$bot->join($chan_name);
	$this->mode($chan_name, '+Ro '. $bot->getNumeric());
	

