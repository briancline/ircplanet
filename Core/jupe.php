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

	class Jupe
	{
		var $server;
		var $expire_ts;
		var $last_mod;
		var $reason;
		var $active;
		
		function __construct( $server, $duration, $last_mod, $reason )
		{
			$this->server = $server;
			$this->expire_ts = time() + $duration;
			$this->last_mod = $last_mod;
			$this->reason = $reason;
			$this->active = true;
		}
		
		function get_server()        { return $this->server; }
		function get_expire_ts()     { return $this->expire_ts; }
		function get_duration()      { return $this->expire_ts - time(); }
		function get_last_mod()      { return $this->last_mod; }
		function get_reason()        { return $this->reason; }
		function is_expired()        { return (time() >= $this->expire_ts); }
		function is_active()         { return $this->active; }

		function set_duration($n)    { $this->expire_ts = time() + $n; }
		function set_last_mod($n)    { $this->last_mod = $n; }
		function set_reason($s)      { $this->reason = $s; }
		function activate()          { $this->active = true; }
		function deactivate()        { $this->active = false; }
		
		function __toString()        { return $this->server; }

		function expire_now()
		{
			$this->active = false;
			$this->expire_ts = time();
			$this->last_mod = time();
		}
	}


