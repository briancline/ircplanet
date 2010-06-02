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
	require(SERVICE_DIR .'/db_badnick.php');
	
	
	class NicknameService extends Service
	{
		var $db_badnicks = array();

		function serviceConstruct()
		{
		}
		
		
		function serviceDestruct()
		{
		}
		

		function serviceLoad()
		{
			$this->loadBadnicks();
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
		
		
		function loadBadnicks()
		{
			$res = db_query('select * from ns_badnicks order by badnick_id asc');
			while ($row = mysql_fetch_assoc($res)) {
				$badnick = new DB_BadNick($row);
				
				$badnick_key = strtolower($badnick->getMask());
				$this->db_badnicks[$badnick_key] = $badnick;
			}

			debugf('Loaded %d badnicks.', count($this->db_badnicks));
		}


		function getUserLevel($user_obj)
		{
			if (!is_object($user_obj))
				return 0;
			if (!isAccount($user_obj) && (!isUser($user_obj) || !$user_obj->isLoggedIn()))
				return 0;

			if (!isAccount($user_obj))
				$account = $this->getAccount($user_obj->getAccountName());
			else
				$account = $user_obj;
			
			$res = db_query("select `level` from `ns_admins` where user_id = ". $account->getId());
			if ($res && mysql_num_rows($res) > 0) {
				$level = mysql_result($res, 0);
				mysql_free_result($res);
				return $level;
			}
			
			return 1;
		}


		function getBadnick($mask)
		{
			$mask = strtolower($mask);
			if (array_key_exists($mask, $this->db_badnicks))
				return $this->db_badnicks[$mask];

			return false;
		}


		function isBadnick($nick_name)
		{
			foreach ($this->db_badnicks as $b_key => $badnick) {
				if ($badnick->matches($nick_name))
					return true;
			}

			return false;
		}


		function addBadnick($mask)
		{
			if ($this->getBadnick($mask) != false)
				return false;

			$badnick = new DB_BadNick();
			$badnick->setMask($mask);
			$badnick->save();

			$key = strtolower($mask);
			$this->db_badnicks[$key] = $badnick;

			return $this->db_badnicks[$key];
		}


		function removeBadnick($mask)
		{
			$badnick = $this->getBadnick($mask);
			if ($badnick == false)
				return false;

			$key = strtolower($mask);
			unset($this->db_badnicks[$key]);
			$badnick->delete();

			return true;
		}


	}
	
	$cs = new NicknameService();


