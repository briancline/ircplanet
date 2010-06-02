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

	$ac_name = $pargs[1];

	if (!($account = $this->getAccount($ac_name))) {
		$bot->noticef($user, 'That account does not exist!');
		return false;
	}
	

	/**
	 * If enabled in the configuration, make sure this user doesn't 
	 * own any channels before removing their account...
	 */
	if (defined('NS_CHECK_CHANNELS') && NS_CHECK_CHANNELS) {
		$cres = db_queryf("
				select ch.name 
				from channel_access ca 
				inner join channels ch on ch.channel_id = ca.chan_id
				where ca.user_id = '%d' and ca.level = '500'
				", $account->getId());
		if ($cres && mysql_num_rows($cres) > 0) {
			$channels = array();
			while ($row = mysql_fetch_assoc($cres))
				$channels[] = $row['name'];

			$bot->noticef($user, 'All channels owned by %s must be purged before the account can be removed.', $account->getName());
			$bot->noticef($user, '%s owns the following channel(s): %s', $account->getName(), implode(', ', $channels));
		}

		mysql_free_result($cres);
		return false;
	}

	$ac_id = $account->getId();
	$ac_name = $account->getName();

	$this->removeAccount($account);
	$account->delete();

	/**
	 * Notify all other services so that they can remove any service-specific
	 * information about the account (i.e., channel services access, etc)
	 */
	$this->notifyServices(NOTIFY_ACCOUNT, NOTIFY_DELETE, $ac_id);

	$bot->noticef($user, 'The account for %s has been purged.', $ac_name);


