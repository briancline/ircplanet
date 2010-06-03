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

	$option = strtoupper($pargs[2]);
	$value = '';
	
	if ($cmd_num_args >= 3)
		$value = assemble($pargs, 3);
	
	
	if ($option == 'PURPOSE') {
		if (strlen($value) >= MAXLEN_CHAN_PURPOSE) {
			$bot->notice($user, 'The channel purpose you provided is too long. Please try something shorter.');
			return false;
		}
		
		$chan_reg->setPurpose($value);
		$bot->notice($user, 'Updated channel purpose.');
	}
	
	
	
	elseif ($option == 'URL') {
		if (strlen($value) >= MAXLEN_CHAN_URL) {
			$bot->notice($user, 'The channel URL you provided is too long. Please try something shorter.');
			return false;
		}
		
		$chan_reg->setUrl($value);
		$bot->notice($user, 'Updated channel URL.');
	}
	
	
	
	elseif ($option == 'DEFTOPIC') {
		if (strlen($value) >= MAXLEN_CHAN_DEFAULT_TOPIC) {
			$bot->notice($user, 'The default channel topic you provided is too long. Please try something shorter.');
			return false;
		}
		
		$chan_reg->setDefaultTopic($value);
		$bot->topic($chan_name, $value);
		$bot->notice($user, 'Updated default channel topic.');
	}
	
	
	
	elseif ($option == 'DEFMODES') {
		if (strlen($value) >= MAXLEN_CHAN_DEFAULT_MODES) {
			$bot->notice($user, 'The default channel modes you provided were too long. Please try something shorter.');
			return false;
		}
		
		$value = $this->cleanModes($value);
		$chan_reg->setDefaultModes($value);
		$bot->mode($chan_name, $value);
		$bot->noticef($user, 'Updated default channel modes to %s.', $value);
	}
	
	
	
	elseif ($option == 'INFOLINES') {
		if (empty($value)) {
			$value = !$chan_reg->showsInfoLines();
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
		
		$chan_reg->setInfoLines($value);
		$bot->noticef($user, 'Toggled display of channel info lines to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'AUTOOP') {
		if (empty($value)) {
			$value = !$chan_reg->autoOps();
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
		
		$chan_reg->setAutoOp($value);
		$bot->noticef($user, 'Toggled channel auto op to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'AUTOOPALL') {
		if (empty($value)) {
			$value = !$chan_reg->autoOpsAll();
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
		
		$chan_reg->setAutoOpAll($value);
		$bot->noticef($user, 'Toggled channel auto op everyone to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'AUTOVOICE') {
		if (empty($value)) {
			$value = !$chan_reg->autoVoices();
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
		
		$chan_reg->setAutoVoice($value);
		$bot->noticef($user, 'Toggled channel auto voice to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'AUTOVOICEALL') {
		if (empty($value)) {
			$value = !$chan_reg->autoVoicesAll();
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
		
		$chan_reg->setAutoVoiceAll($value);
		$bot->noticef($user, 'Toggled channel auto voice everyone to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'AUTOLIMIT') {
		if (empty($value)) {
			$value = !$chan_reg->autoLimits();
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
		
		$chan_reg->setAutoLimit($value);
		$bot->noticef($user, 'Toggled channel auto user limit to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'AUTOLIMITBUFFER') {
		if (!is_numeric($value) || $value < MIN_CHAN_AUTOLIMIT_BUFFER || $value > MAX_CHAN_AUTOLIMIT_BUFFER) {
			$bot->noticef($user, 'The user count buffer must be an integer ranging from %d to %d.',
				MIN_CHAN_AUTOLIMIT_BUFFER, MAX_CHAN_AUTOLIMIT_BUFFER);
			return false;
		}
		
		$chan_reg->setAutoLimitBuffer($value);
		$bot->noticef($user, 'Updated auto limit usercount buffer to %d users.', $value);
	}
	
	
	
	elseif ($option == 'AUTOLIMITWAIT') {
		if (!is_numeric($value) || $value < MIN_CHAN_AUTOLIMIT_WAIT || $value > MAX_CHAN_AUTOLIMIT_WAIT) {
			$bot->noticef($user, 'The auto limit delay must be an integer ranging from %d to %d.',
				MIN_CHAN_AUTOLIMIT_WAIT, MAX_CHAN_AUTOLIMIT_WAIT);
			return false;
		}
		
		$chan_reg->setAutoLimitWait($value);
		$bot->noticef($user, 'Updated auto limit delay to %d seconds.', $value);
	}
	
	
	
	elseif ($option == 'STRICTOP') {
		if (empty($value)) {
			$value = !$chan_reg->strictOps();
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
		
		$chan_reg->setStrictOp($value);
		$bot->noticef($user, 'Toggled channel strict ops to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'STRICTVOICE') {
		if (empty($value)) {
			$value = !$chan_reg->strictVoices();
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
		
		$chan_reg->setStrictVoice($value);
		$bot->noticef($user, 'Toggled channel strict voices to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'STRICTTOPIC') {
		if (empty($value)) {
			$value = !$chan_reg->strictTopic();
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
		
		$chan_reg->setStrictTopic($value);
		$bot->noticef($user, 'Toggled channel strict topic to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'STRICTMODES') {
		if (empty($value)) {
			$value = !$chan_reg->strictModes();
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
		
		$chan_reg->setStrictModes($value);
		$bot->noticef($user, 'Toggled channel strict modes to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	elseif ($option == 'NOPURGE' && $user_admin_level >= 500) {
		if (empty($value)) {
			$value = !$chan_reg->isPermanent();
		}
		else {
			$value = strtoupper($value);
			if ($value == 'ON')      $value = true;
			elseif ($value == 'OFF') $value = false;
			else {
				$bot->notice($user, 'Value must either be ON or OFF.');
				return false;
			}
		}
		
		$chan_reg->setPermanent($value);
		$bot->noticef($user, 'Toggled nopurge flag to %s.',
			$value ? 'ON' : 'OFF');
	}
	
	
	
	else {
		$bot->noticef($user, '%s%s%s is not a valid channel option!',
			BOLD_START, $option, BOLD_END);
		return false;
	}
	
	
	$chan_reg->save();	
	

