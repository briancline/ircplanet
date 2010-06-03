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

	define( 'MAXLEN_USERINFOLINE', 150 );
	define( 'MAXLEN_USERNAME',      20 );
	define( 'MAXLEN_USEREMAIL',    100 );
	define( 'MAXLEN_FAKEHOST',      63 );
	
	require_once('db_record.php');
	
	class DB_User extends DB_Record
	{
		protected $_table_name = 'accounts';
		protected $_key_field = 'account_id';
		protected $_insert_timestamp_field = 'create_date';
		protected $_update_timestamp_field = 'update_date';
		
		protected $account_id;
		protected $name;
		protected $password;
		protected $email;
		protected $info_line;
		protected $fakehost;
		protected $lastseen_ts = 0;
		protected $register_ts = 0;
		protected $suspend = 0;
		protected $no_purge = 0;
		protected $auto_op = 0;
		protected $auto_voice = 0;
		protected $enforce_nick = 0;
		
		protected function recordConstruct()
		{
			if (func_num_args() == 0)
				return;
			
			$name = func_get_arg(0);
		}
		
		protected function recordDestruct()
		{
			
		}
		
		public function getId()             { return $this->account_id; }
		public function getName()           { return $this->name; }
		public function getPassword()       { return $this->password; }
		public function getEmail()          { return $this->email; }
		public function getInfoLine()      { return $this->info_line; }
		public function getFakehost()       { return $this->fakehost; }
		public function getRegisterTs()    { return $this->create_date; }
		public function getLastseenTs()    { return $this->lastseen_ts; }
		
		public function hasInfoLine()      { return !empty($this->info_line); }
		public function hasFakehost()       { return !empty($this->fakehost); }
		public function isSuspended()       { return 1 == $this->suspend; }
		public function isPermanent()       { return 1 == $this->no_purge; }
		public function autoOps()           { return 1 == $this->auto_op; }
		public function autoVoices()        { return 1 == $this->auto_voice; }
		public function enforcesNick()      { return 1 == $this->enforce_nick; }
		
		public function setName($s)         { $this->name = $s; }
		public function setPassword($s)     { $this->password = $s; }
		public function setEmail($s)        { $this->email = $s; }
		public function setInfoLine($s)    { $this->info_line = $s; }
		public function setFakehost($s)     { $this->fakehost = $s; }
		public function setRegisterTs($t)  { $this->register_ts = $t; }
		public function setSuspend($b)      { $this->suspend = $b ? 1 : 0; }
		public function setPermanent($b)    { $this->no_purge = $b ? 1 : 0; }
		public function setAutoOp($b)      { $this->auto_op = $b ? 1 : 0; }
		public function setAutoVoice($b)   { $this->auto_voice = $b ? 1 : 0; }
		public function setEnforceNick($b) { $this->enforce_nick = $b ? 1 : 0; }
		public function updateLastseen()    { $this->lastseen_ts = time(); }
	}


