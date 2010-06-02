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

	// User mode flags
	$USER_MODES = array(
		'd' => array('const' => 'UMODE_DEAF',         'uint' => 0x0001),
		'i' => array('const' => 'UMODE_INVISIBLE',    'uint' => 0x0002),
		'o' => array('const' => 'UMODE_OPER',         'uint' => 0x0004),
		's' => array('const' => 'UMODE_SERVERMSG',    'uint' => 0x0008),
		'w' => array('const' => 'UMODE_WALLOPS',      'uint' => 0x0010),
		'k' => array('const' => 'UMODE_SERVICE',      'uint' => 0x0020),
		'g' => array('const' => 'UMODE_HACKMSG',      'uint' => 0x0040),
		'x' => array('const' => 'UMODE_HIDDENHOST',   'uint' => 0x0080),
		'r' => array('const' => 'UMODE_REGISTERED',   'uint' => 0x0100),
		'f' => array('const' => 'UMODE_FAKEHOST',     'uint' => 0x0200)
	);


	class User
	{
		var $numeric;
		var $nick;
		var $account_id = 0;
		var $account_name;
		var $account_ts = 0;
		var $ident;
		var $host;
		var $fakehost;
		var $ip;
		var $start_ts;
		var $desc;
		var $modes = 0;
		var $away_msg;
		var $last_spoke = START_TIME;
		var $channels = array();
		
		function __construct($num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes = "", $account = "", $account_ts = 0)
		{
			$this->numeric = $num;
			$this->nick = $nick;
			$this->account_name = $account;
			$this->account_ts = $account_ts;
			$this->ident = $ident;
			$this->host = $host;
			$this->ip = $ip;
			$this->start_ts = $start_ts;
			$this->desc = $desc;
			$this->addModes($modes);
		}
		
		function isBot()              { return false; }
		function isService()          { return $this->hasMode(UMODE_SERVICE); }
		function isDeaf()             { return $this->hasMode(UMODE_DEAF); }
		function isOper()             { return $this->hasMode(UMODE_OPER); }
		function isRegistered()       { return $this->hasMode(UMODE_REGISTERED); }
		function isHostHidden()      { return $this->hasMode(UMODE_HIDDENHOST); }
		function isLocal()            { return $this->getServerNumeric() == SERVER_NUM; }
		function isAway()             { return $this->away_msg != ''; }
		function isLoggedIn()        { return $this->account_id > 0; }
		function hasAccountName()    { return strlen($this->account_name) > 0; }
		function hasFakehost()        { return $this->hasMode(UMODE_FAKEHOST); }
		
		function getNick()            { return $this->nick; }
		function getIdent()           { return $this->ident; }
		function getHost()            { return $this->host; }
		function getFakehost()        { return $this->fakehost; }
		function getIp()              { return $this->ip; }
		function getName()            { return $this->desc; }
		function getAway()            { return $this->away_msg; }
		function getNumeric()         { return $this->numeric; }
		function getServerNumeric()  { return substr($this->numeric, 0, BASE64_SERVLEN); }
		function getAccountName()    { return $this->account_name; }
		function getAccountId()      { return $this->account_id; }
		function getAccountTs()      { return $this->account_ts; }
		function getSignonTs()       { return $this->start_ts; }
		function getIdleTime()       { return time() - $this->last_spoke; }
		
		function setNick($s)          { $this->nick = $s; }
		function setFakehost($s)      { $this->fakehost = $s; }
		function setAccountId($i)    { $this->account_id = $i; }
		function setAccountName($s)  { $this->account_name = $s; }
		function setAccountTs($t)    { $this->account_ts = $t; }
		function setAway($s = "")     { $this->away_msg = $s; }
		
		
		static function isValidMode($mode)
		{
			global $USER_MODES;
			return in_array($mode, $USER_MODES);
		}
		
		static function isValidModeInt($mode)
		{
			global $USER_MODES;
			foreach ($USER_MODES as $c => $i)
				if ($i['uint'] == $mode)
					return true;
			
			return false;
		}

		function addModes($str)
		{
			global $USER_MODES;
			foreach ($USER_MODES as $c => $i)
				if (strpos($str, $c) !== false) $this->addMode($i['uint']);
		}
		
		function addMode($mode)
		{
			global $USER_MODES;
			if (!is_int($mode))
				return $this->addMode($USER_MODES[$mode]['uint']);
			if ($this->isValidModeInt($mode) && !$this->hasMode($mode))
				$this->modes |= $mode;
		}
		
		function removeMode($mode)
		{
			global $USER_MODES;
			if (!is_int($mode))
				return $this->removeMode($USER_MODES[$mode]['uint']);
			if ($this->isValidModeInt($mode) && $this->hasMode($mode))
				$this->modes &= ~$mode;
		}
		
		function hasMode($mode)
		{
			global $USER_MODES;
			if (!is_int($mode))
				return $this->hasMode($USER_MODES[$mode]['uint']);
			
			return(($this->modes & $mode) == $mode);
		}
		
		function getModes()
		{
			global $USER_MODES;

			$modes = '';
			foreach ($USER_MODES as $c => $i)
				if ($this->hasMode($c)) $modes .= $c;
			
			return $modes;
		}
		
		function getFullMask()      { return $this->nick .'!'. $this->ident .'@'. $this->host; }
		function getFullIpMask()   { return $this->nick .'!'. $this->ident .'@'. $this->ip; }
		function getFullMaskSafe() { $mask = $this->nick .'!'. $this->ident .'@'. $this->getHostSafe(); }

		function getHostSafe()
		{
			if ($this->hasFakehost())
				return $this->getFakehost();
			elseif ($this->isHostHidden() && $this->hasAccountName())
				return $this->getAccountName() .'.'. HIDDEN_HOST;
				
			return $this->getHost();
		}

		function getGlineHost()    { return $this->ident .'@'. $this->host; }
		function getGlineIp()      { return $this->ident .'@'. $this->ip; }
		function getGlineMask()    { return substr($this->getHostMask(), 2); }
		
		function getHostMask()
		{
			$mask = '*!*'. right($this->ident, IDENT_LEN) .'@';
			$host = $this->host;
			
			if ($this->hasFakehost()) {
				$host = $this->fakehost;
			}
			elseif ($this->isHostHidden()) {
				$host = $this->getAccountName() .'.'. HIDDEN_HOST;
			}

			$levels = explode('.', $host);
			$num_levels = count($levels);
			
			if (isIp($host)) {
				$host = assemble($levels, 0, 3, '.');
				$host .= '.*';
			}
			elseif ($num_levels > 2) {
				for ($n = $num_levels - 1; $n > 0; $n--) {
					if (preg_match('/[0-9]/', $levels[$n]))
						break;
				}
				
				$host = '*.';
				$host .= assemble($levels, $n + 1, -1, '.');
			}
			
			$mask = fixHostMask($mask);
			
			return $mask . $host;
		}
		
		function addChannel($name)
		{
			if (!in_array($name, $this->channels))
				$this->channels[] = $name;
		}
		
		function removeChannel($name)
		{
			$channels = $this->channels;
			for ($i = 0; $i < count($channels); ++$i) {
				if ($channels[$i] == $name) {
					unset($channels[$i]);
					break;
				}
			}
			
			$this->channels = array_copy($channels);
		}

		function removeAllChannels()
		{
			$this->channels = array();
		}
		
		function getChannelList()
		{
			return $this->channels;
		}
	}


	foreach ($USER_MODES as $c => $i) {
		if (!defined($i['const']))
			define($i['const'], $i['uint']);
	}
	


