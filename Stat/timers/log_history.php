<?php

	$oper_count = 0;
	$service_count = 0;
	
	foreach($this->users as $num => $user)
	{
		if($user->is_service())
			$service_count++;
		
		if($user->is_oper())
			$oper_count++;
	}
	
	foreach($this->servers as $num => $serv)
	{
		if($serv->is_service())
			$service_server_count++;
	}
	
	db_queryf("insert into stats_history 
		(date, servers, users, channels, accounts, opers, services, service_servers) 
		values ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
		db_date(time()),
		count($this->servers),
		count($this->users),
		count($this->channels),
		count($this->accounts),
		$oper_count,
		$service_count,
		$service_server_count
	);
	
?>