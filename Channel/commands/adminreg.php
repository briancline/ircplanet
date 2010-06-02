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
	$owner_nick = $pargs[2];
	$purpose = '';
	
	if ($cmd_num_args > 2)
		$purpose = assemble($pargs, 3);
	
	if ($chan_name[0] != '#') {
		$bot->notice($user, 'Channel names must begin with the # character.');
		return false;
	}
	
	if (!($owner = $this->getAccount($owner_nick))) {
		$bot->noticef($user, '%s is not a known account name!', $owner_nick);
		return false;
	}
	
	if (!($reg = $this->getChannelReg($chan_name))) {
		$reg = new DB_Channel($chan_name, $owner->getId());
		$reg->setPurpose($purpose);
		$reg->save();
		$reg = $this->addChannelReg($reg);
		
		$bot->join($chan_name);
		$chan = $this->getChannel($chan_name);
		
		if (!$chan->isOp($bot->getNumeric()))
			$this->mode($chan_name, '+Ro '. $bot->getNumeric());
		else
			$bot->mode($chan_name, '+R');
	}
	else {
		$bot->noticef($user, 'Sorry, %s is already registered.',
			$reg->getName());
		return false;
	}


