<?php
	
	if( $user->is_logged_in() )
	{
		$user_name = $user->get_account_name();
		$account = $this->get_account( $user_name );
		$password_md5 = $account->get_password();
	}
	else if( $cmd_num_args < 2 )
	{
		$bot->noticef( $user, "%sSyntax:%s %s %s",
			BOLD_START, BOLD_END, strtolower($pargs[0]),
			$this->get_command_syntax($pargs[0]) );
		return false;
	}
	else
	{
		$user_name = $pargs[1];
		$password_md5 = md5( $pargs[2] );
	}
	
	
	if( strtolower($user_name) == strtolower($user->get_nick()) )
	{
		$bot->notice( $user, "Suicide is not the answer!" );
		return false;
	}
	
	if( !($account = $this->get_account($user_name)) )
	{
		$bot->noticef( $user, "%s is not a registered nick.", $user_name );
		return false;
	}
	
	if( $user->is_logged_in() && $account->get_id() != $user->get_account_id() )
	{
		$bot->notice( $user, "You cannot ghost someone else's nick!" );
		return false;
	}
	
	if( !($target = $this->get_user_by_nick($user_name)) )
	{
		$bot->notice( $user, "No one is using that nick." );
		return false;
	}
	
	$target_nick = $target->get_nick();
	
	if( $account->get_password() != $password_md5 )
	{
		$bot->notice( $user, "Invalid password!" );
		return false;
	}
	
	$user_name = $account->get_name();
	$this->kill( $target->get_numeric(), "GHOST command used by ". $user->get_nick() );
	
	if( $user->is_logged_in() )
	{
		$bot->noticef( $user, "%s has been disconnected.", $target_nick );
	}
	else 
	{
		$bot->noticef( $user, "%s has been disconnected. You are now logged in.", $target_nick );
		$this->sendf( FMT_ACCOUNT, SERVER_NUM, $user->get_numeric(), $user_name );
		$user->set_account_name( $user_name );
		$user->set_account_id( $account->get_id() );
	}
	
?>