<?php

	$ac_name = $pargs[1];

	if( !($account = $this->get_account($ac_name)) ) 
	{
		$bot->noticef( $user, 'That account does not exist!' );
		return false;
	}
	

	/**
	 * If enabled in the configuration, make sure this user doesn't 
	 * own any channels before removing their account...
	 */
	if( defined('NS_CHECK_CHANNELS') && NS_CHECK_CHANNELS )
	{
		$cres = db_queryf("
				select ch.name 
				from channel_access ca 
				inner join channels ch on ch.channel_id = ca.chan_id
				where ca.user_id = '%d' and ca.level = '500'
				", $account->get_id() );
		if( $cres && mysql_num_rows($cres) > 0 )
		{
			$channels = array();
			while( $row = mysql_fetch_assoc($cres) )
				$channels[] = $row['name'];

			$bot->noticef( $user, 'All channels owned by %s must be purged before the account can be removed.', $account->get_name() );
			$bot->noticef( $user, '%s owns the following channel(s): %s', $account->get_name(), implode(', ', $channels) );
		}

		mysql_free_result( $cres );
		return false;
	}

	$ac_id = $account->get_id();
	$ac_name = $account->get_name();

	$this->remove_account( $account );
	$account->delete();

	/**
	 * Notify all other services so that they can remove any service-specific
	 * information about the account (i.e., channel services access, etc)
	 */
	$this->notify_services( NOTIFY_ACCOUNT, NOTIFY_DELETE, $ac_id );

	$bot->noticef( $user, 'The account for %s has been purged.', $ac_name );

?>
