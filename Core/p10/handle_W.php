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

	$nick = $args[3];
	$source_num = $args[0];
	$source = $this->get_user($source_num);
	
	if (!($target = $this->get_user_by_nick($nick))) {
		$this->sendf(FMT_WHOIS_NOTFOUND, SERVER_NUM, $source_num, $nick);
		return false;
	}
	
	$t_num = $target->get_numeric();
	$t_host = $target->get_host_safe();
	$t_chans = '';
	
	if (!$target->is_service()) {
		$chans = array();
		foreach ($target->get_channel_list() as $chan_key) {
			$chan = $this->get_channel($chan_key);
			$prefix = '';
			
			if ($chan->is_secret() && !$chan->is_on($source_num) && !$source->is_oper())
				continue;
			
			if ($target->is_deaf())
				$prefix .= '-';
			if ($chan->is_op($t_num))
				$prefix .= '@';
			elseif ($chan->is_voice($t_num))
				$prefix .= '+';
			
			$chans[] = $prefix . $chan->get_name(); 
		}
		
		$t_chans = join(' ', $chans);
	}
	
	
	$server = $this->get_server($target->get_server_numeric());
	
	$this->sendf(FMT_WHOIS_USER, SERVER_NUM, $source_num, 
		$target->get_nick(),
		$target->get_ident(),
		$t_host,
		$target->get_name());
	
	if (!empty($t_chans)) {
		$this->sendf(FMT_WHOIS_CHANNELS, SERVER_NUM, $source_num,
			$target->get_nick(),
			$t_chans);
	}
	
	$this->sendf(FMT_WHOIS_SERVER, SERVER_NUM, $source_num,
		$target->get_nick(),
		$server->get_name(),
		$server->get_desc());
	
	if ($target->is_away()) {
		$this->sendf(FMT_WHOIS_AWAY, SERVER_NUM, $source_num,
			$target->get_nick(), $target->get_away());
	}
	
	if ($target->is_oper()) {
		$this->sendf(FMT_WHOIS_OPER, SERVER_NUM, $source_num, 
			$target->get_nick());
	}

	if ($target->is_logged_in()) {
		$this->sendf(FMT_WHOIS_ACCOUNT, SERVER_NUM, $source_num,
			$target->get_nick(), $target->get_account_name());
	}
	
	if ($target->is_host_hidden() && ($target->has_fakehost() || $target->is_logged_in()) 
			&& ($source->is_service() || $source->is_oper() || $source == $target))
	{
		$this->sendf(FMT_WHOIS_REALHOST, SERVER_NUM, $source_num,
			$target->get_nick(), $target->get_ident(), $target->get_host(), 
			$target->get_ip());
	}
	
	if ($target->is_local()) {
		$this->sendf(FMT_WHOIS_IDLE, SERVER_NUM, $source_num,
			$target->get_nick(),
			$target->get_idle_time(),
			$target->get_signon_ts());
	}

	$this->sendf(FMT_WHOIS_END, SERVER_NUM, $source_num, 
		$target->get_nick());
	

