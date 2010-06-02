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
	
	
	class StatService extends Service
	{
		var $pending_events = array();
		
		
		function serviceConstruct()
		{
			$this->addTimer(true, 60, 'log_history.php');
		}
		
		
		function serviceDestruct()
		{
		}
		

		function serviceLoad()
		{
			db_query("delete from stats_servers");
			db_query("delete from stats_users");
			db_query("delete from stats_channels");
			db_query("delete from stats_channel_users");
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
			
			$res = db_query("select `level` from `ss_admins` where user_id = ". $acct_id);
			if ($res && mysql_num_rows($res) > 0) {
				$level = mysql_result($res, 0);
				mysql_free_result($res);
				return $level;
			}
			
			return 0;
		}
		
	}
	
	$ss = new StatService();


