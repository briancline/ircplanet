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

	$server = $pargs[1];
	$jupe = $this->get_jupe($server);
	
	if (!$jupe) {
		$bot->noticef($user, 'There is no jupe for %s.', $server);
		return false;
	}

	/**
	 * ircu doesn't actually allow you to remove a jupe; it can only be deactivated
	 * and/or expire. Since ircu does keep track of the last modification timestamp
	 * of a jupe, we can claim to have the most recent version by updating the
	 * timestamp and changing the duration to 0, so as to make it expire immediately.
	 * This effectively removes the jupe from the network.
	 */
	$jupe->expire_now();

	if ($jupe->is_active()) {
		$this->sendf(FMT_JUPE_ACTIVE, SERVER_NUM, $jupe->get_server(), 0, 
				$jupe->get_last_mod(), $jupe->get_reason());
	}
	else {
		$this->sendf(FMT_JUPE_INACTIVE, SERVER_NUM, $jupe->get_server(), 0, 
				$jupe->get_last_mod(), $jupe->get_reason());
	}
	

