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
		
		if( !account )
		{
			$account = new DB_User($row['account_id']);
			$account_key = strtolower($account->get_name());
			
			$this->accounts[$account_key] = $account;
			
			debug( 'Loaded new account '. $account->get_name() );
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