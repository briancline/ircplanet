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
	
	require_once('db_channel_access.php');
	require_once('db_ban.php');
	
	define('MAXLEN_CHAN_PURPOSE',        200);
	define('MAXLEN_CHAN_URL',            255);
	define('MAXLEN_CHAN_DEFAULT_TOPIC',  255);
	define('MAXLEN_CHAN_DEFAULT_MODES',   20);
	define('MIN_CHAN_AUTOLIMIT_BUFFER',    1);
	define('MAX_CHAN_AUTOLIMIT_BUFFER',  100);
	define('MIN_CHAN_AUTOLIMIT_WAIT',      1);
	define('MAX_CHAN_AUTOLIMIT_WAIT',    300);
	
	class DB_Channel extends DB_Record 
	{
		protected $_table_name = 'channels';
		protected $_key_field = 'channel_id';
		protected $_exclude_from_insert = array('levels', 'bans');
		protected $_exclude_from_update = array('levels', 'bans');
		protected $_insert_timestamp_field = 'create_date';
		protected $_update_timestamp_field = 'update_date';
		
		protected $channel_id = 0;
		protected $name;
		protected $register_ts;
		protected $create_ts;
		protected $purpose;
		
		protected $def_topic;
		protected $def_modes;
		protected $info_lines = 0;
		protected $suspend = 0;
		protected $no_purge = 0;
		protected $auto_op = 1;
		protected $auto_op_all = 0;
		protected $auto_voice = 0;
		protected $auto_voice_all = 0;
		protected $auto_limit = 0;
		protected $auto_limit_buffer = 5;
		protected $auto_limit_wait = 30;
		protected $strict_op = 0;
		protected $strict_voice = 0;
		protected $strictModes = 0;
		protected $strictTopic = 0;
		protected $no_op = 0;
		protected $no_voice = 0;
		
		protected $levels = array();
		protected $bans = array();
		
		function recordConstruct($args)
		{
			if (count($args) > 1) {
				$name = $args[0];
				$owner_id = $args[1];
			}
			
			if (!empty($name)) {
				$this->name = $name;
				$this->register_ts = time();
				$this->register_date = db_date();
			}
			
			if (isset($owner_id) && $owner_id > 0) {
				$this->save();
				$owner = new DB_Channel_Access();
				$owner->setChanId($this->channel_id);
				$owner->setUserId($owner_id);
				$owner->setLevel(500);
				$this->addAccess($owner);
			}
		}
		
		function recordDestruct()
		{
		}
		

		function hasDefaultTopic()       { return !empty($this->def_topic); }
		function hasDefaultModes()       { return !empty($this->def_modes); }
		function showsInfoLines()        { return 1 == $this->info_lines; }
		function isSuspended()            { return 1 == $this->suspend; }
		function isPermanent()            { return 1 == $this->no_purge; }
		function autoOps()                { return 1 == $this->auto_op; }
		function autoOpsAll()            { return 1 == $this->auto_op_all; }
		function autoVoices()             { return 1 == $this->auto_voice; }
		function autoVoicesAll()         { return 1 == $this->auto_voice_all; }
		function autoLimits()             { return 1 == $this->auto_limit; }
		function strictOps()              { return 1 == $this->strict_op; }
		function strictVoices()           { return 1 == $this->strict_voice; }
		function strictModes()            { return 1 == $this->strictModes; }
		function strictTopic()            { return 1 == $this->strictTopic; }
		function noOps()                  { return 1 == $this->no_op; }
		function noVoices()               { return 1 == $this->no_voice; }
		
		function getId()                  { return $this->channel_id; }
		function getName()                { return $this->name; }
		function getRegisterTs()         { return $this->register_ts; }
		function getCreateTs()           { return $this->create_ts; }
		function getPurpose()             { return $this->purpose; }
		function getUrl()                 { return $this->url; }
		function getDefaultTopic()       { return $this->def_topic; }
		function getDefaultModes()       { return $this->def_modes; }
		function getAutoLimitBuffer()   { return $this->auto_limit_buffer; }
		function getAutoLimitWait()     { return $this->auto_limit_wait; }
		
		function getLevels()              { return $this->levels; }
		
		function hasPendingAutolimit()   { return isset($this->_alimit_pending) && $this->_alimit_pending; }
		function setPendingAutolimit($b) { $this->_alimit_pending = $b; }
		
		function setCreateTs($n)         { $this->create_ts = $n; }
		function setRegisterDate($d)     { $this->register_date = $d; }
		function setPurpose($s)           { $this->purpose = $s; }
		function setUrl($s)               { $this->url = $s; }
		function setDefaultTopic($s)     { $this->def_topic = $s; }
		function setDefaultModes($s)     { $this->def_modes = $s; }
		function setInfoLines($b)        { $this->info_lines = $b ? 1 : 0; }
		function setSuspend($b)           { $this->suspend = $b ? 1 : 0; }
		function setPermanent($b)         { $this->no_purge = $b ? 1 : 0; }
		function setAutoOp($b)           { $this->auto_op = $b ? 1 : 0; }
		function setAutoOpAll($b)       { $this->auto_op_all = $b ? 1 : 0; }
		function setAutoVoice($b)        { $this->auto_voice = $b ? 1 : 0; }
		function setAutoVoiceAll($b)    { $this->auto_voice_all = $b ? 1 : 0; }
		function setAutoLimit($b)        { $this->auto_limit = $b ? 1 : 0; }
		function setAutoLimitBuffer($n) { $this->auto_limit_buffer = $n; }
		function setAutoLimitWait($n)   { $this->auto_limit_wait = $n; }
		function setStrictOp($b)         { $this->strict_op = $b ? 1 : 0; }
		function setStrictVoice($b)      { $this->strict_voice = $b ? 1 : 0; }
		function setStrictModes($b)      { $this->strictModes = $b ? 1 : 0; }
		function setStrictTopic($b)      { $this->strictTopic = $b ? 1 : 0; }
		function setNoOp($b)             { $this->no_op = $b ? 1 : 0; }
		function setNoVoice($b)          { $this->no_voice = $b ? 1 : 0; }
		
		
		function addAccess($access_obj)
		{
			$user_id = $access_obj->getUserId();
			$this->levels[$user_id] = $access_obj;
		}
		
		function removeAccess($user_id)
		{
			if (array_key_exists($user_id, $this->levels)) {
				$this->levels[$user_id]->delete();
				unset($this->levels[$user_id]);
			}
		}
		
		
		function getLevelById($user_id)
		{
			if (array_key_exists($user_id, $this->levels))
				return $this->levels[$user_id]->getLevel();
			
			return 0;
		}
		
		
		function addBan($ban_obj)
		{
			$mask = strtolower($ban_obj->getMask());
			$this->bans[$mask] = $ban_obj;
		}
		
		function getBan($mask)
		{
			$mask = strtolower($mask);
			if (array_key_exists($mask, $this->bans))
				return $this->bans[$mask];
			
			return false;
		}
		
		function removeBan($mask)
		{
			$mask = strtolower($mask);
			if (array_key_exists($mask, $this->bans)) {
				$this->bans[$mask]->delete();
				unset($this->bans[$mask]);
			}
		}
		
		function clearBans()
		{
			foreach ($this->bans as $mask => $ban)
				$ban->delete();

			$this->bans = array();
		}
		
		function countMatchingBans($mask)
		{
			if (is_object($mask))
				return $this->countMatchingBans($mask->getFullMask());
			
			$match_count = 0;
			$mask = strtolower($mask);
			
			foreach ($this->bans as $mask_iter => $ban) {
				if (fnmatch($mask_iter, $mask))
					$match_count++;
			}
			
			return $match_count;
		}
		
		function hasMatchingBans($mask)
		{
			if (is_object($mask))
				return $this->hasMatchingBans($mask->getFullMask());
			
			$mask = strtolower($mask);
			
			foreach ($this->bans as $mask_iter => $ban) {
				if (fnmatch($mask_iter, $mask))
					return true;
			}
			
			return false;
		}
		
		function getMatchingBan($mask)
		{
			if (is_object($mask))
				return $this->getMatchingBan($mask->getFullMask());
			
			$mask = strtolower($mask);
			
			foreach ($this->bans as $mask_iter => $ban) {
				if (fnmatch($mask_iter, $mask))
					return $ban;
			}
			
			return false;
		}
		
		
		function getMatchingBans($mask = '*')
		{
			if (is_object($mask))
				return $this->getMatchingBans($mask->getFullMask());
			
			$matches = array();
			$mask = strtolower($mask);
			
			foreach ($this->bans as $mask_iter => $ban) {
				if ($mask == '*' || fnmatch($mask, $mask_iter))
					$matches[$mask_iter] = $ban;
			}
			
			if (count($matches) == 0)
				return false;
			
			return $matches;
		}
		
		
		function recordSave()
		{
			foreach ($this->levels as $user_id => $access) {
				if (empty($user_id) || $user_id == 0 || $this->channel_id == 0)
					continue;
				
				$access->save();
			}
			
			foreach ($this->bans as $mask => $ban) {
				if (empty($mask) || !isBanRecord($ban) || $this->channel_id == 0)
					continue;
				
				$ban->save();
			}
		}
		
		
		function recordDelete()
		{
			foreach ($this->bans as $mask => $ban)
				$ban->delete();
			
			foreach ($this->levels as $user_id => $access)
				$access->delete();
		}
		
	}
	

