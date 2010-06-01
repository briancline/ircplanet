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
	
	function convert_duration($dur)
	{
		$secs = 0;
		$amount = '';
		$found_unit = false;
		$dur = strtolower($dur);
		$units = array(
			'y' => 31556926,
			'w' =>   604800,
			'd' =>    86400,
			'h' =>     3600,
			'm' =>       60,
			's' =>        1
		);
		
		for($c = 0; $c < strlen($dur); ++$c)
		{
			$char = $dur[$c];
			if(is_numeric($char))
			{
				$amount .= $char;
			}
			else if(array_key_exists($char, $units))
			{
				if(empty($amount))
					return false;
				
				$found_unit = true;
				$secs += ($amount * $units[$char]);
				$amount = '';
				
				/**
				 * Enforce top-down time durations by removing units
				 * (ex., 5w2d is valid, 2d5w is invalid, 2d4d is invalid)
				 */
				foreach($units as $key => $val)
				{
					unset($units[$key]);
					if($key == $char)
						break;
				}
			}
			else
			{
				return false;
			}
		}
		
		if(!$found_unit)
			$secs *= 60;
		
		if($secs < 0)
			return false;
		
		return $secs;
	}
	
	
	function get_date($ts)
	{
		return date('D j M Y H:i:s', $ts);
	}



