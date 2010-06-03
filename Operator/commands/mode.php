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

	if (!($chan = $this->getChannel($chan_name))) {
		$bot->noticef($user, "Nobody is on channel %s.", $chan_name);
		return false;
	}
	
	$modes = $pargs[2];
	$mode_str = '';
	$mode_arg = 3;
	$mode_add = true;
	$mode_args = array();
	$addModes = $rem_modes = array();
	$new_key = $new_limit = '';
	
	for ($m = 0; $m < strlen($modes); ++$m) {
		$mode = $modes[$m];
		
		switch ($mode) {
			case '+':  $mode_add = true;  $mode_str .= $mode; break;
			case '-':  $mode_add = false; $mode_str .= $mode; break;
			case 'i':
			case 'm':
			case 'n':
			case 'p':
			case 'r':
			case 's':
			case 't':
				$mode_str .= $mode;

				if ($mode_add)
					$addModes[] = $mode;
				else
					$rem_modes[] = $mode;
				
				break;
			
			case 'k':
				if ($mode_add) {
					if ($cmd_num_args < $mode_arg) {
						$bot->notice($user, 'You did not specify a key!');
						return false;
					}
					
					$mode_str .= $mode;
					$addModes[] = $mode;

					$new_key = $pargs[$mode_arg];
					$mode_args[] = $pargs[$mode_arg++];
					break;
				}
				else {
					$mode_args[] = '*';
					$rem_modes[] = $mode;
					$mode_str .= $mode;
					break;
				}
			
			case 'l':
				if ($mode_add) {
					if ($cmd_num_args < $mode_arg) {
						$bot->notice($user, 'You did not specify a limit!');
						return false;
					}
					
					$new_limit = $pargs[$mode_arg];
					if ($new_limit <= 0 || !is_numeric($new_limit)) {
						$new_limit = 0;
						break;
					}
					
					$mode_str .= $mode;
					$addModes[] = $mode;

					$mode_args[] = $pargs[$mode_arg++];
					break;
				}
				else {
					$rem_modes[] = $mode;
					$mode_str .= $mode;
					break;
				}
			
			case 'o':
			case 'v':
			case 'b':
				$bot->notice($user, 'The mode command cannot be used to change ops, voices, or bans.');
				return false;
				break;
			
			default:
				$bot->noticef($user, '%s is not a valid channel mode!', $mode);
				return false;
				break;
		}
	}
	
	if (strlen($mode_str) > 0) {
		if (!preg_match('/^[+-]/', $mode_str))
			$mode_str = '+'. $mode_str;
		if (count($mode_args) > 0)
			$mode_str .= ' '. join(' ', $mode_args);
		
		$this->mode($chan_name, $mode_str);
	}
	
	if (count($addModes) > 0)
		$chan->addModes(join('', $addModes));
	if (count($rem_modes) > 0)
		$chan->removeModes(join('', $rem_modes));
	if (strlen($new_key) > 0)
		$chan->setKey($new_key);
	if ($new_limit > 0)
		$chan->setLimit($new_limit);
	
//	$bot->noticef($user, '%s modes are now: %s %s %s', $chan->getName(), $chan->getModes(), $chan->getLimit(), $chan->getKey());
	

