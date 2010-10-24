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

	class Mute
	{
		protected $mask;
		protected $expire_ts;
		protected $lastmod_ts;
		protected $reason;
		protected $active = false;
		
		public function __construct($mask, $duration, $lastmod, $reason, $active = true)
		{
			$this->mask = $mask;
			$this->expire_ts = time() + $duration;
			$this->lastmod_ts = $lastmod;
			$this->reason = $reason;
			$this->active = $active;
		}
		
		public function __toString()       { return $this->mask; }
		
		public function getMask()          { return $this->mask; }
		public function getExpireTs()      { return $this->expire_ts; }
		public function getLastMod()       { return $this->lastmod_ts; }
		public function getDuration()      { return $this->expire_ts - time(); }
		public function getReason()        { return $this->reason; }
		public function isActive()         { return 1 == $this->active; }
		public function isExpired()        { return (time() >= $this->expire_ts); }
		
		public function setDuration($n)    { $this->expire_ts = time() + $n; }
		public function setLastMod($n)     { $this->lastmod_ts = $n; }
		public function setReason($s)      { $this->reason = $s; }
		public function setActive()        { $this->active = 1; }
		public function setInactive()      { $this->active = 0; }
		
		public function matches($host)
		{
			if (isUser($host) || isBot($host)) {
				return fnmatch($this->mask, $host->getGlineHost()) 
					|| fnmatch($this->mask, $host->getGlineIp());
			}
			else {
				return fnmatch($this->mask, $host);
			}
		}
	}


