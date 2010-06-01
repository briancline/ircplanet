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
	
	$numeric = $args[0];
	$parted_all_chans = false;
	
	/**
	 * /join 0 is the same as /parting all channels.
	 */
	if ($args[2] == '0') {
		$this->remove_user_from_all_channels($numeric);
		$parted_all_chans = true;
	}
	else {
		$channels = explode(',', $chan_name);
		foreach ($channels as $chan_name) {
			/**
			 * As retarded as I think this is, we now have to check and see if
			 * a channel we receive via a J token actually exists first. This
			 * is due to ircu2.10.12.x's new 'zannel' behavior and +A channel
			 * mode. In some cases, ircu won't remove a channel from memory
			 * for up to 48 hours after the last user /parts it. Thus, we may
			 * receive a J message from the uplink if this has occurred. P10
			 * still thinks the channel exists, but we know better...don't we.
			 */

			$chan = $this->get_channel($chan_name);

			if (!$chan) {
				$ts = $args[count($args) - 1];
				$this->add_channel($chan_name, $ts);
				$this->add_channel_user($chan_name, $numeric, 'o');
			}
			else {
				$this->add_channel_user($chan_name, $numeric);
			}
		}
	}
	

