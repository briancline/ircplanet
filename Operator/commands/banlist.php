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

	if (!($chan = $this->get_channel($chan_name))) {
		$bot->noticef($user, "Nobody is on channel %s.", $chan_name);
		return false;
	}
	
	$mask = '*';
	$show_active = false;
	$show_inactive = false;
	
	if ($cmd_num_args > 1)
		$mask = $pargs[2];
	else
		$mask = '*';
	
	$bans = $chan->get_matching_bans($mask);
	
	if (!$bans) {
		if ($mask == '*')
			$bot->noticef($user, 'The ban list for %s is empty.', $chan->get_name());
		else
			$bot->noticef($user, 'There are no bans on %s matching %s.', $chan->get_name(), $mask);
		
		return false;
	}
	
	$ban_num = 0;
	foreach ($bans as $mask)
		$bot->noticef($user, '%3d) %s%s%s', ++$ban_num, BOLD_START, $mask, BOLD_END);
	
	$bot->notice($user, 'End of ban list.');


