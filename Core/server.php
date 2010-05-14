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

	// Server mode flags
	
	$SERVER_FLAGS = array(
		'h' => array( 'const' => 'SMODE_HUB',          'uint' => 0x0001 ),
		's' => array( 'const' => 'SMODE_SERVICE',      'uint' => 0x0002 ),
		'6' => array( 'const' => 'SMODE_IPV6',         'uint' => 0x0004 )
	);


	class Server
	{
		var $numeric;
		var $name;
		var $desc;
		var $modes;
		var $start_ts;
		var $max_users;
		var $uplink;
		var $users = array();
		
		function __construct( $uplink, $num, $name, $desc, $start_ts, $max_users, $modes = "" )
		{
			$this->uplink = $uplink;
			$this->numeric = $num;
			$this->name = $name;
			$this->desc = $desc;
			$this->add_modes( $modes );
			$this->start_ts = $start_ts;
			$this->max_users = $max_users;
		}
		
		function is_jupe()             { return false; }
		function is_service()          { return $this->has_mode('s'); }
		function is_hub()              { return $this->has_mode('h'); }
		function is_ipv6()             { return $this->has_mode('6'); }
		function get_uplink_numeric()  { return $this->uplink; }
		function get_name()            { return $this->name; }
		function get_numeric()         { return $this->numeric; }
		function get_desc()            { return $this->desc; }
		function get_start_ts()        { return $this->start_ts; }
		function get_max_users()       { return $this->max_users; }
		
		function get_name_abbrev( $max_len = 0 )
		{
			$name = $this->get_name();
			$ppos = strpos( $name, "." );
			
			if($max_len == 0)
				$max_len = strlen($name) + 1;

			if($ppos == -1 || $ppos > $max_len)
				$name = substr($name, 0, $max_len - 1) ."*";
			else 
				$name = substr($name, 0, $ppos + 1) ."*";
			
			return $name;
		}
		
		
		static function is_valid_mode( $mode )
		{
			global $SERVER_FLAGS;
			return in_array( $mode, $SERVER_FLAGS );
		}
		
		static function is_valid_mode_int( $mode )
		{
			global $SERVER_FLAGS;
			foreach( $SERVER_FLAGS as $c => $i )
				if( $i['uint'] == $mode )
					return true;
			
			return false;
		}

		function add_modes( $str )
		{
			global $SERVER_FLAGS;
			foreach( $SERVER_FLAGS as $c => $i )
				if( strpos($str, $c) !== false ) $this->add_mode( $i['uint'] );
		}
		
		function add_mode( $mode )
		{
			global $SERVER_FLAGS;
			if( !is_int($mode) )
				return $this->add_mode( $SERVER_FLAGS[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && !$this->has_mode($mode) )
				$this->modes |= $mode;
		}
		
		function remove_mode( $mode )
		{
			global $SERVER_FLAGS;
			if( !is_int($mode) )
				return $this->remove_mode( $SERVER_FLAGS[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && $this->has_mode($mode) )
				$this->modes &= ~$mode;
		}

		function has_mode( $mode )
		{
			global $SERVER_FLAGS;
			if( !is_int($mode) )
				return $this->has_mode( $SERVER_FLAGS[$mode]['uint'] );
			return( ($this->modes & $mode) == $mode );
		}
		
		function add_user( $numeric )
		{
			$this->users[] = $numeric;
		}
		
		function remove_user( $numeric )
		{
			$users = array();
			
			foreach( $this->users as $u )
			{
				if( $u == $numeric )
					continue;
				
				$users[] = $u;
			}
			
			$this->users = $users;
		}
	}


	foreach( $SERVER_FLAGS as $c => $i )
	{
		if( !defined($i['const']) )
			define( $i['const'], $i['uint'] );
	}
	


