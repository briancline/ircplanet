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

	class DB_Gline extends DB_Record
	{
		protected $_table_name = 'os_glines';
		protected $_key_field = 'gline_id';
		
		protected $gline_id;
		protected $set_ts = 0;
		protected $expire_ts = 0;
		protected $lastmod_ts = 0;
		protected $mask;
		protected $reason;
		protected $active = 0;
		
		protected function recordConstruct() { }
		protected function recordDestruct()  { }
		
		public function getSetTs()           { return $this->set_ts; }
		public function getExpireTs()        { return $this->expire_ts; }
		public function getLastMod()         { return $this->lastmod_ts; }
		public function getRemainingSecs()   { return $this->getExpireTs() - time(); }
		public function getMask()            { return $this->mask; }
		public function getReason()          { return $this->reason; }
		public function isActive()           { return 1 == $this->active; }
		public function isExpired()          { return (time() >= $this->expire_ts); }
		public function isRealName()         { return '$R' == substr($this->mask, 0, 2); }
		public function isChannel()          { return '#' == $this->mask[0]; }
		
		public function setTs($n)            { $this->set_ts = $n; }
		public function setDuration($n)      { $this->expire_ts = time() + $n; }
		public function setLastMod($n)       { $this->lastmod_ts = $n; }
		public function setMask($s)          { $this->mask = fixHostMask($s); }
		public function setReason($s)        { $this->reason = $s; }
		public function setActive()          { $this->active = 1; }
		public function setInactive()        { $this->active = 0; }
		public function setActiveState($b)   { $this->active = ($b ? 1 : 0); }
		
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
