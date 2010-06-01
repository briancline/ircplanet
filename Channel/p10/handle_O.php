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

	$user = $this->get_user($args[0]);
	$bot = $this->get_user($args[2]);
	$cmd_msg = assemble($args, 3);

	$account_pattern = '/^\|IPSVC\|AC\|(L|R|D)\|[0-9]+\|$/';
	$channel_pattern = '/^\|IPSVC\|C\|(R|D)\|[0-9]+\|$/';
	$access_pattern = '/^\|IPSVC\|CA\|(R|D)\|[0-9]+\|[0-9]+\|$/';

   	if(!$user->is_service())
		return false;

	if (preg_match($account_pattern, $cmd_msg)) {
		$nargs = explode('|', $cmd_msg);
		$cmd = $nargs[3];
		$account_id = $nargs[4];

		$load = ($cmd == 'L');
		$reload = ($cmd == 'R');
		$delete = ($cmd == 'D');
		
		debugf('Received account %s command from %s: %s', 
			$cmd, $user->get_nick(), $account_id);

		if ($load) {
			$this->load_single_account($account_id);
			$account = $this->get_account_by_id($account_id);
			
			if ($account)
				debugf('Successfully loaded account %s.', $account->get_name());
			else
				debugf('Couldnt find account %d!', $account_id);
		}
		elseif ($reload) {
			$account = $this->get_account_by_id($account_id);

			if ($account)
				$account->refresh();

			if ($account && $account->get_id() == $account_id)
				debugf('Successfully reloaded account %s.', $account->get_name());
			else
				debugf('Record for account id %d disappeared!', $account_id);
		}
		elseif ($delete) {
			$account = $this->get_account_by_id($account_id);

			if ($account && $this->remove_account($account)) {
				$ac_name = $account->get_name();
				$account->delete();

				foreach ($this->users as $numeric => $user) {
					if ($user->is_logged_in() && $user->get_account_id() == $account_id)
						$user->set_account_id(0);
				}

				foreach ($this->db_channels as $chan_key => $chan) {
					$level = $chan->get_level_by_id($account_id);

					if ($level == 500) {
						$this->remove_channel_reg($chan);
						$chan->delete();

						if (($tmp_chan = $this->get_channel($chan_key)) 
								&& $tmp_chan->is_on($bot->get_numeric())) 
						{
							$bot->part($tmp_chan->get_name(), 'Owner account purged');
						}
					}
					elseif ($level > 0) {
						debugf('Removed %s access to %s', $ac_name, $chan->get_name());
						$chan->remove_access($account_id);
					}
				}
				
				debugf('Successfully removed account %s (%d).', $ac_name, $account_id);
			}
			else {
				debugf('Couldnt remove account %d!', $account_id);
			}
		}
	}
	elseif (preg_match($channel_pattern, $cmd_msg)) {
		$nargs = explode('|', $cmd_msg);
		$cmd = $nargs[3];
		$channel_id = $nargs[4];

		$reload = ($cmd == 'R');
		$delete = ($cmd == 'D');

		debugf('Received channel %s command from %s, channel %d', 
				$cmd, $user->get_nick(), $channel_id);

		$channel = $this->get_channel_reg_by_id($channel_id);
		if (!$channel) {
			debugf('Cannot locate channel ID %d!', $channel_id);
			return false;
		}

		if ($reload) {
			$channel->refresh();

			if ($channel->get_id() == $channel_id)
				debugf('Successfully reloaded channel %s.', $channel->get_name());
			else
				debugf('Record for channel id %d disappeared!', $channel_id);
		}
		elseif ($delete) {
			if ($this->remove_channel_reg($channel)) {
				$ch_name = $channel->get_name();
				$channel->delete();
				debugf('Successfully removed channel %s (%d).', $ch_name, $channel_id);
			}
			else {
				debugf('Couldnt remove channel %d!', $channel_id);
			}
		}
	}
	elseif (preg_match($access_pattern, $cmd_msg)) {
		$nargs = explode('|', $cmd_msg);
		$cmd = $nargs[3];
		$channel_id = $nargs[4];
		$account_id = $nargs[5];

		$reload = ($cmd == 'R');
		$delete = ($cmd == 'D');

		debugf('Received channel access %s command from %s, channel %d, account %d', 
				$cmd, $user->get_nick(), $channel_id, $account_id);

		$channel = $this->get_channel_reg_by_id($channel_id);
		if (!$channel) {
			debugf('Cannot locate channel ID %d!', $channel_id);
			return false;
		}

		$access = $channel->get_level_by_id($account_id);
		if (!$access) {
			debugf('Cannot locate channel %s access record for account %d!', $channel->get_name(), $account_id);
			return false;
		}

		if ($reload) {
			$access->refresh();

			if ($access->get_user_id() == $account_id)
				debugf('Successfully refreshed access on %s for %s', $channel->get_name(), $account->get_name());
			else
				debugf('Something is wrong... account ID was %d, now is %d?', $account_id, $access->get_user_id());
		}
		elseif ($delete) {
			$channel->remove_access($account_id);

			debugf('Successfully removed access on %s from %s', $channel->get_name(), $account->get_name());
		}
	}


