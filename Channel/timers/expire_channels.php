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
	
	if (!defined('MAX_CHAN_AGE') || MAX_CHAN_AGE == 0)
		return;
	
	
	foreach ($this->db_channels as $chan_key => $reg) {
		if ($reg->isPermanent())
			continue;
		
		$youngest_ts = 0;
		foreach ($reg->getLevels() as $user_id => $level) {
			$user = $this->getAccountById($user_id);

			if (!$user) {
				debugf('Found an orphaned access record for user ID %d in %s, deleting',
					$user_id, $reg->getName(), $reg->removeAccess($user_id));
				$level->delete();
				continue;
			}
			
			if ($level->getLevel() == 500 && $youngest_ts < $user->getLastseenTs())
				$youngest_ts = $user->getLastseenTs();
		}
		
		if ($youngest_ts == 0)
			continue;
		
		$age_days = (time() - $youngest_ts) / 86400;
		
		if ($age_days > MAX_CHAN_AGE) {
			debug("Channel ". $reg->getName() ." age is $age_days days");
			
			$this->removeChannelReg($reg);
			$reg->delete();

			$reason = 'So long, and thanks for all the fish!';
			
			if (($chan = $this->getChannel($chan_key)) && $chan->isOn($bot->getNumeric())) {
				$bot->mode($chan->getName(), '-R');
				$this->sendf(FMT_PART_REASON, $bot->getNumeric(), $chan->getName(), $reason);
				$this->removeChannelUser($chan->getName(), $bot->getNumeric());
			}
		}
	}
	


