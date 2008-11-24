<?php
	
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
