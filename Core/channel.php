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

	require_once(CORE_DIR .'/channeluser.php');
	require_once(CORE_DIR .'/ban.php');
	
	// Channel mode flags
	$CHANNEL_MODES = array(
		's' => array('const' => 'CMODE_SECRET',       'uint' => 0x0001),
		'p' => array('const' => 'CMODE_PRIVATE',      'uint' => 0x0002),
		'm' => array('const' => 'CMODE_MODERATE',     'uint' => 0x0004),
		't' => array('const' => 'CMODE_TOPICOPONLY',  'uint' => 0x0008),
		'i' => array('const' => 'CMODE_INVITE',       'uint' => 0x0010),
		'n' => array('const' => 'CMODE_NOEXTMSG',     'uint' => 0x0020),
		'r' => array('const' => 'CMODE_REGONLY',      'uint' => 0x0040),
		'l' => array('const' => 'CMODE_LIMIT',        'uint' => 0x0080),
		'k' => array('const' => 'CMODE_KEY',          'uint' => 0x0100),
		'R' => array('const' => 'CMODE_REGISTERED',   'uint' => 0x0200),
		'D' => array('const' => 'CMODE_DELJOINS',     'uint' => 0x0400),
		'd' => array('const' => 'CMODE_DELJOINSPEND', 'uint' => 0x0800),
		'U' => array('const' => 'CMODE_USERPASS',     'uint' => 0x1000),
		'A' => array('const' => 'CMODE_ADMINPASS',    'uint' => 0x2000)
	);
	
	
	class Channel
	{
		var $name;
		var $topic;
		var $modes;
		var $key;
		var $admin_pass;
		var $user_pass;
		var $ts;
		var $limit = 0;
		var $bans = array();
		var $users = array();
		
		function __construct($name, $ts, $modes = "", $key = "", $limit = 0)
		{
			$this->name = $name;
			$this->ts = $ts;
			$this->addModes($modes);
			$this->key = $key;
			$this->limit = $limit;
		}
		
		function __toString()       { return $this->name; }
		
		function getName()         { return $this->name; }
		function getTopic()        { return $this->topic; }
		function getTs()           { return $this->ts; }
		function getUserCount()   { return count($this->users); }
		function getLimit()        { return $this->limit; }
		function getKey()          { return $this->key; }
		function getAdminPass()   { return $this->admin_pass; }
		function getUserPass()    { return $this->user_pass; }
		
		function isSecret()        { return $this->hasMode(CMODE_SECRET); }
		
		function setTs($v)         { $this->ts = $v; }
		function setName($v)       { $this->name = $v; }
		function setKey($v)        { $this->key = $v; }
		function setAdminPass($v) { $this->admin_pass = $v; }
		function setUserPass($v)  { $this->user_pass = $v; }
		function setModes($v)      { $this->modes = 0; $this->addModes($v); }
		function setTopic($v)      { $this->topic = $v; }
		function setLimit($v) { 
			$this->limit = $v;
			
			if ($v <= 0)
				$this->removeMode(CMODE_LIMIT); 
			else 
				$this->addMode(CMODE_LIMIT);
		}
		
		function getOpList()
		{
			$ops = array();
			foreach ($this->users as $numeric => $chan_user)
				if ($chan_user->isOp())
					$ops[] = $numeric;
			
			return $ops;
		}
		
		function getVoiceList()
		{
			$voices = array();
			foreach ($this->users as $numeric => $chan_user)
				if ($chan_user->isVoice())
					$voices[] = $numeric;
			
			return $voices;
		}

		function getOpCount()
		{
			$count = 0;
			foreach ($this->users as $numeric => $chan_user)
				if ($chan_user->isOp())
					$count++;
			
			return $count;
		}
		
		
		function getVoiceCount()
		{
			$count = 0;
			foreach ($this->users as $numeric => $chan_user)
				if ($chan_user->isVoice())
					$count++;
			
			return $count;
		}
		
		static function isValidMode($mode)
		{
			global $CHANNEL_MODES;
			return array_key_exists($mode, $CHANNEL_MODES);
		}
		
		static function isValidModeInt($mode)
		{
			global $CHANNEL_MODES;
			foreach ($CHANNEL_MODES as $c => $i)
				if ($i['uint'] == $mode)
					return true;
			
			return false;
		}
		
		function hasExactModes($modes)
		{
			global $CHANNEL_MODES;

			$imodes = 0;
			foreach ($CHANNEL_MODES as $c => $i)
				if (strpos($modes, $c) !== false) $imodes |= $i['uint'];
			
			return($imodes == $this->modes);
		}
		

		function addModes($str)
		{
			global $CHANNEL_MODES;
			foreach ($CHANNEL_MODES as $c => $i)
				if (strpos($str, $c) !== false) $this->addMode($i['uint']);
		}
		
		function removeModes($str)
		{
			global $CHANNEL_MODES;
			foreach ($CHANNEL_MODES as $c => $i)
				if (strpos($str, $c) !== false) $this->removeMode($i['uint']);
		}
		
		function addMode($mode)
		{
			global $CHANNEL_MODES;
			if (!is_int($mode) && isset($CHANNEL_MODES[$mode]))
				return $this->addMode($CHANNEL_MODES[$mode]['uint']);
			if ($this->isValidModeInt($mode) && !$this->hasMode($mode)) {
				if ($mode == CMODE_SECRET) {
					$this->removeMode(CMODE_PRIVATE);
				}
				if ($mode == CMODE_PRIVATE) {
					$this->removeMode(CMODE_SECRET);
				}
				
				$this->modes |= $mode;
			}
		}
		
		function removeMode($mode)
		{
			global $CHANNEL_MODES;
			if (!is_int($mode) && isset($CHANNEL_MODES[$mode]))
				return $this->removeMode($CHANNEL_MODES[$mode]['uint']);
			if ($this->isValidModeInt($mode) && $this->hasMode($mode)) {
				if ($mode == CMODE_KEY)
					$this->key = '';
				if ($mode == CMODE_LIMIT)
					$this->limit = 0;
				if ($mode == CMODE_ADMINPASS)
					$this->admin_pass = '';
				if ($mode == CMODE_USERPASS)
					$this->user_pass = '';
				
				$this->modes &= ~$mode;
			}
		}
		
		function hasMode($mode)
		{
			global $CHANNEL_MODES;
			if (!is_int($mode))
				return $this->hasMode($CHANNEL_MODES[$mode]['uint']);
			
			return(($this->modes & $mode) == $mode);
		}
		
		function getModes()
		{
			global $CHANNEL_MODES;

			$modes = '';
			$params = array();
			
			foreach ($CHANNEL_MODES as $c => $i) {
				if ($this->hasMode($c)) {
					$modes .= $c;
					if ($c == 'l')  $params[] = $this->getLimit();
					if ($c == 'k')  $params[] = $this->getKey();
					if ($c == 'A')  $params[] = $this->getAdminPass();
					if ($c == 'U')  $params[] = $this->getUserPass();
				}
			}
			
			if (!empty($params))
				$modes .= ' '. join(' ', $params);
			
			return $modes;
		}
		
		function clearModes()
		{
			$this->modes = 0;
		}
		
		function clearUserModes()
		{
			foreach ($this->users as $numeric => $user)
				$user->clearModes();
		}
		
		function clearOps()
		{
			foreach ($this->users as $user) {
				if ($user->isOp())
					$user->removeMode(CUMODE_OP);
			}
		}
		
		function clearVoices()
		{
			foreach ($this->users as $user) {
				if ($user->isVoice())
					$user->removeMode(CUMODE_VOICE);
			}
		}
		
		function clearBans()
		{
			$this->bans = array();
		}
		

		
		function addBan($mask, $ts = 0, $setby = "")
		{
			if ($ts == 0)
				$ts = time();

			foreach ($this->bans as $ban_mask => $ban) {
				$tmp_mask = strtolower($ban_mask);
				if (fnmatch($mask, $tmp_mask))
					unset($this->bans[$ban_mask]);
			}
			
			$this->bans[$mask] = new Ban($mask, $ts, $setby);
		}
		
		function hasBan($mask)
		{
			$mask = strtolower($mask);
			return array_key_exists($mask, $this->bans);
		}
		
		function getMatchingBans($mask = '*')
		{
			$mask = strtolower($mask);
			$matches = array();
			
			foreach ($this->bans as $ban_mask => $ban) {
				$tmp_mask = strtolower($ban_mask);
				if ($mask == '*' || fnmatch($tmp_mask, $mask))
					$matches[] = $ban_mask;
			}
			
			if (count($matches) > 0)
				return $matches;
			
			return false;
		}
		
		function getInferiorBans($mask)
		{
			$mask = strtolower($mask);
			$matches = array();
			
			foreach ($this->bans as $ban_mask => $ban) {
				$tmp_mask = strtolower($ban_mask);
				if (fnmatch($mask, $tmp_mask))
					$matches[] = $ban_mask;
			}
			
			if (count($matches) > 0)
				return $matches;
			
			return false;
		}
		
		function removeBan($mask)
		{
			unset($this->bans[$mask]);
		}
		
		
		
		function addUser($numeric, $modes)
		{
			$oplevel = 0;
			for ($i = 0; $i < strlen($modes); $i++) {
				if (is_numeric($modes[$i])) {
					$omodes = $modes;
					$oplevel = substr($modes, $i);
					$modes = substr($modes, 0, $i);
					$modes .= 'o';
					break;
				}
			}
			
			$this->users[$numeric] = new ChannelUser($numeric, $modes, $oplevel);
		}
		
		function removeUser($numeric)
		{
			unset($this->users[$numeric]);
		}
		
		function addOp($numeric)         { if($this->isOn($numeric)) $this->users[$numeric]->addMode(CUMODE_OP); }
		function removeOp($numeric)      { if($this->isOn($numeric)) $this->users[$numeric]->removeMode(CUMODE_OP); }
		function addVoice($numeric)      { if($this->isOn($numeric)) $this->users[$numeric]->addMode(CUMODE_VOICE); }
		function removeVoice($numeric)   { if($this->isOn($numeric)) $this->users[$numeric]->removeMode(CUMODE_VOICE); }
		
		function setOplevel($numeric, $level)  { if($this->isOp($numeric)) $this->users[$numeric]->setOplevel($level) ; }
		
		function isOn($numeric)     { return(array_key_exists($numeric, $this->users)); }
		function isOp($numeric)     { return($this->isOn($numeric) && $this->users[$numeric]->isOp()); }
		function isVoice($numeric)  { return($this->isOn($numeric) && $this->users[$numeric]->isVoice()); }
		
		
		function getUserList()
		{
			$users = array();
			foreach ($this->users as $numeric => $chan_user)
				$users[] = $numeric;
			
			return $users;
		}
		
		function getBurstUserlist()
		{
			$userlist = '';
			foreach ($this->users as $numeric => $obj) {
				if (substr($numeric, 0, BASE64_SERVLEN) != SERVER_NUM)
					continue;
				
				if (!empty($userlist))
					$userlist .= ',';
				
				$userlist .= $numeric;
				$modes = $obj->getModes();
				
				if (!empty($modes))
					$userlist .= ':'. $modes;
			}
			
			return $userlist;
		}
		
		function getBurstBanlist()
		{
			$banlist = '';
			foreach ($this->bans as $ban) {
				if (!empty($banlist))
					$banlist .= ' ';
				
				$banlist .= $ban->mask;
			}
			
			return $banlist;
		}
	}
	
	
	foreach ($CHANNEL_MODES as $c => $i) {
		if (!defined($i['const']))
			define($i['const'], $i['uint']);
	}
	
	

