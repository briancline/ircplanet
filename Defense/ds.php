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

	require('globals.php');
	require('../Core/service.php');
	require_once(SERVICE_DIR .'/db_whitelistentry.php');
	
	
	class DefenseService extends Service
	{
		var $pending_events = array();
		var $pending_commands = array();
		var $whitelist = array();
		
		
		function loadWhitelistEntries()
		{
			$res = db_query('select * from ds_whitelist order by whitelist_id asc');
			while ($row = mysql_fetch_assoc($res)) {
				$entry = new DB_WhitelistEntry($row);
				
				$entry_key = strtolower($entry->getMask());
				$this->whitelist[$entry_key] = $entry;
			}

			debugf('Loaded %d whitelist entries.', count($this->whitelist));
		}


		function serviceConstruct()
		{
		}
		
		
		function serviceDestruct()
		{
		}
		

		function serviceLoad()
		{
			$this->loadWhitelistEntries();
		}
		
		
		function servicePreburst()
		{
		}
		
		
		function servicePostburst()
		{
			$bot_num = $this->default_bot->getNumeric();
			foreach ($this->default_bot->channels as $chan_name) {
				$chan = $this->getChannel($chan_name);
				
				if (!$chan->isOp($bot_num))
					$this->op($chan->getName(), $bot_num);
			}
		}
		
		
		function servicePreread()
		{
		}
		

		function serviceClose($reason = 'So long, and thanks for all the fish!')
		{
			foreach ($this->users as $numeric => $user) {
				if ($user->isBot()) {
					$this->sendf(FMT_QUIT, $numeric, $reason);
					$this->removeUser($numeric);
				}
			}
		}

		
		function serviceMain()
		{
		}
		
		
		function getUserLevel($user_obj)
		{
			$acct_id = $user_obj;
			
			if (is_object($user_obj) && isUser($user_obj)) {
				if (!$user_obj->isLoggedIn())
					return 0;
				
				$acct_id = $user_obj->getAccountId();
			}
			
			$res = db_query("select `level` from `ds_admins` where user_id = ". $acct_id);
			if ($res && mysql_num_rows($res) > 0) {
				$level = mysql_result($res, 0);
				mysql_free_result($res);
				return $level;
			}
			
			return 0;
		}
		
		
		function addWhitelistEntry($mask)
		{
			$entry = new DB_WhitelistEntry();
			$entry->setMask($mask);
			$entry->save();
			
			$key = strtolower($mask);
			$this->whitelist[$key] = $entry;
			
			return $this->whitelist[$key];
		}
		
		
		function getWhitelistEntry($mask)
		{
			$key = strtolower($mask);
			if (array_key_exists($key, $this->whitelist))
				return $this->whitelist[$key];
			
			return false;
		}
		
		
		function removeWhitelistEntry($mask)
		{
			$key = strtolower($mask);
			if (!array_key_exists($key, $this->whitelist))
				return;
			
			$this->whitelist[$key]->delete();
			unset($this->whitelist[$key]);
		}
		
		
		function isWhitelisted($mask)
		{
			foreach ($this->whitelist as $entry) {
				if ($entry->matches($mask))
					return true;
			}
			
			return false;
		}
		

		function isBlacklistedDb($ip)
		{
			if (!defined('BLACK_GLINE'))
				return false;
			
			$res = db_query(sprintf(
					"select count(entry_id) FROM `ds_blacklist` WHERE `ip_address` = '%s'", 
					addslashes($ip)));
			if ($res && mysql_result($res, 0) > 0) {
				mysql_free_result($res);
				debugf('IP %s blacklisted by admin.', $ip);
				return true;
			}
			
			return false;
		}


		/**
		 * isBlacklistedDns is a generic function to provide extensibility
		 * for easily checking DNS based blacklists. It has three arguments:
		 * 	host:    The IP address of the host you wish to check.
		 * 	suffix:    The DNS suffix for the DNSBL service.
		 *    pos_resp:  An array containing responses that should be considered
		 * 	           a positive match. If not provided, will assume that ANY
		 * 	           successful DNS resolution against the DNSBL should be
		 * 	           considered a positive match.
		 * 
		 * For example:
		 * 	isBlacklistedDns('1.2.3.4', 'dnsbl.com')
		 * 		Returns true if 4.3.2.1.dnsbl.com returns any DNS resolution.
		 * 	isBlacklistedDns('1.2.3.4', 'dnsbl.com', 2)
		 * 		Returns true if 4.3.2.1.dnsbl.com contains '127.0.0.2' in its 
		 * 		response.
		 * 	isBlacklistedDns('1.2.3.4', 'dnsbl.com', array(2, 3))
		 * 		Returns true if 4.3.2.1.dnsbl.com contains either 127.0.0.2 or 
		 * 		127.0.0.3 in its response.
		 */
		function isBlacklistedDns($host, $dns_suffix, $pos_responses = -1)
		{
			// Don't waste time checking private class IPs.
			if (isPrivateIp($host))
				return false;
			
			$start_ts = microtime(true);
			
			/**
			 * DNS blacklists work by storing records for ipaddr.dnsbl.com,
			 * but with DNS all octets are reversed. So to check if 1.2.3.4
			 * is blacklisted in a DNSBL, we need to query for the hostname
			 * 4.3.2.1.dnsbl.com.
			 */
			$octets = explode('.', $host);
			$reverse_octets = implode('.', array_reverse($octets));
			$lookup_addr = $reverse_octets .'.'. $dns_suffix .'.';

			debugf('DNSBL checking %s', $lookup_addr);
			$dns_result = @dns_get_record($lookup_addr, DNS_A);

			if (count($dns_result) > 0) {
				$dns_result = $dns_result[0]['ip'];
				$resolved = true;
			}
			else {
				$dns_result = $lookup_addr;
				$resolved = false;
			}
			
			$end_ts = microtime(true);
			debugf('DNSBL check time elapsed: %0.4f seconds (%s = %s)', 
					$end_ts - $start_ts, $lookup_addr, $dns_result);
			
			// If it didn't resolve, don't check anything
			if (!$resolved)
				return false;
			
			// Check for any successful resolution
			if ($resolved && $pos_responses == -1 || empty($pos_responses))
				return true;
			
			// Check for a match against the provided string
			if (is_string($pos_responses) && !empty($pos_responses)
			 		&& $dns_result == ('127.0.0.'. $pos_responses))
				return true;
			
			// Check for a match within the provided array
			if (is_array($pos_responses)) {
				foreach ($pos_responses as $tmp_match) {
					$tmp_match = '127.0.0.'. $tmp_match;
					if ($tmp_match == $dns_result)
						return true;
				}
			}
			
			// All checks failed; host tested negative.
			return false;
		}
		
		
		function isTorHost($host)
		{
			/**
			 * The TOR DNSBL will return 127.0.0.1 as the address for a host
			 * if it is a Tor server or exit node, and 127.0.0.2 if the host
			 * is neither but one exists on the same class C subnet. We don't
			 * care if there's one on the subnet, only if the host we query
			 * for is actually a Tor server or exit node.
			 * 
			 * For more information on the TOR DNSBL, please see
			 * http://www.sectoor.de/tor.php.
			 */
			
			/**
			 * We use multiple Tor DNSBLs because sometimes you'll get a
			 * false negative if one DNSBL isn't 100% up-to-date. Rare,
			 * but not impossible.
			 */
			$blacklists = array(
				'tor.dnsbl.sectoor.de' => array(1),
				'tor.dan.me.uk'        => array(100),
				'tor.ahbl.org'         => array(2)
			);

			foreach ($blacklists as $dns_suffix => $responses) {
				if ($this->isBlacklistedDns($host, $dns_suffix, $responses))
					return true;
			}
			
			return false;
		}
		
		
		function isCompromisedHost($host)
		{
			/**
			 * To determine if a host is compromised, check a myriad of public
			 * DNSBL services (some are IRC-centric) to see if they are listed.
			 */
			$blacklists = array(
				'ircbl.ahbl.org'      => array(2),
				'dnsbl.dronebl.org'   => array(),
				'dnsbl.proxybl.org'   => array(2),
				'rbl.efnetrbl.org'    => array(1, 2, 3, 4),
				'dnsbl.swiftbl.net'   => array(2, 3, 4, 5),
				'cbl.abuseat.org'     => array(2),
				'xbl.spamhaus.org'    => array(),
				'drone.abuse.ch'      => array(2, 3, 4, 5),
				'httpbl.abuse.ch'     => array(2, 3, 4),
				'spam.abuse.ch'       => array(2)
			);
			
			foreach ($blacklists as $dns_suffix => $responses) {
				if ($this->isBlacklistedDns($host, $dns_suffix, $responses))
					return true;
			}
			
			return false;
		}
		
		
		function performGline($gline_mask, $gline_duration, $gline_reason)
		{
			if (defined('OS_GLINE') && OS_GLINE == true && defined('OS_NICK')) {
				$oper_service = $this->getUserByNick(OS_NICK);
				$gline_command = irc_sprintf('GLINE %s %s %s', 
						$gline_mask, $gline_duration, $gline_reason);

				if (!$oper_service) {
					$pending_commands[] = $gline_command;
					return;
				}

				$this->default_bot->message($oper_service, $gline_command);
			}
			else {
				$gline_secs = convertDuration($gline_duration);
				$new_gl = $this->addGline($gline_mask, $gline_secs, time(), $gline_reason);
				$this->enforceGline($new_gl);
			}
		}
	}
	
	$ds = new DefenseService();


