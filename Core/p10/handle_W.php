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
	$source = $this->getUser($source_num);
	
	if (!($target = $this->getUserByNick($nick))) {
		$this->sendf(FMT_WHOIS_NOTFOUND, SERVER_NUM, $source_num, $nick);
		return false;
	}
	
	$t_num = $target->getNumeric();
	$t_host = $target->getHostSafe();
	$t_chans = '';
	
	if (!$target->isService()) {
		$chans = array();
		foreach ($target->getChannelList() as $chan_key) {
			$chan = $this->getChannel($chan_key);
			$prefix = '';
			
			if ($chan->isSecret() && !$chan->isOn($source_num) && !$source->isOper())
				continue;
			
			if ($target->isDeaf())
				$prefix .= '-';
			if ($chan->isOp($t_num))
				$prefix .= '@';
			elseif ($chan->isVoice($t_num))
				$prefix .= '+';
			
			$chans[] = $prefix . $chan->getName(); 
		}
		
		$t_chans = join(' ', $chans);
	}
	
	
	$server = $this->getServer($target->getServerNumeric());
	
	$this->sendf(FMT_WHOIS_USER, SERVER_NUM, $source_num, 
		$target->getNick(),
		$target->getIdent(),
		$t_host,
		$target->getName());
	
	if (!empty($t_chans)) {
		$this->sendf(FMT_WHOIS_CHANNELS, SERVER_NUM, $source_num,
			$target->getNick(),
			$t_chans);
	}
	
	$this->sendf(FMT_WHOIS_SERVER, SERVER_NUM, $source_num,
		$target->getNick(),
		$server->getName(),
		$server->getDesc());
	
	if ($target->isAway()) {
		$this->sendf(FMT_WHOIS_AWAY, SERVER_NUM, $source_num,
			$target->getNick(), $target->getAway());
	}
	
	if ($target->isOper()) {
		$this->sendf(FMT_WHOIS_OPER, SERVER_NUM, $source_num, 
			$target->getNick());
	}

	if ($target->isLoggedIn()) {
		$this->sendf(FMT_WHOIS_ACCOUNT, SERVER_NUM, $source_num,
			$target->getNick(), $target->getAccountName());
	}
	
	if ($target->isHostHidden() && ($target->hasFakehost() || $target->isLoggedIn()) 
			&& ($source->isService() || $source->isOper() || $source == $target))
	{
		$this->sendf(FMT_WHOIS_REALHOST, SERVER_NUM, $source_num,
			$target->getNick(), $target->getIdent(), $target->getHost(), 
			$target->getIp());
	}
	
	if ($target->isLocal()) {
		$this->sendf(FMT_WHOIS_IDLE, SERVER_NUM, $source_num,
			$target->getNick(),
			$target->getIdleTime(),
			$target->getSignonTs());
	}

	$this->sendf(FMT_WHOIS_END, SERVER_NUM, $source_num, 
		$target->getNick());
	

