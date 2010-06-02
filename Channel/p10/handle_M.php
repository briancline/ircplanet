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
	
	$source = $args[0];
	$target = $args[2];
	$target_is_chan = ($target[0] == '#');
	
	if (strlen($source) == 5)
		$source = $this->getUser($source);
	
	if (isUser($source) && !$source->isService() && $target_is_chan && $this->isChannelRegistered($target)) {
		$bans_to_check = array();
		$users_to_check = array();
		$users_to_reop = array();
		$deop_source = false;
		$mode_sub = false;
		$arg_index = 3;
		$modes = $args[3];

		for ($i = 0; $i < strlen($modes); $i++) {
			$mode = $modes[$i];
			
			if ($mode == 'o' || $mode == 'v' || $mode == 'b' || $mode == 'l' || $mode == 'k')
				$arg_index++;
			
			if ($mode == '-')
				$mode_sub = true;
			
			if (!$mode_sub && $mode == 'b')
				$bans_to_check[] = $args[$arg_index];
				
			if ($mode_sub && ($mode == 'o' || $mode == 'v'))
				$users_to_check[] = $this->getUser($args[$arg_index]);
		}

		$source_access = $this->getChannelAccess($target, $source);
		$act_users = $this->getActiveChannelUsers($target);
		
		foreach ($act_users as $tmp_user) {
			$tmp_access = $this->getChannelAccess($target, $tmp_user);

			foreach ($bans_to_check as $tmp_mask) {
				if (fnmatch($tmp_mask, $tmp_user->getFullMask()) 
						|| fnmatch($tmp_mask, $tmp_user->getFullIpMask()))
				{
					$deop_source = true;
					$bot->unban($target, $tmp_mask);
				}
			}
		}
		
		foreach ($users_to_check as $tmp_target) {
			if (!isUser($tmp_target) || !$tmp_user->isLoggedIn())
				continue;
			
			$tmp_access = $this->getChannelAccess($target, $tmp_target);
			if ($tmp_access == false)
				continue;

			if ($tmp_access->isProtected() && 
					(!$source_access || $source_access->getLevel() <= $tmp_target->getLevel()))
			{
				$users_to_reop[] = $tmp_target;
				$deop_source = true;
			}
		}
		
		if (!empty($users_to_reop)) {
			$mode_buf = '';
			$mode_arg_buf = '';
			
			if ($deop_source) {
				$mode_buf = '-o';
				$mode_arg_buf = $source->getNumeric() .' ';
			}
			
			$mode_buf .= '+';
			foreach ($users_to_reop as $tmp_user) {
				$mode_buf .= 'o';
				$mode_arg_buf .= $tmp_user->getNumeric() .' ';
			}
			
			$mode_change = $mode_buf .' '. $mode_arg_buf;
			
			$this->default_bot->mode($target, $mode_change);
		}
	}


