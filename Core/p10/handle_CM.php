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
	
	$chan = $this->getChannel($chan_name);
	$modes = $args[3];
	
	if (!$chan) {
		debugf('Received CLEARMODE for non-existent channel %s', $chan_name);
		return;
	}
	
	for ($i = 0; $i < strlen($modes); $i++) {
		switch ($modes[$i]) {
			case 'o':
				$chan->clearOps();
				break;
			
			case 'v':
				$chan->clearVoices();
				break;
			
			case 'b':
				$chan->clearBans();
				break;
			
			case 'p':
			case 's':
			case 'm':
			case 't':
			case 'i':
			case 'n':
			case 'k': // the Channel class takes care of clearing the key
			case 'l': // the Channel class takes care of setting the limit to 0
			case 'r':
			case 'D':
				$chan->removeMode($modes[$i]);
				break;
			
			default:
				debugf('Received unknown or disallowed mode change in CLEARMODE for %s',
					$chan->getName());
				break;
		}
	}
