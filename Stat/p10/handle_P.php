<?php
	
	$is_public = false;
	$is_private = false;
	$cmd_msg = assemble( $args, 3 );
	$cmd_target = $privmsg_target;
	
	$this->load_command_info();
	
	if( empty($chan_key) && array_key_exists($cmd_target, $this->users) && $this->users[$cmd_target]->is_bot() )
	{
		$bot = $this->users[$cmd_target];
		$is_private = true;
	}
	if( !empty($chan_key) && $message[0] == '!' )
	{
		$cmd_msg = substr( $cmd_msg, 1 );
		$bot = $this->default_bot;
		$is_public = true;
	}
	
	if( $is_public || $is_private )
	{
		$user_numeric = $args[0];
		$user = $this->get_user( $user_numeric );
		$pargs = line_get_args( $cmd_msg, false );
		$cmd_name = strtolower( $pargs[0] );
		
		$last_char = substr($cmd_msg, strlen($cmd_msg) - 1);
		$is_ctcp = ($cmd_name[0] == CTCP_START && $last_char == CTCP_END);
		
		if( $is_ctcp )
		{
			$cmd_msg = trim( $cmd_msg, CTCP_START . CTCP_END );
			$cmd_name = trim( $cmd_name, CTCP_START . CTCP_END );
			$cmd_name = "ctcp_". $cmd_name;
		}
		else if(!$user->is_oper())
		{
			$bot->noticef($user, "You must be a global operator to use this service.");
			return false;
		}
		
		$spoofed_ctcp = ( !$is_ctcp && substr($cmd_name, 0, 5) == 'ctcp_' );
		$cmd_handler_file = CMD_HANDLER_DIR . $cmd_name . '.php';
		
		if( ($this->command_exists($cmd_name) || $is_ctcp) && file_exists($cmd_handler_file) && !$spoofed_ctcp )
		{
			$user_level = $this->get_user_level( $user );
			$cmd_level = $this->get_command_level( $cmd_name );
			$cmd_req_args = $this->get_command_arg_count( $cmd_name );
			$cmd_num_args = count( $pargs ) - 1;
			
			if( $cmd_num_args > 0 )
			{
				$chan_name = $pargs[1];
				$chan_key = strtolower( $chan_name );
			}
			
			if( $user_level >= $cmd_level )
			{
				if( $cmd_num_args >= $cmd_req_args )
				{
					$chan_name = $chan_key;
					$cmd_result = include( $cmd_handler_file );
					
					if($cmd_result == false)
						return false;
					
					$report_cmd = str_replace( "_", " ", $cmd_name );
					$report_cmd = strtoupper( $report_cmd );
					$this->report_command( $report_cmd, $user, assemble($pargs, 1) );
				}
				else
				{
					$bot->noticef( $user, "%sSyntax:%s %s %s", BOLD_START, BOLD_END, 
						$cmd_name, $this->get_command_syntax($cmd_name) );
				}
			}
			else
			{
				$bot->noticef( $user, "You do not have enough access to use that command!" );
			}
		}
		else if( !$is_public )
		{
			$bot->noticef( $user->numeric, 
				"Invalid command! Use %sshowcommands%s to get a list of available commands.",
				BOLD_START, BOLD_END );
		}
	}

?>