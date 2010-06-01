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

	class Timer
	{
		var $recurring;
		var $ts;
		var $include_file;
		var $ts_last_run;
		
		var $data = array();
		
		function __construct($recurring, $ts, $include_file, $data = 0)
		{
			$this->recurring = $recurring;
			$this->ts = $ts;
			$this->ts_last_run = time();
			$this->include_file = $include_file;
			
			if (!$recurring)
				$this->ts += time();
			
			if ($data > 0)
				$this->data = $data;
		}
		
		function is_recurring()        { return $this->recurring; }
		function get_interval()        { return $this->ts; }
		function get_include()         { return $this->include_file; }
	
		function get_data_elements()
		{
			if (!is_array($this->data))
				return array($this->data);
			
			return $this->data;
		}


		function get_next_run()
		{
			if ($this->is_recurring())
				return $this->ts_last_run + $this->ts;
			else
				return $this->ts;
		}

		
		function update()              { $this->ts_last_run = time(); }
		function set_data_elements($v)
		{
			if (!is_array($v))
				$v = array($v);
			
			$this->data = $v; 
		}
	}


