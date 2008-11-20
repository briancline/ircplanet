<?php
	
	$user_num = '';
	$nick = $pargs[1];
	
	foreach( $this->users as $num => $u )
	{
		if( strtolower($u->get_nick()) == $nick )
		{
			$user_num = $num;
			break;
		}
	}
	
	if( empty($user_num) )
		$bot->notice( $user->numeric, "Who?!" );
	else
		print_array($this->users[$num]);
	
	if(array_key_exists(strtolower($nick), $this->accounts))
		print_array($this->accounts[strtolower($nick)]);
?>