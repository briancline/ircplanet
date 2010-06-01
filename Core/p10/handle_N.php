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
	
	if($num_args == 4)
	{
		// This is an existing user changing their nick
		$nick_change = true;
		$numeric = $args[0];
		$new_nick = $args[2];
		$old_nick = $this->users[$numeric]->get_nick();
		$this->users[$numeric]->set_nick($new_nick);
	}
	else
	{
		$nick_change = false;
		// This is a new user
		$nick = $args[2];
		$start_ts = $args[4];
		$ident = $args[5];
		$host = $args[6];
		$ip = base64_to_ip($args[$num_args - 3]);
		$numeric = $args[$num_args - 2];
		$desc = $args[$num_args - 1];
		$account = '';
		$account_ts = 0;
		$fakehost = '';
		$modes = '';
		$mode_arg = 8;
		
		if($args[7][0] == '+')
		{
			$modes = $args[7];
			
			if(preg_match('/r/', $modes))
				$account = $args[$mode_arg++];
			if(preg_match('/f/', $modes))
				$fakehost = $args[$mode_arg++];
		}
		
		if($ts_idx = strpos($account, ':'))
		{
			$account_ts = substr($account, $ts_idx + 1);
			$account = substr($account, 0, $ts_idx);

			/**
			 * Some variants of ircu also attach another instance of the signon TS
			 * to this account param, so if we find one, just trash it. No need for it.
			 */
			if($sts_idx = strpos($account_ts, ':')) {
				$account_ts = substr($account_ts, 0, $sts_idx);
			}
		}
		
		$user = $this->add_user($numeric, $nick, $ident, $host, $desc, $start_ts, $ip, $modes, $account, $account_ts);
		
		if(!empty($fakehost)) {
			$user->set_fakehost($fakehost);
		}
	}
	
	$user = $this->get_user($numeric);
	$account_name = $user->get_account_name();
	
	if($account = $this->get_account($account_name))
	{
		$user->set_account_id($account->get_id());
		$account->update_lastseen();
		$account->save();
	}
		

