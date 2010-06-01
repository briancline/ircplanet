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

	$ts = $args[3];
	$chan_key = strtolower($args[2]);
	$modes = '';
	$key = '';
	$admin_pass = '';
	$user_pass = '';
	$limit = 0;
	$has_banlist = $args[$num_args - 1][0] == '%';
	$userlist_pos = 4;
	$cleared_local_modes = false;

	/**
	 * AE B #log.oracle 1131900616
	 * AE B #testchan 1131291938 AEBFo:o
	 * AE B #support 1105674755 +tnl 14 AEBFh,M[AAC:ov
	 * AE B #opers 1100986985 +smtin M[AAD:o
	 * AE B #coder-com 1113336997 +tn AEBFh:o,M[AAC :%*!*user@*.fucker.com
	 * AE B #coder-com 1113336997 :%*!*more@*.bans.com
	 * AE B #testchan 1131291938 +stinlk 69 w00t3rz AEBFo:o
	 */
	
	$has_modes = ($args[4][0] == '+');
	if($has_modes)
	{
		$userlist_pos++;
		$modes_pos = 4;
		
		if(preg_match('/l/', $args[$modes_pos]))
			$limit = $args[$userlist_pos++];
		if(preg_match('/k/', $args[$modes_pos]))
			$key = $args[$userlist_pos++];
		if(preg_match('/A/', $args[$modes_pos]))
			$admin_pass = $args[$userlist_pos++];
		if(preg_match('/U/', $args[$modes_pos]))
			$user_pass = $args[$userlist_pos++];
		
		$modes = $args[$modes_pos];
	}

	if(($chan = $this->get_channel($chan_key)))
	{
		if($ts < $chan->get_ts())
		{
			$chan->clear_bans();
			$chan->clear_modes();
			$chan->clear_user_modes();

			$chan->set_name($chan_name);
			$chan->set_ts($ts);
			
			$cleared_local_modes = true;
		}
		
		$chan->add_modes($modes);
		$chan->set_limit($limit);
		$chan->set_key($key);
		$chan->set_admin_pass($admin_pass);
		$chan->set_user_pass($user_pass);
	}
	else
	{
		$chan = $this->add_channel($chan_name, $ts, $modes, $key, $limit);
		$chan->set_admin_pass($admin_pass);
		$chan->set_user_pass($user_pass);
	}
	
	/**
	 * ircu might not send a user list with a burst line (for instance, if it's
	 * breaking up tons of bans across multiple lines), so account for that here.
	 */
	$userlist = array();
	$has_userlist = ($userlist_pos < ($num_args - 1) || (!$has_banlist && $userlist_pos == ($num_args - 1)));
	if($has_userlist)
	{
		$userlist = explode(',', $args[$userlist_pos]);
		
		foreach($userlist as $user)
		{
			$user_modes = '';
			$numeric = substr($user, 0, 5);
			if(strlen($user) > 5)
				$user_modes = substr($user, 6);
			
			$this->add_channel_user($chan_name, $numeric, $user_modes);
		}
	}
	
	$banlist = array();
	$banlist_pos = $num_args - 1;
	if($has_banlist)
	{
		// skip the % character
		$ban_string = substr($args[$banlist_pos], 1);
		$banlist = explode(' ', $ban_string);
		
		foreach($banlist as $ban)
			$chan->add_ban($ban);
	}
	
	$user_count = count($userlist);
	$ban_count = count($banlist);


