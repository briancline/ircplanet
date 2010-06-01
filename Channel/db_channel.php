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
		protected $strict_modes = 0;
		protected $strict_topic = 0;
		protected $no_op = 0;
		protected $no_voice = 0;
		
		protected $levels = array();
		protected $bans = array();
		
		function record_construct($args)
		{
			if(count($args) > 1)
			{
				$name = $args[0];
				$owner_id = $args[1];
			}
			
			if(!empty($name))
			{
				$this->name = $name;
				$this->register_ts = time();
				$this->register_date = db_date();
			}
			
			if(isset($owner_id) && $owner_id > 0)
			{
				$this->save();
				$owner = new DB_Channel_Access();
				$owner->set_chan_id($this->channel_id);
				$owner->set_user_id($owner_id);
				$owner->set_level(500);
				$this->add_access($owner);
			}
		}
		
		function record_destruct()
		{
		}
		

		function has_default_topic()       { return !empty($this->def_topic); }
		function has_default_modes()       { return !empty($this->def_modes); }
		function shows_info_lines()        { return 1 == $this->info_lines; }
		function is_suspended()            { return 1 == $this->suspend; }
		function is_permanent()            { return 1 == $this->no_purge; }
		function auto_ops()                { return 1 == $this->auto_op; }
		function auto_ops_all()            { return 1 == $this->auto_op_all; }
		function auto_voices()             { return 1 == $this->auto_voice; }
		function auto_voices_all()         { return 1 == $this->auto_voice_all; }
		function auto_limits()             { return 1 == $this->auto_limit; }
		function strict_ops()              { return 1 == $this->strict_op; }
		function strict_voices()           { return 1 == $this->strict_voice; }
		function strict_modes()            { return 1 == $this->strict_modes; }
		function strict_topic()            { return 1 == $this->strict_topic; }
		function no_ops()                  { return 1 == $this->no_op; }
		function no_voices()               { return 1 == $this->no_voice; }
		
		function get_id()                  { return $this->channel_id; }
		function get_name()                { return $this->name; }
		function get_register_ts()         { return $this->register_ts; }
		function get_create_ts()           { return $this->create_ts; }
		function get_purpose()             { return $this->purpose; }
		function get_url()                 { return $this->url; }
		function get_default_topic()       { return $this->def_topic; }
		function get_default_modes()       { return $this->def_modes; }
		function get_auto_limit_buffer()   { return $this->auto_limit_buffer; }
		function get_auto_limit_wait()     { return $this->auto_limit_wait; }
		
		function get_levels()              { return $this->levels; }
		
		function has_pending_autolimit()   { return isset($this->_alimit_pending) && $this->_alimit_pending; }
		function set_pending_autolimit($b) { $this->_alimit_pending = $b; }
		
		function set_create_ts($n)         { $this->create_ts = $n; }
		function set_register_date($d)     { $this->register_date = $d; }
		function set_purpose($s)           { $this->purpose = $s; }
		function set_url($s)               { $this->url = $s; }
		function set_default_topic($s)     { $this->def_topic = $s; }
		function set_default_modes($s)     { $this->def_modes = $s; }
		function set_info_lines($b)        { $this->info_lines = $b ? 1 : 0; }
		function set_suspend($b)           { $this->suspend = $b ? 1 : 0; }
		function set_permanent($b)         { $this->no_purge = $b ? 1 : 0; }
		function set_auto_op($b)           { $this->auto_op = $b ? 1 : 0; }
		function set_auto_op_all($b)       { $this->auto_op_all = $b ? 1 : 0; }
		function set_auto_voice($b)        { $this->auto_voice = $b ? 1 : 0; }
		function set_auto_voice_all($b)    { $this->auto_voice_all = $b ? 1 : 0; }
		function set_auto_limit($b)        { $this->auto_limit = $b ? 1 : 0; }
		function set_auto_limit_buffer($n) { $this->auto_limit_buffer = $n; }
		function set_auto_limit_wait($n)   { $this->auto_limit_wait = $n; }
		function set_strict_op($b)         { $this->strict_op = $b ? 1 : 0; }
		function set_strict_voice($b)      { $this->strict_voice = $b ? 1 : 0; }
		function set_strict_modes($b)      { $this->strict_modes = $b ? 1 : 0; }
		function set_strict_topic($b)      { $this->strict_topic = $b ? 1 : 0; }
		function set_no_op($b)             { $this->no_op = $b ? 1 : 0; }
		function set_no_voice($b)          { $this->no_voice = $b ? 1 : 0; }
		
		
		function add_access($access_obj)
		{
			$user_id = $access_obj->get_user_id();
			$this->levels[$user_id] = $access_obj;
		}
		
		function remove_access($user_id)
		{
			if(array_key_exists($user_id, $this->levels))
			{
				$this->levels[$user_id]->delete();
				unset($this->levels[$user_id]);
			}
		}
		
		
		function get_level_by_id($user_id)
		{
			if(array_key_exists($user_id, $this->levels))
				return $this->levels[$user_id]->get_level();
			
			return 0;
		}
		
		
		function add_ban($ban_obj)
		{
			$mask = strtolower($ban_obj->get_mask());
			$this->bans[$mask] = $ban_obj;
		}
		
		function get_ban($mask)
		{
			$mask = strtolower($mask);
			if(array_key_exists($mask, $this->bans))
				return $this->bans[$mask];
			
			return false;
		}
		
		function remove_ban($mask)
		{
			$mask = strtolower($mask);
			if(array_key_exists($mask, $this->bans))
			{
				$this->bans[$mask]->delete();
				unset($this->bans[$mask]);
			}
		}
		
		function clear_bans()
		{
			foreach($this->bans as $mask => $ban)
				$ban->delete();

			$this->bans = array();
		}
		
		function count_matching_bans($mask)
		{
			if(is_object($mask))
				return $this->count_matching_bans($mask->get_full_mask());
			
			$match_count = 0;
			$mask = strtolower($mask);
			
			foreach($this->bans as $mask_iter => $ban)
			{
				if(fnmatch($mask_iter, $mask))
					$match_count++;
			}
			
			return $match_count;
		}
		
		function has_matching_bans($mask)
		{
			if(is_object($mask))
				return $this->has_matching_bans($mask->get_full_mask());
			
			$mask = strtolower($mask);
			
			foreach($this->bans as $mask_iter => $ban)
			{
				if(fnmatch($mask_iter, $mask))
					return true;
			}
			
			return false;
		}
		
		function get_matching_ban($mask)
		{
			if(is_object($mask))
				return $this->get_matching_ban($mask->get_full_mask());
			
			$mask = strtolower($mask);
			
			foreach($this->bans as $mask_iter => $ban)
			{
				if(fnmatch($mask_iter, $mask))
					return $ban;
			}
			
			return false;
		}
		
		
		function get_matching_bans($mask = '*')
		{
			if(is_object($mask))
				return $this->get_matching_bans($mask->get_full_mask());
			
			$matches = array();
			$mask = strtolower($mask);
			
			foreach($this->bans as $mask_iter => $ban)
			{
				if($mask == '*' || fnmatch($mask, $mask_iter))
					$matches[$mask_iter] = $ban;
			}
			
			if(count($matches) == 0)
				return false;
			
			return $matches;
		}
		
		
		function record_save()
		{
			foreach($this->levels as $user_id => $access)
			{
				if(empty($user_id) || $user_id == 0 || $this->channel_id == 0)
					continue;
				
				$access->save();
			}
			
			foreach($this->bans as $mask => $ban)
			{
				if(empty($mask) || !is_ban_record($ban) || $this->channel_id == 0)
					continue;
				
				$ban->save();
			}
		}
		
		
		function record_delete()
		{
			foreach($this->bans as $mask => $ban)
				$ban->delete();
			
			foreach($this->levels as $user_id => $access)
				$access->delete();
		}
		
	}
	

