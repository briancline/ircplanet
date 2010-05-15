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

	require_once( CORE_DIR .'/channeluser.php' );
	require_once( CORE_DIR .'/ban.php' );
	
	// Channel mode flags
	$CHANNEL_MODES = array(
		's' => array( 'const' => 'CMODE_SECRET',       'uint' => 0x0001 ),
		'p' => array( 'const' => 'CMODE_PRIVATE',      'uint' => 0x0002 ),
		'm' => array( 'const' => 'CMODE_MODERATE',     'uint' => 0x0004 ),
		't' => array( 'const' => 'CMODE_TOPICOPONLY',  'uint' => 0x0008 ),
		'i' => array( 'const' => 'CMODE_INVITE',       'uint' => 0x0010 ),
		'n' => array( 'const' => 'CMODE_NOEXTMSG',     'uint' => 0x0020 ),
		'r' => array( 'const' => 'CMODE_REGONLY',      'uint' => 0x0040 ),
		'l' => array( 'const' => 'CMODE_LIMIT',        'uint' => 0x0080 ),
		'k' => array( 'const' => 'CMODE_KEY',          'uint' => 0x0100 ),
		'R' => array( 'const' => 'CMODE_REGISTERED',   'uint' => 0x0200 ),
		'D' => array( 'const' => 'CMODE_DELJOINS',     'uint' => 0x0400 ),
		'd' => array( 'const' => 'CMODE_DELJOINSPEND', 'uint' => 0x0800 ),
		'U' => array( 'const' => 'CMODE_USERPASS',     'uint' => 0x1000 ),
		'A' => array( 'const' => 'CMODE_ADMINPASS',    'uint' => 0x2000 )
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
		
		function __construct( $name, $ts, $modes = "", $key = "", $limit = 0 )
		{
			$this->name = $name;
			$this->ts = $ts;
			$this->add_modes( $modes );
			$this->key = $key;
			$this->limit = $limit;
		}
		
		function __toString()       { return $this->name; }
		
		function get_name()         { return $this->name; }
		function get_topic()        { return $this->topic; }
		function get_ts()           { return $this->ts; }
		function get_user_count()   { return count( $this->users ); }
		function get_limit()        { return $this->limit; }
		function get_key()          { return $this->key; }
		function get_admin_pass()   { return $this->admin_pass; }
		function get_user_pass()    { return $this->user_pass; }
		
		function is_secret()        { return $this->has_mode(CMODE_SECRET); }
		
		function set_ts($v)         { $this->ts = $v; }
		function set_name($v)       { $this->name = $v; }
		function set_key($v)        { $this->key = $v; }
		function set_admin_pass($v) { $this->admin_pass = $v; }
		function set_user_pass($v)  { $this->user_pass = $v; }
		function set_modes($v)      { $this->modes = 0; $this->add_modes($v); }
		function set_topic($v)      { $this->topic = $v; }
		function set_limit($v) { 
			$this->limit = $v;
			
			if($v <= 0)
				$this->remove_mode(CMODE_LIMIT); 
			else 
				$this->add_mode(CMODE_LIMIT);
		}
		
		function get_op_list()
		{
			$ops = array();
			foreach( $this->users as $numeric => $chan_user )
				if( $chan_user->is_op() )
					$ops[] = $numeric;
			
			return $ops;
		}
		
		function get_voice_list()
		{
			$voices = array();
			foreach( $this->users as $numeric => $chan_user )
				if( $chan_user->is_voice() )
					$voices[] = $numeric;
			
			return $voices;
		}

		function get_op_count()
		{
			$count = 0;
			foreach( $this->users as $numeric => $chan_user )
				if( $chan_user->is_op() )
					$count++;
			
			return $count;
		}
		
		
		function get_voice_count()
		{
			$count = 0;
			foreach( $this->users as $numeric => $chan_user )
				if( $chan_user->is_voice() )
					$count++;
			
			return $count;
		}
		
		static function is_valid_mode( $mode )
		{
			global $CHANNEL_MODES;
			return in_array( $mode, $CHANNEL_MODES );
		}
		
		static function is_valid_mode_int( $mode )
		{
			global $CHANNEL_MODES;
			foreach( $CHANNEL_MODES as $c => $i )
				if( $i['uint'] == $mode )
					return true;
			
			return false;
		}
		
		function has_exact_modes( $modes )
		{
			global $CHANNEL_MODES;

			$imodes = 0;
			foreach( $CHANNEL_MODES as $c => $i )
				if( strpos($modes, $c) !== false ) $imodes |= $i['uint'];
			
			return( $imodes == $this->modes );
		}
		

		function add_modes( $str )
		{
			global $CHANNEL_MODES;
			foreach( $CHANNEL_MODES as $c => $i )
				if( strpos($str, $c) !== false ) $this->add_mode( $i['uint'] );
		}
		
		function remove_modes( $str )
		{
			global $CHANNEL_MODES;
			foreach( $CHANNEL_MODES as $c => $i )
				if( strpos($str, $c) !== false ) $this->remove_mode( $i['uint'] );
		}
		
		function add_mode( $mode )
		{
			global $CHANNEL_MODES;
			if( !is_int($mode) && isset($CHANNEL_MODES[$mode]) )
				return $this->add_mode( $CHANNEL_MODES[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && !$this->has_mode($mode) )
			{
				if( $mode == CMODE_SECRET ) {
					$this->remove_mode( CMODE_PRIVATE );
				}
				if( $mode == CMODE_PRIVATE ) {
					$this->remove_mode( CMODE_SECRET );
				}
				
				$this->modes |= $mode;
			}
		}
		
		function remove_mode( $mode )
		{
			global $CHANNEL_MODES;
			if( !is_int($mode) && isset($CHANNEL_MODES[$mode]) )
				return $this->remove_mode( $CHANNEL_MODES[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && $this->has_mode($mode) )
			{
				if( $mode == CMODE_KEY )
					$this->key = '';
				if( $mode == CMODE_LIMIT )
					$this->limit = 0;
				if( $mode == CMODE_ADMINPASS )
					$this->admin_pass = '';
				if( $mode == CMODE_USERPASS )
					$this->user_pass = '';
				
				$this->modes &= ~$mode;
			}
		}
		
		function has_mode( $mode )
		{
			global $CHANNEL_MODES;
			if( !is_int($mode) )
				return $this->has_mode( $CHANNEL_MODES[$mode]['uint'] );
			
			return( ($this->modes & $mode) == $mode );
		}
		
		function get_modes()
		{
			global $CHANNEL_MODES;

			$modes = '';
			$params = array();
			
			foreach( $CHANNEL_MODES as $c => $i )
			{
				if( $this->has_mode($c) )
				{
					$modes .= $c;
					if( $c == 'l' )  $params[] = $this->get_limit();
					if( $c == 'k' )  $params[] = $this->get_key();
					if( $c == 'A' )  $params[] = $this->get_admin_pass();
					if( $c == 'U' )  $params[] = $this->get_user_pass();
				}
			}
			
			if(!empty($params))
				$modes .= ' '. join(' ', $params);
			
			return $modes;
		}
		
		function clear_modes()
		{
			$this->modes = 0;
		}
		
		function clear_user_modes()
		{
			foreach( $this->users as $numeric => $user )
				$user->clear_modes();
		}
		
		function clear_ops()
		{
			foreach( $this->users as $user )
			{
				if( $user->is_op() )
					$user->remove_mode( CUMODE_OP );
			}
		}
		
		function clear_voices()
		{
			foreach( $this->users as $user )
			{
				if( $user->is_voice() )
					$user->remove_mode( CUMODE_VOICE );
			}
		}
		
		function clear_bans()
		{
			$this->bans = array();
		}
		

		
		function add_ban( $mask, $ts = 0, $setby = "" )
		{
			if( $ts == 0 )
				$ts = time();

			foreach( $this->bans as $ban_mask => $ban )
			{
				$tmp_mask = strtolower( $ban_mask );
				if( fnmatch($mask, $tmp_mask) )
					unset( $this->bans[$ban_mask] );
			}
			
			$this->bans[$mask] = new Ban( $mask, $ts, $setby );
		}
		
		function has_ban( $mask )
		{
			$mask = strtolower( $mask );
			return array_key_exists( $mask, $this->bans );
		}
		
		function get_matching_bans( $mask = '*' )
		{
			$mask = strtolower( $mask );
			$matches = array();
			
			foreach( $this->bans as $ban_mask => $ban )
			{
				$tmp_mask = strtolower( $ban_mask );
				if( $mask == '*' || fnmatch($tmp_mask, $mask) )
					$matches[] = $ban_mask;
			}
			
			if( count($matches) > 0 )
				return $matches;
			
			return false;
		}
		
		function get_inferior_bans( $mask )
		{
			$mask = strtolower( $mask );
			$matches = array();
			
			foreach( $this->bans as $ban_mask => $ban )
			{
				$tmp_mask = strtolower( $ban_mask );
				if( fnmatch($mask, $tmp_mask) )
					$matches[] = $ban_mask;
			}
			
			if( count($matches) > 0 )
				return $matches;
			
			return false;
		}
		
		function remove_ban( $mask )
		{
			unset( $this->bans[$mask] );
		}
		
		
		
		function add_user( $numeric, $modes )
		{
			$oplevel = 0;
			for($i = 0; $i < strlen($modes); $i++)
			{
				if( is_numeric($modes[$i]) )
				{
					$omodes = $modes;
					$oplevel = substr( $modes, $i );
					$modes = substr( $modes, 0, $i );
					$modes .= 'o';
					break;
				}
			}
			
			$this->users[$numeric] = new ChannelUser( $numeric, $modes, $oplevel );
		}
		
		function remove_user( $numeric )
		{
			unset( $this->users[$numeric] );
		}
		
		function add_op( $numeric )         { if($this->is_on($numeric)) $this->users[$numeric]->add_mode( CUMODE_OP ); }
		function remove_op( $numeric )      { if($this->is_on($numeric)) $this->users[$numeric]->remove_mode( CUMODE_OP ); }
		function add_voice( $numeric )      { if($this->is_on($numeric)) $this->users[$numeric]->add_mode( CUMODE_VOICE ); }
		function remove_voice( $numeric )   { if($this->is_on($numeric)) $this->users[$numeric]->remove_mode( CUMODE_VOICE ); }
		
		function set_oplevel( $numeric, $level )  { if($this->is_op($numeric)) $this->users[$numeric]->set_oplevel( $level ) ; }
		
		function is_on( $numeric )     { return( array_key_exists($numeric, $this->users) ); }
		function is_op( $numeric )     { return( $this->is_on($numeric) && $this->users[$numeric]->is_op() ); }
		function is_voice( $numeric )  { return( $this->is_on($numeric) && $this->users[$numeric]->is_voice() ); }
		
		
		function get_user_list()
		{
			$users = array();
			foreach( $this->users as $numeric => $chan_user )
				$users[] = $numeric;
			
			return $users;
		}
		
		function get_burst_userlist()
		{
			$userlist = '';
			foreach( $this->users as $numeric => $obj )
			{
				if( substr($numeric, 0, BASE64_SERVLEN) != SERVER_NUM )
					continue;
				
				if( !empty($userlist) )
					$userlist .= ',';
				
				$userlist .= $numeric;
				$modes = $obj->get_modes();
				
				if( !empty($modes) )
					$userlist .= ':'. $modes;
			}
			
			return $userlist;
		}
		
		function get_burst_banlist()
		{
			$banlist = '';
			foreach( $this->bans as $ban )
			{
				if( !empty($banlist) )
					$banlist .= ' ';
				
				$banlist .= $ban->mask;
			}
			
			return $banlist;
		}
	}
	
	
	foreach( $CHANNEL_MODES as $c => $i )
	{
		if( !defined($i['const']) )
			define( $i['const'], $i['uint'] );
	}
	
	

