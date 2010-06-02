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

	if (!($reg = $this->getChannelReg($chan_name))) {
		$bot->noticef($user, '%s is not registered!', $chan_name);
		return false;
	}
	
	$mod_uid = $pargs[2];
	$option = strtoupper($pargs[3]);
	$value = '';
	
	if ($cmd_num_args >= 4)
		$value = assemble($pargs, 4);
	
	if ($mod_user = $this->getAccount($mod_uid)) {
		if ((!$access = $this->getChannelAccessAccount($chan_name, $mod_user))) {
			$bot->noticef($user, '%s is not in the %s access list.',
				$mod_user->getName(), $reg->getName());
			return false;
		}
		
		if ($option == 'LEVEL') {
			$new_level = $value;
			if ($new_level < 1 || $new_level > 500) {
				$bot->notice($user, 'Access level must range from 1 to 500.');
				return false;
			}
			
			$access->setLevel($new_level);
			$bot->noticef($user, '%s\'s level on %s has has been changed to %d.',
				$mod_user->getName(), $reg->getName(), $new_level);
		}
		elseif ($option == 'AUTOOP') {
			if (empty($value)) {
				$value = !$access->autoOps();
			}
			else {
				$value = strtoupper($value);
				if ($value == 'ON') $value = true;
				elseif ($value == 'OFF') $value = false;
				else {
					$bot->notice($user, 'Value must either be ON or OFF.');
					return false;
				}
			}
			
			$access->setAutoOp($value);
			$bot->noticef($user, '%s\'s auto-op on %s has been toggled %s.',
				$mod_user->getName(), $reg->getName(), $value ? 'ON' : 'OFF');
		}
		elseif ($option == 'AUTOVOICE') {
			if (empty($value)) {
				$value = !$access->autoVoices();
			}
			else {
				$value = strtoupper($value);
				if ($value == 'ON') $value = true;
				elseif ($value == 'OFF') $value = false;
				else {
					$bot->notice($user, 'Value must either be ON or OFF.');
					return false;
				}
			}
			
			$access->setAutoVoice($value);
			$bot->noticef($user, '%s\'s auto-voice on %s has been toggled %s.',
				$mod_user->getName(), $reg->getName(), $value ? 'ON' : 'OFF');
		}
		elseif ($option == 'PROTECT') {
			if (empty($value)) {
				$value = !$access->isProtected();
			}
			else {
				$value = strtoupper($value);
				if ($value == 'ON') $value = true;
				elseif ($value == 'OFF') $value = false;
				else {
					$bot->notice($user, 'Value must either be ON or OFF.');
					return false;
				}
			}

			$access->setProtect($value);
			$bot->noticef($user, '%s\'s protection on %s has been toggled %s.',
				$mod_user->getName(), $reg->getName(), $value ? 'ON' : 'OFF');
		}
		else {
			$bot->noticef($user, '%s%s%s is not a valid setting!', BOLD_START, $option, BOLD_END);
			return false;
		}
		
		$access->save();
	}
	else {
		$bot->noticef($user, 'Account %s does not exist.', $mod_uid);
		return false;
	}
	

