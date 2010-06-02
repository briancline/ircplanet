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

	class DB_Channel_Access extends DB_Record
	{
		protected $_table_name = 'channel_access';
		protected $_key_field = 'access_id';
		
		protected $user_id;
		protected $chan_id;
		protected $level;
		protected $suspend = 0;
		protected $protect = 0;
		protected $auto_op = 1;
		protected $auto_voice = 0;
		
		protected function recordConstruct()   { }
		protected function recordDestruct()    { }
		
		public function getUserId()        { return $this->user_id; }
		public function getChanId()        { return $this->chan_id; }
		public function getLevel()          { return $this->level; }
		
		public function isSuspended()       { return 1 == $this->suspend; }
		public function isProtected()       { return 1 == $this->protect; }
		public function autoOps()           { return 1 == $this->auto_op; }
		public function autoVoices()        { return 1 == $this->auto_voice; }
		
		public function setChanId($n)      { $this->chan_id = $n; }
		public function setUserId($n)      { $this->user_id = $n; }
		public function setLevel($n)        { $this->level = $n; }
		public function setSuspend($b)      { $this->suspend = $b ? 1 : 0; }
		public function setProtect($b)      { $this->protect = $b ? 1 : 0; }
		public function setAutoOp($b)      { $this->auto_op = $b ? 1 : 0; }
		public function setAutoVoice($b)   { $this->auto_voice = $b ? 1 : 0; }
	}
	

