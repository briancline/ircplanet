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

	require_once('globals.php');
	require_once('../Core/service.php');
	require_once(SERVICE_DIR .'/db_channel.php');
	require_once(SERVICE_DIR .'/db_channel_access.php');
	require_once(SERVICE_DIR .'/db_badchan.php');
	
	
	class ChannelService extends Service
	{
		var $db_channels = array();
		var $db_badchans = array();
		
		
		function loadChannels()
		{
			$res = db_query('select * from channels order by lower(name) asc');
			while ($row = mysql_fetch_assoc($res)) {
				$channel_key = strtolower($row['name']);
				$channel = new DB_Channel($row);
				
				if ($channel->autoLimits() && !$channel->hasPendingAutolimit()) {
					$this->addTimer(false, $channel->getAutoLimitWait(), 
						'auto_limit.php', $channel->getName());
					$channel->setPendingAutolimit(true);
				}
				
				$clean_defmodes = $this->cleanModes($channel->getDefaultModes());
				if ($channel->getDefaultModes() != $clean_defmodes) {
					debugf("Setting defmodes for %s from [%s] to [%s]", $channel->getName(), $channel->getDefaultModes(), $clean_defmodes);
					$channel->setDefaultModes($clean_defmodes);
					$channel->save();
				}
				
				$this->db_channels[$channel_key] = $channel;
			}
			
			debugf("Loaded %d channel records.", count($this->db_channels));
		}
		
		
		function loadAccess()
		{
			$n = 0;
			$res = db_query('select * from channel_access');
			while ($row = mysql_fetch_assoc($res)) {
				$chan_id = $row['chan_id'];
				$user_id = $row['user_id'];
				
				$chan = $this->getChannelRegById($chan_id);
				if (!$chan) {
					debug("*** Loaded channel access pair for channel ID $chan_id, but no such channel exists!");
					continue;
				}

				$access = new DB_Channel_Access($row);
				$chan->addAccess($access);
				
				$n++;
			}
			
			debug("Loaded $n channel access records.");
		}
		
		
		function loadBans()
		{
			$n = 0;
			$res = db_query('select * from channel_bans');
			while ($row = mysql_fetch_assoc($res)) {
				$chan_id = $row['chan_id'];
				$user_id = $row['user_id'];
				$mask = $row['mask'];
				
				$chan = $this->getChannelRegById($chan_id);
				if (!$chan) {
					debug("*** Loaded ban for channel ID $chan_id, but no such channel exists!");
					continue;
				}
				
				$ban = new DB_Ban($chan_id, $user_id, $mask);
				$ban->loadFromRow($row);
				$chan->addBan($ban);
				
				$n++;
			}
			
			debug("Loaded $n channel ban records.");
		}
		
		
		function loadBadchans()
		{
			$res = db_query('select * from cs_badchans order by badchan_id asc');
			while ($row = mysql_fetch_assoc($res)) {
				$badchan = new DB_BadChan($row);
				
				$badchan_key = strtolower($badchan->getMask());
				$this->db_badchans[$badchan_key] = $badchan;
			}

			debugf('Loaded %d badchans.', count($this->db_badchans));
		}


		function serviceConstruct()
		{
		}
		
		
		function serviceLoad()
		{
			$this->loadChannels();
			$this->loadAccess();
			$this->loadBans();
			$this->loadBadchans();
			
			$this->addTimer(true, 300, 'save_data.php');
			$this->addTimer(true, 30, 'expire_channels.php');
		}
		
		
		function servicePreburst()
		{
			$bot = $this->default_bot;
			$botnum = $bot->getNumeric();

			foreach ($this->db_channels as $dbchan_key => $dbchan) {
				if ($chan = $this->getChannel($dbchan_key)) {
					$this->addChannelUser($chan->getName(), $botnum, 'o');
				}
				else {
					$ts = $dbchan->getCreateTs();

					if ($ts == 0 || ($ts > $dbchan->getRegisterTs() && $dbchan->getRegisterTs() > 0)) {
						$ts = $dbchan->getRegisterTs();
						$dbchan->setCreateTs($ts);
						$dbchan->save();
					}
					
					$chan = $this->addChannel($dbchan->getName(), $ts);
					$chan->addMode(CMODE_REGISTERED);
					$this->addChannelUser($dbchan->getName(), $botnum, 'o');
				}
				
				if ($dbchan->hasDefaultTopic()) {
					$deftopic = $dbchan->getDefaultTopic();
					$chan->setTopic($deftopic);
				}
				
				if ($dbchan->hasDefaultModes()) {
					$defmodes = $dbchan->getDefaultModes();
					$chan->addModes($defmodes);
				}
			}
		}
		
		
		function servicePostburst($uplink_burst = false)
		{
			$bot = $this->default_bot;
			$botnum = $bot->getNumeric();
			
			foreach ($this->db_channels as $dbchan_key => $dbchan) {
				$chan = $this->getChannel($dbchan_key);
				if ($chan && !$chan->isOp($botnum)) {
					$this->mode($chan->getName(), '+Ro '. $botnum);
					$bot->mode($chan->getName(), $dbchan->getDefaultModes());
					$dbchan->setCreateTs($chan->getTs());
					$dbchan->save();
				}
			}

			foreach ($this->default_bot->channels as $chan_name) {
				$chan = $this->getChannel($chan_name);
				
				if (!$chan->isOp($botnum))
					$this->op($chan->getName(), $botnum);
			}
		}
		
		
		function serviceDestruct()
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
		
		
		function servicePreread()
		{
		}


		function getChannelRegById($chan_id)
		{
			foreach ($this->db_channels as $chan_key => $chan) {
				if ($chan->getId() == $chan_id)
					return $chan;
			}
			
			return false;
		}
		
		
		function addChannelReg($reg)
		{
			$chan_key = strtolower($reg->getName());
			
			$this->db_channels[$chan_key] = $reg;
			
			return $this->db_channels[$chan_key];
		}
		
		
		function removeChannelReg($chan_name)
		{
			if (isChannelRecord($chan_name))
				$chan_name = $chan_name->getName();
			
			$chan_reg = 0;
			$chan_key = strtolower($chan_name);
			
			if (array_key_exists($chan_key, $this->db_channels)) {
				$chan_reg = $this->db_channels[$chan_key];
				unset($this->db_channels[$chan_key]);
			}
			
			return $chan_reg;
		}
		
		
		function getChannelReg($chan_name)
		{
			$chan_key = strtolower($chan_name);
			
			if (array_key_exists($chan_key, $this->db_channels))
				return $this->db_channels[$chan_key];
			
			return false;
		}
		
		
		function getChannelRegCount($user_id)
		{
			$chan_count = 0;
			
			foreach ($this->db_channels as $chan_key => $chan) {
				$levels = $chan->getLevels();
				foreach ($levels as $level_uid => $level) {
					if ($level_uid == $user_id && $level->getLevel() == 500)
						$chan_count++;
				}
			}
			
			return $chan_count;
		}
		
		
		function isChannelRegistered($chan_name)
		{
			if (isChannel($chan_name))
				$chan_name = $chan_name->getName();
			
			return false !== $this->getChannelReg($chan_name);
		}
		
		
		function getAdminLevel($user_obj)
		{
			if (!is_object($user_obj))
				return 0;
			if (!isAccount($user_obj) && (!isUser($user_obj) || !$user_obj->isLoggedIn()))
				return 0;

			if (!isAccount($user_obj))
				$account = $this->getAccount($user_obj->getAccountName());
			else
				$account = $user_obj;
			
			$res = db_query("select `level` from `cs_admins` where user_id = ". $account->getId());
			if ($res && mysql_num_rows($res) > 0) {
				$level = mysql_result($res, 0);
				mysql_free_result($res);
				return $level;
			}
			
			return 0;
		}
		
		
		function getChannelAccess($chan_name, $user_obj)
		{
			$chan_key = strtolower($chan_name);
			
			if (!($chan = $this->getChannelReg($chan_key)))
				return false;
			if (!is_object($user_obj) || !isUser($user_obj) || !$user_obj->isLoggedIn())
				return false;
			
			$levels = $chan->getLevels();
			
			if (!array_key_exists($user_obj->getAccountId(), $levels))
				return false;
			
			return $levels[$user_obj->getAccountId()];
		}
		
		
		function getChannelAccessAccount($chan_name, $account_obj)
		{
			$chan_key = strtolower($chan_name);
			
			if (!($chan = $this->getChannelReg($chan_key)))
				return false;
			if (!isAccount($account_obj))
				return false;
			
			$levels = $chan->getLevels();
			if (!array_key_exists($account_obj->getId(), $levels))
				return false;
			
			return $levels[$account_obj->getId()];
		}
		
		
		function getChannelLevel($chan_name, $user_obj)
		{
			$chan_key = strtolower($chan_name);
			
			if (!array_key_exists($chan_key, $this->db_channels))
				return 0;
			if (!is_object($user_obj) || !isUser($user_obj) || !$user_obj->isLoggedIn())
				return 0;
			
			$chan = $this->getChannelReg($chan_key);
			return $chan->getLevelById($user_obj->getAccountId());
		}
		
		
		function getChannelLevelByName($chan_name, $user_name)
		{
			$chan_key = strtolower($chan_name);
			
			if (!array_key_exists($chan_key, $this->db_channels))
				return 0;
			if (!($account = $this->getAccount($user_name)))
				return 0;
			
			$chan = $this->getChannelReg($chan_key);
			return $chan->getLevelById($account->getId());
		}
		
		
		function getActiveChannelUsers($chan_name)
		{
			$chan = $this->getChannelReg($chan_name);
			$active_users = array();
			
			if (!$chan)
				return false;
			
			$levels = $chan->getLevels();
			$seek_account_ids = array();
			
			foreach ($levels as $tmp_level)
				$seek_account_ids[] = $tmp_level->getUserId();
			
			foreach ($this->users as $tmp_numeric => $tmp_user) {
				if (!$tmp_user->isLoggedIn())
					continue;
				
				if (in_array($tmp_user->getAccountId(), $seek_account_ids))
					$active_users[] = $tmp_user;
			}
			
			return $active_users;
		}
		

		function getBadchan($mask)
		{
			$mask = strtolower($mask);
			if (array_key_exists($mask, $this->db_badchans))
				return $this->db_badchans[$mask];

			return false;
		}


		function isBadchan($chan_name)
		{
			if (isChannel($chan_name))
				$chan_name = $chan_name->getName();

			foreach ($this->db_badchans as $b_key => $badchan) {
				if ($badchan->matches($chan_name))
					return true;
			}

			return false;
		}


		function addBadchan($mask)
		{
			if ($this->getBadchan($mask) != false)
				return false;

			$badchan = new DB_BadChan();
			$badchan->setMask($mask);
			$badchan->save();

			$key = strtolower($mask);
			$this->db_badchans[$key] = $badchan;

			return $this->db_badchans[$key];
		}


		function removeBadchan($mask)
		{
			$badchan = $this->getBadchan($mask);
			if ($badchan == false)
				return false;

			$key = strtolower($mask);
			unset($this->db_badchans[$key]);
			$badchan->delete();

			return true;
		}


	}
	
	$cs = new ChannelService();


