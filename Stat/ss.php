<?php
/*
 * ircPlanet Services for ircu
 * Copyright (c) 2005 Brian Cline.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:

 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of ircPlanet nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without 
 *    specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

	require( 'globals.php' );
	require( '../Core/service.php' );
	
	
	class StatService extends Service
	{
		var $pending_events = array();
		
		
		function service_construct()
		{
			$this->add_timer( true, 60, 'log_history.php' );
		}
		
		
		function service_destruct()
		{
		}
		

		function service_load()
		{
			db_query("delete from stats_servers");
			db_query("delete from stats_users");
			db_query("delete from stats_channels");
			db_query("delete from stats_channel_users");
		}
		
		
		function service_preburst()
		{
		}
		
		
		function service_postburst()
		{
			$bot_num = $this->default_bot->get_numeric();
			foreach( $this->default_bot->channels as $chan_name )
			{
				$chan = $this->get_channel( $chan_name );
				
				if( !$chan->is_op($bot_num) )
					$this->op( $chan->get_name(), $bot_num );
			}
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
			
			if( is_object($user_obj) && is_user($user_obj) )
			{
				if( !$user_obj->is_logged_in() )
					return 0;
				
				$acct_id = $user_obj->get_account_id();
			}
			
			$res = db_query( "select `level` from `ss_admins` where user_id = ". $acct_id );
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
			
			return true;
		}
	}
	
	$ss = new StatService();

?>
