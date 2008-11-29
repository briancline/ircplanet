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
	
	$last_update_ts = START_TIME;

	if( !empty($timer_data) )
		$last_update_ts = $timer_data[0];
		
	$last_update = db_date($last_update_ts);
	
	/**
	 * Check for new and updated account records since this timer last ran
	 */
	$res = db_query( 
		'select account_id, name, update_date '.
		'from accounts '.
		"where create_date >= '$last_update' or update_date >= '$last_update'", false );

	while( $row = mysql_fetch_assoc($res) )
	{
		$account = $this->get_account($row['name']);
		
		if( !$account )
		{
			/**
			 * We've never seen this account before, so load it into memory and associate
			 * it with any users who are using it.
			 */
			$account = new DB_User($row['account_id']);
			$account_key = strtolower($account->get_name());
			
			$this->accounts[$account_key] = $account;

			/**
			 * Make sure that we tie up any loose ends where we receive an AC account
			 * message from another service before we know about the account. The account
			 * name is stored for each user, so we just need to find any users with a set
			 * account name but a missing account ID. If this is the matching account,
			 * set the account ID accordingly so we will now know who they are.
			 */
			foreach($this->users as $numeric => $user)
			{
				if(!$user->is_logged_in() && $user->has_account_name()
						&& strtolower($user->get_account_name()) == $account_key)
				{
					$user->set_account_id($account->get_id());
					debug('Associated new account with user '. $user->get_nick());
				}
			}
		}
		else 
		{
			/**
			 * It appears we updated the account internally, so skip this account.
			 */
			if($account->get_update_ts() >= $last_update_ts)
				continue;
			
			/**
			 * Another service updated this account, so refresh its information.
			 */
			$account->refresh();
		}
	}
	
	$timer_data = time();
	
?>
