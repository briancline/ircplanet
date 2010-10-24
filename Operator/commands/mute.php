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

	$mask = $pargs[1];
	$duration = $pargs[2];
	$reason = assemble($pargs, 3);
	
	if ($tmpUser = $this->getUserByNick($mask)) {
		$mask = $tmpUser->getMuteMask();
	}
	elseif (!pregMatch('/[@\.]/', $mask)) {
		$bot->noticef($user, 'Mute masks must be in the ident@host form. Nick masks are not allowed.');
		return false;
	}
	
	$affectedHosts = $this->getMatchingUserhostCount($mask);
	$affectedRatio = ($affectedHosts / count($this->users)) * 100;
	
	if ($mask == '*@*' || $mask == '*@*.*') {
		$bot->noticef($user, 'Your mask is not restrictive enough.');
		return false;
	}
	
	if (!($durationSecs = convertDuration($duration))) {
		$bot->notice($user, 'Invalid duration specified! See help for more details.');
		return false;
	}
	
	$maxTime = 2147483647;
	$expireTime = time() + $durationSecs;
	
	if ($expireTime > $maxTime || $expireTime < 0) {
		$bot->noticef($user, 'The duration you specified is too large. Please try something more sensible.');
		return false;
	}
	
	if ($mute = $this->getMute($mask)) {
		$mute->setDuration($durationSecs);
		$mute->setReason($reason);
		$mute->setLastMod(time());
		$mute->setActive();
	}
	else {
		$mute = $this->addMute($mask, $durationSecs, time(), $reason);
	}
	
	$this->enforceMute($mute);
