<?php
	
	$last_update_ts = $timer_data[0];
	
	if(empty($last_update_ts))
		$last_update_ts = START_TIME;
		
	$last_update = db_date($last_update_ts);
	
	/**
	 * Check for new and updated account records
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
			$account = new DB_User($row['account_id']);
			$account_key = strtolower($account->get_name());
			
			$this->accounts[$account_key] = $account;
			
			debug( 'Loaded new account '. $account->get_name() );

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
					$user->set_acount_id($account->get_id());
					debug('Associated new account with user '. $user->get_nick());
				}
			}
		}
		else 
		{
			debug("Account $row[name] has updated recently!");
			
			if($account->get_update_ts() >= $last_update_ts)
			{
				debug($account->get_name() ." was updated internally. Skipping...");
				continue;
			}
			
			$account = $this->get_account($row['name']);
			$account->refresh();
		}
	}
	
	
	$timer_data = time();
	
?>
