<?php
	
	/**
	 * Check for new account records
	 */
	$res = db_query( 'select * from accounts order by lower(name) asc', false );
	while( $row = mysql_fetch_assoc($res) )
	{
		if( !$this->get_account($row['name']) )
		{
			$account_key = strtolower( $row['name'] );
			$account = new DB_User( $account_key );
			$account->load_from_row( $row );
			
			$this->accounts[$account_key] = $account;
			debug( 'Loaded new account '. $account->get_name() );
			$n++;
		}
	}
	
?>