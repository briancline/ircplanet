<?php

	$user_numeric = $args[0];
	$user = $this->get_user( $user_numeric );
		
	$privmsg_target = $args[2];
	$secure_nick = $secure_server = '';
	$is_secure = false;
	
	if( ($at_pos = strpos($privmsg_target, "@")) )
	{
		$is_secure = true;
		$secure_nick = substr( $privmsg_target, 0, $at_pos );
		$secure_server = substr( $privmsg_target, $at_pos + 1 );
		$privmsg_target_obj = $this->get_user_by_nick( $secure_nick );
		$privmsg_target = $privmsg_target_obj->get_numeric();
	}
	
?>