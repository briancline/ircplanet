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
	
	$count = 0;
	$matches = array();

	$mask = strtolower($pargs[1]);
	
	$max_user_len = 14;
	$max_server_len = 9;
	
	foreach ($this->users as $numeric => $tmp_user) {
		$ip = $tmp_user->get_ip();
		$ip_mask = $tmp_user->get_ident() .'@'. $ip;
		$host_mask = $tmp_user->get_ident() .'@'. $tmp_user->get_host();
		
		if (fnmatch($mask, $host_mask) || fnmatch($mask, $ip_mask)) {
			$user_host = $tmp_user->get_nick() .'!'. $host_mask;
			$user_len = strlen($user_host);
			
			$server = $this->get_server($tmp_user->get_server_numeric());
			$server = $server->get_name_abbrev();
			$server_len = strlen($server);
			
			if ($max_user_len < $user_len)
				$max_user_len = $user_len;
			if ($max_server_len < $server_len)
				$max_server_len = $server_len;
			
			$matches[] = array(
				'user' => $user_host,
				'ip' => $ip,
				'server' => $server
			);
		}
	}
	
	$format = "%-". $max_user_len ."s     %-15s     %-". $max_server_len ."s";
	
	$bot->noticef($user, $format, 'USER HOST MASK', 'IP ADDRESS', 'ON SERVER');
	$bot->noticef($user, str_repeat('-', $max_user_len + $max_server_len + 25));
	
	foreach ($matches as $match)
		$bot->noticef($user, $format, $match['user'], $match['ip'], $match['server']);
	
	$bot->noticef($user, '%d matches found.', count($matches));
	

