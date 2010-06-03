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
	
	$is_new_user = ($num_args > 4);
	
	if ($is_new_user) {
		$gline_host = $user->getGlineHost();
		$gline_ip = $user->getGlineIp();
		$gline_set = false;
		
		foreach ($this->glines as $gline_key => $gline) {
			if (!$gline->isExpired() && ($gline->matches($gline_host) || $gline->matches($gline_ip))) {
				$this->enforceGline($gline);
				$gline_set = true;
			}
		}
		
		if (defined('CLONE_GLINE') && CLONE_GLINE == true && !$gline_set
				&& $this->getCloneCount($user->getIp()) > CLONE_MAX
				&& !isPrivateIp($user->getIp()))
		{
			$gline_mask = '*@'. $user->getIp();
			$gline_secs = convertDuration(CLONE_DURATION);
			$new_gl = $this->addGline($gline_mask, $gline_secs, time(), CLONE_REASON);
			$this->enforceGline($new_gl);
			$gline_set = true;
		}
		
		if (defined('TOR_GLINE') && TOR_GLINE == true && !$gline_set 
				&& $this->isTorHost($user->getIp()))
		{
			$gline_mask = '*@'. $user->getIp();
			$gline_secs = convertDuration(TOR_DURATION);
			$new_gl = $this->addGline($gline_mask, $gline_secs, time(), TOR_REASON);
			$this->enforceGline($new_gl);
			$gline_set = true;
		}
		
		if (defined('COMP_GLINE') && COMP_GLINE == true && !$gline_set
				&& $this->isCompromisedHost($user->getIp()))
		{
			$gline_mask = '*@'. $user->getIp();
			$gline_secs = convertDuration(COMP_DURATION);
			$new_gl = $this->addGline($gline_mask, $gline_secs, time(), COMP_REASON);
			$this->enforceGline($new_gl);
			$gline_set = true;
		}
	}

	// Don't log new users during the initial burst, as it could flood the log channel.
	if ($this->finished_burst) {
		if ($is_new_user) {
			// new user
			$server = $this->getServer($args[0]);
			$rest = irc_sprintf('%H (%s@%s) [%s]', $user, $user->getIdent(), 
				$user->getHost(), $user->getName());
			$this->reportEvent('NICK', $server, $rest);
		}
		else {
			// nick change
			$this->reportEvent('NICK', $old_nick, $new_nick);
		}
	}


