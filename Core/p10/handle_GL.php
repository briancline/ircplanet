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

	$active = ($args[3][0] == '+');
	$mask = substr($args[3], 1);
	$duration = $args[4];
	$lastmod = ($num_args >= 7 ? $args[5] : 0);
	$lifetime = ($num_args >= 8 ? $args[6] : 0);
	$reason = $args[$num_args - 1];
	
	$gline = $this->getGline($mask);

	if ($gline && $lastmod > $gline->getLastMod()) {
		if ($active) {
			debugf('*** Re-activating G-line %s', $gline->getMask());
			$gline->setActive();
		}
		else {
			debugf('*** De-activating G-line %s', $gline->getMask());
			$gline->setInactive();
		}
		
		$gline->setDuration($duration);
		$gline->setLastMod($lastmod);
		$gline->setReason($reason);
		
		if (method_exists($this, 'serviceChangeGline')) {
			$this->serviceChangeGline($gline);
		}
	}
	elseif (!$gline) {
		$this->addGline($mask, $duration, time(), $lastmod, $reason, $active);
	}
