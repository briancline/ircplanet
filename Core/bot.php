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
	
	require_once('p10.php');
	require_once('user.php');

	class Bot extends User
	{
		var $net;
		
		function __construct($num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes = "", &$instance)
		{
			$this->numeric = $num;
			$this->nick = $nick;
			$this->ident = $ident;
			$this->host = $host;
			$this->ip = $ip;
			$this->start_ts = $start_ts;
			$this->desc = $desc;
			$this->addModes($modes);
			$this->net = $instance;
		}
		
		function isBot() { return true; }
		
		function message($target, $text)
		{
			if (is_object($target) && isUser($target))
				$target = $target->getNumeric();
			
			$this->net->sendf(FMT_PRIVMSG, $this->numeric, $target, $text);
			$this->last_spoke = time();
		}

		function notice($target, $text)
		{
			if (is_object($target) && isUser($target))
				$target = $target->getNumeric();
			
			$this->net->sendf(FMT_NOTICE, $this->numeric, $target, $text);
		}

		function messagef($target, $format)
		{
			$args = func_get_args();
			$target = array_shift($args);
			$format = array_shift($args);
			
			if (is_object($target) && isUser($target))
				$target = $target->getNumeric();
			
			$notice_text = irc_vsprintf($format, $args);
			$this->net->sendf(FMT_PRIVMSG, $this->numeric, $target, $notice_text);
			$this->last_spoke = time();
		}
		
		function noticef($target, $format)
		{
			$args = func_get_args();
			$target = array_shift($args); // Remove target 
			$format = array_shift($args); // Remove format
			
			if (is_object($target) && isUser($target))
				$target = $target->getNumeric();
			
			$notice_text = irc_vsprintf($format, $args);
			$this->net->sendf(FMT_NOTICE, $this->numeric, $target, $notice_text);
		}
		
		function sendSyntax($target, $command)
		{
			$this->noticef($target, "%sSyntax:%s %s %s",
				BOLD_START, 
				BOLD_END,
				$command,
				$this->net->getCommandSyntax($command)
			);
		}
		
		function sendNoaccess($target)
		{
			$this->notice($target, "You do not have sufficient permissions to use that command.");
		}
		
		function op($chan_name, $num_list)       { return $this->net->performChanUserMode($this->getNumeric(), $chan_name, '+', 'o', $num_list); }
		function deop($chan_name, $num_list)     { return $this->net->performChanUserMode($this->getNumeric(), $chan_name, '-', 'o', $num_list); }
		function voice($chan_name, $num_list)    { return $this->net->performChanUserMode($this->getNumeric(), $chan_name, '+', 'v', $num_list); }
		function devoice($chan_name, $num_list)  { return $this->net->performChanUserMode($this->getNumeric(), $chan_name, '-', 'v', $num_list); }
		function ban($chan_name, $num_list)      { return $this->net->performChanUserMode($this->getNumeric(), $chan_name, '+', 'b', $num_list); }
		function unban($chan_name, $num_list)    { return $this->net->performChanUserMode($this->getNumeric(), $chan_name, '-', 'b', $num_list); }


		function invite($nick, $chan_name)
		{
			$this->net->sendf(FMT_INVITE, $this->getNumeric(), $nick, $chan_name);
		}
		
		
		function topic($chan_name, $topic, $chan_ts = 0)
		{
			$chan = $this->net->getChannel($chan_name);
			if (!$chan)
				return;
			
			if($chan_ts == 0)
				$chan_ts = $chan->getTs();
			
			$this->net->sendf(FMT_TOPIC, $this->getNumeric(), $chan_name, $chan_ts, time(), $topic);
		}
		
		
		function clearModes($chan_name)
		{
			$chan = $this->net->getChannel($chan_name);
			$this->net->sendModeLine(sprintf(FMT_MODE, $this->getNumeric(), $chan_name, '-psmntilkrD *', $chan->getTs()));
		}

		function mode($chan_name, $modes)
		{
			$this->net->sendMode($this, $chan_name, $modes);
		}
		
		function kick($chan_name, $numeric, $reason)
		{
			$this->net->removeChannelUser($chan_name, $numeric);
			$this->net->sendf(FMT_KICK, $this->getNumeric(), $chan_name, $numeric, $reason);
		}
		
		function join($chan_name, $create_ts = 0)
		{
			$chan = $this->net->getChannel($chan_name);

			if (!$chan) {
				if ($create_ts == 0)
					$create_ts = time();
				
				$this->net->sendf(FMT_CREATE, $this->getNumeric(), $chan_name, $create_ts);
				$this->net->addChannel($chan_name, $create_ts);

				$chan = $this->net->getChannel($chan_name);
				$chan->addUser($this->getNumeric(), 'o');
			}
			else {
				$this->net->sendf(FMT_JOIN, $this->getNumeric(), $chan_name, $chan->getTs());
				$this->net->addChannelUser($chan_name, $this->getNumeric());
			}
		}

		function part($chan_name, $reason = "")
		{
			if (empty($reason))
				$this->net->sendf(FMT_PART, $this->getNumeric(), $chan_name);
			else 
				$this->net->sendf(FMT_PART_REASON, $this->getNumeric(), $chan_name, $reason);
			
			$this->net->removeChannelUser($chan_name, $this->getNumeric());
		}
	
		function kill($user_num, $reason = 'So long...')
		{
			if (isUser($user_num))
				$user_num = $user_num->getNumeric();
			
			if (!($user = $this->net->getUser($user_num)))
				return false;
			
			$my_serv = $this->net->getServer($this->getServerNumeric());
			$this->net->sendf(FMT_KILL, $this->getNumeric(), $user_num, 
				$this->getNick(), $reason);
			$this->net->removeUser($user_num);
		}
	}


