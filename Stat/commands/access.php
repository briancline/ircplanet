<?php

	$admins = array();
	$search_mask = $pargs[1];

	$tmp_q = db_query( '
			select ss_admins.user_id, accounts.name, ss_admins.level 
			from ss_admins 
			inner join accounts on accounts.account_id = ss_admins.user_id 
			order by level desc
	' );
	while( $row = mysql_fetch_assoc($tmp_q) )
	{
		$tmp_account = $this->get_account( $row['name'] );
		if( !$tmp_account || !fnmatch($search_mask, $tmp_account->get_name()) )
			continue;

		$admins[$tmp_account->get_name()] = $row['level'];
	}
	mysql_free_result( $tmp_q );


	$bot->noticef( $user, '%s  %5s  %-15s  %-30s%s', 
		BOLD_START, 'Level', 'User Name', 'E-mail Address', BOLD_END );
	$bot->noticef( $user, str_repeat('-', 56) );

	foreach( $admins as $tmp_name => $tmp_level )
	{
		$tmp_account = $this->get_account( $tmp_name );
		$bot->noticef( $user, '  %5s  %-15s  %-30s', 
			$tmp_level, $tmp_account->get_name(), 
			$tmp_account->get_email() );
	}

?>
