<?php

	require( 'globals.php' );
	require( '../Core/service.php' );
	
	
	class OperatorService extends Service
	{
		var $pending_events = array();
		
		
		function service_construct()
		{
		}
		
		
		function service_destruct()
		{
		}
		

		function service_load()
		{
		}
		
		
		function service_preburst()
		{
		}
		
		
		function service_postburst()
		{
		}
		
		
		function service_preread()
		{
		}
		

		function service_close( $reason = 'So long, and thanks for all the fish!' )
		{
			foreach( $this->users as $numeric => $user )
			{
				if( $user->is_bot() )
				{
					$this->sendf( FMT_QUIT, $numeric, $reason );
					$this->remove_user( $numeric );
				}
			}
		}

		
		function service_main()
		{
		}
		
		
		function get_user_level( $user_obj )
		{
			$acct_id = $user_obj;
			
			if( is_object($user_obj) && get_class($user_obj) == 'User' )
			{
				if( !$user_obj->is_logged_in() )
					return 0;
				
				$acct_id = $user_obj->get_account_id();
			}
			
			$res = db_query( "select `level` from `os_admins` where user_id = ". $acct_id );
			if( $res && mysql_num_rows($res) > 0 )
			{
				$level = mysql_result( $res, 0 );
				mysql_free_result( $res );
				return $level;
			}
			
			return 0;
		}
		
		
		function report_command( $command_name, $source, $arg1 = "", $arg2 = "", $arg3 = "", $arg4 = "", $arg5 = "")
		{
			$command_name = BOLD_START . $command_name . BOLD_END;
			return $this->report_event( $command_name, $source, $arg1, $arg2, $arg3, $arg4, $arg5, true );
		}
		
		
		function report_event( $event_name, $source, $arg1 = "", $arg2 = "", $arg3 = "", $arg4 = "", $arg5 = "", $is_command = false )
		{
			if( (!$is_command && !REPORT_EVENTS) || ($is_command && !REPORT_COMMANDS) )
				return;
			
			$bot = $this->default_bot;
			
			$source_type = get_class($source);
			if($source_type == 'Server')
				$source = BOLD_START . $source->get_name_abbrev(NICKLENGTH) . BOLD_END;
			else if($source_type == 'User')
				$source = $source->get_nick();
			
			for($i = 1; $i <= 5; $i++)
			{
				eval('$arg = $arg'. $i .';');
				
				$arg_type = get_class($arg);
				if($arg_type == 'Server' || $arg_type == 'Channel')
					$arg = $arg->get_name();
				else if($arg_type == 'User')
					$arg = $arg->get_nick();
				
				eval('$arg'. $i .' = $arg;');
			}
			
			if(strlen($source) > NICKLENGTH)
				$source = substr($source, 0, NICKLENGTH);
			
			$margin = substr_count( $source, BOLD_START );
			$misc = $arg1 .' '. $arg2 .' '. $arg3 .' '. $arg4 .' '. $arg5;
			$misc = trim($misc);
			
			if($this->finished_burst)
				$bot->messagef( BOT_CHAN, "[%". (NICKLENGTH + $margin) ."s] %s %s", $source, $event_name, $misc);

/*
			$this->pending_events[] = array(
				'margin'      => $margin,
				'source'      => $source,
				'event_name'  => $event_name,
				'misc'        => $misc );
			
			if($this->finished_burst)
			{
				foreach($this->pending_events as $event)
				{
					extract($event);
					$bot->messagef( BOT_CHAN, "[%". (NICKLENGTH + $margin) ."s] %s %s",
						$source, $event_name, $misc);
				}
				
				$this->pending_events = array();
			}
*/
			
			return true;
		}
	}
	
	$os = new OperatorService();

?>
