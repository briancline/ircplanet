<?php

	$uid = $pargs[1];
	
	if(!($acct = $this->get_account($uid)))
	{
		$bot->noticef($user, '%s is not a valid account name.', $uid);
		return false;
	}
	
	$aid = $acct->get_id();
	
	$res = mysql_query("select * from accounts where id = '$aid'");
	$row = @mysql_fetch_assoc($res);
	
	$dbuser = new DB_User($aid);
	$dbuser->load_from_row($row);
	
	$bot->noticef($user, '%d ?= %d', $dbuser->get_id(), $aid);
	
	if($dbuser->get_id() != $aid)
	{
		$ac_key = strtolower($uid);
		unset($this->accounts[$ac_key]);
		$bot->noticef($user, 'Removed account id %d from memory.', $aid);
	}
	else
	{
		$ac_key = strtolower($dbuser->get_name());
		$this->accounts[$ac_key] = $dbuser;
		$dbuser = $this->get_account($uid);
		$bot->noticef($user, 'Updated account %s in memory. (%s)', $dbuser->get_name(), $dbuser->password);
	}
	
?>