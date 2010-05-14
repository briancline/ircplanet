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
		
		protected function record_construct()   { }
		protected function record_destruct()    { }
		
		public function get_user_id()        { return $this->user_id; }
		public function get_chan_id()        { return $this->chan_id; }
		public function get_level()          { return $this->level; }
		
		public function is_suspended()       { return 1 == $this->suspend; }
		public function is_protected()       { return 1 == $this->protect; }
		public function auto_ops()           { return 1 == $this->auto_op; }
		public function auto_voices()        { return 1 == $this->auto_voice; }
		
		public function set_chan_id($n)      { $this->chan_id = $n; }
		public function set_user_id($n)      { $this->user_id = $n; }
		public function set_level($n)        { $this->level = $n; }
		public function set_suspend($b)      { $this->suspend = $b ? 1 : 0; }
		public function set_protect($b)      { $this->protect = $b ? 1 : 0; }
		public function set_auto_op($b)      { $this->auto_op = $b ? 1 : 0; }
		public function set_auto_voice($b)   { $this->auto_voice = $b ? 1 : 0; }
	}
	

