<?php
	
	$is_public = false;
	$is_private = false;
	$cmd_msg = assemble( $args, 3 );
	$cmd_target = $privmsg_target;
	
	if( empty($chan_key) && array_key_exists($cmd_target, $this->users) && $this->users[$cmd_target]->is_bot() )
	{
		$bot = $this->users[$cmd_target];
		$is_private = true;
	}
	if( !empty($chan_key) && $cmd_msg[0] == '!' )
	{
		$cmd_msg = substr( $cmd_msg, 1 );
		$bot = $this->default_bot;
		$is_public = true;
	}
	
	if( $is_public || $is_private )
	{
		$this->load_command_info();

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
		
		$spoofed_ctcp = ( !$is_ctcp && substr($cmd_name, 0, 5) == 'ctcp_' );
		$cmd_handler_file = CMD_HANDLER_DIR . $cmd_name . '.php';
		
		if( ($this->command_exists($cmd_name) || $is_ctcp) && file_exists($cmd_handler_file) && !$spoofed_ctcp )
		{
			$cmd_level = $this->get_command_level( $cmd_name );
			$cmd_syntax = $this->get_command_syntax( $cmd_name );
			$cmd_req_args = $this->get_command_arg_count( $cmd_name );
			$cmd_num_args = count( $pargs ) - 1;
			
			if( $cmd_num_args > 0 )
				$chan_name = $pargs[1];
			
			if( $is_private )
			{
				$chan_key = strtolower( $chan_name );
			}
			else if( eregi("<channel>", $cmd_syntax) && 
				($cmd_num_args == 0 || ($cmd_num_args > 0 && $pargs[1][0] != '#')) )
			{
				$new_pargs = array( $pargs[0], $args[2] );
				for( $i = 1; $i < count($pargs); ++$i )
					$new_pargs[] = $pargs[$i];
				
				$pargs = $new_pargs;
				$chan_name = $pargs[1];
				$cmd_num_args = count( $pargs ) - 1;
			}
			
			$user_admin_level = $this->get_admin_level( $user );
			$user_channel_level = $this->get_channel_level( $chan_name, $user );
			$user_level = $user_channel_level;
			$user_super = false;
			
			if( $user_admin_level > $user_channel_level )
			{
				$user_level = $user_admin_level;
				$user_super = true;
			}
			
			if( $user_level >= $cmd_level )
			{
				if( $cmd_num_args < $cmd_req_args )
				{
					$bot->noticef( $user, "%sSyntax:%s %s %s", BOLD_START, BOLD_END, 
						$cmd_name, $cmd_syntax );
					return false;
				}
				
				if( $cmd_name != 'register' && $cmd_name != 'adminreg' 
					&& eregi("<channel>", $cmd_syntax)
					&& !($chan_reg = $this->get_channel_reg($chan_name)) )
				{
					$bot->noticef( $user, '%s is not a registered channel.', $chan_name );
					return false;
				}

				include( $cmd_handler_file );
			}
			else
			{
				$bot->noticef( $user, "You do not have enough access to use that command!" );
			}
		}
		else if( !$is_public && !$is_ctcp )
		{
			$bot->noticef( $user->numeric, 
				"Invalid command! Use %sshowcommands%s to get a list of available commands.",
				BOLD_START, BOLD_END );
		}
	}

?>