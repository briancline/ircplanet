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
	require( SERVICE_DIR .'/db_gline.php' );
	require( SERVICE_DIR .'/db_badchan.php' );
	
	
	class OperatorService extends Service
	{
		var $pending_events = array();
		var $tor_hosts = array();
		var $db_glines = array();
		var $db_badchans = array();

		function service_construct()
		{
		}
		
		
		function service_destruct()
		{
		}
		

		function service_load()
		{
			$this->load_glines();
			$this->load_badchans();

			if( defined('TOR_GLINE') && TOR_GLINE == true )
			{
				if(!defined('TOR_DURATION'))
					die('tor_gline is enabled, but tor_duration was not defined!');
				if(convert_duration(TOR_DURATION) == false)
					die('The duration specified in tor_duration is invalid!');
				if(!defined('TOR_REASON') || TOR_REASON == '')
					die('tor_gline is enabled, but tor_reason was not defined!');

				$this->load_tor_hosts();
			}

                        if( defined('CLONE_GLINE') && CLONE_GLINE == true )
                        {
				if(!defined('CLONE_MAX'))
					die('clone_gline is enabled, but clone_max was not defined!');
				if(!is_numeric(CLONE_MAX) || CLONE_MAX == 0)
					die('Invalid value specified for clone_max!');
                                if(!defined('CLONE_DURATION'))
                                        die('clone_gline is enabled, but clone_duration was not defined!');
                                if(convert_duration(CLONE_DURATION) == false)
                                        die('The duration specified in clone_duration is invalid!');
                                if(!defined('CLONE_REASON') || CLONE_REASON == '')
                                        die('clone_gline is enabled, but clone_reason was not defined!');
                        }
		}
		
		
		function service_preburst()
		{
		}
		
		
		function service_postburst()
		{
			foreach($this->db_glines as $key => $db_gline)
			{
				$this->add_gline( $db_gline->get_mask(), $db_gline->get_remaining_secs(), $db_gline->get_reason() );
				$this->enforce_gline( $db_gline->get_mask() );
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
		
		
		function load_glines()
		{
			$res = db_query( 'select * from os_glines order by gline_id asc' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$gline = new DB_Gline( $row );
				
				if( $gline->is_expired() )
				{
					$gline->delete();
					continue;
				}

				$gline_key = strtolower( $gline->get_mask() );
				$this->db_glines[$gline_key] = $gline;
			}

			debugf( 'Loaded %d g-lines.', count($this->db_glines) );
		}


		function load_badchans()
		{
			$res = db_query( 'select * from os_badchans order by badchan_id asc' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$badchan = new DB_BadChan( $row );
				
				$badchan_key = strtolower( $badchan->get_mask() );
				$this->db_badchans[$badchan_key] = $badchan;
			}

			debugf( 'Loaded %d badchans.', count($this->db_badchans) );
		}


		function get_db_gline( $host )
		{
			$gline_key = strtolower( $host );
			if( array_key_exists($gline_key, $this->db_glines) )
				return $this->db_glines[$gline_key];

			return false;
		}


		function service_add_gline( $host, $duration, $reason )
		{
			if( $this->get_db_gline($host) )
				return false;

			$gline = new DB_Gline();
			$gline->set_ts( time() );
			$gline->set_mask( $host );
			$gline->set_duration( $duration );
			$gline->set_reason( $reason );
			$gline->save();

			$gline_key = strtolower( $host );
			$this->db_glines[$gline_key] = $gline;
		}


		function service_remove_gline( $host )
		{
			$gline = $this->get_db_gline($host);

			if(!$gline)
				return false;
			
			$gline->delete();
			$gline_key = strtolower( $host );
			unset( $this->db_glines[$gline_key] );
		}


		function load_tor_hosts()
		{
			if(file_exists(TOR_HOSTS_FILE) && is_readable(TOR_HOSTS_FILE))
			{
				$this->tor_hosts = array();
				$hosts = split("\n", file_get_contents(TOR_HOSTS_FILE));
				foreach($hosts as $host)
					$this->tor_hosts[$host] = 0;
				
				debug("Loaded ". count($this->tor_hosts) ." Tor hosts.");
				return true;
			}
			
			return false;
		}
		
		
		function is_tor_host($host)
		{
			return array_key_exists($host, $this->tor_hosts);
		}
		
		
		function get_badchan( $mask )
		{
			$mask = strtolower( $mask );
			if( array_key_exists($mask, $this->db_badchans) )
				return $this->db_badchans[$mask];

			return false;
		}


		function is_badchan( $chan_name )
		{
			if( is_channel($chan_name) )
				$chan_name = $chan_name->get_name();

			foreach( $this->db_badchans as $b_key => $badchan )
			{
				if( $badchan->matches($chan_name) )
					return true;
			}

			return false;
		}


		function add_badchan( $mask )
		{
			if( $this->get_badchan($mask) != false )
				return false;

			$badchan = new DB_BadChan();
			$badchan->set_mask( $mask );
			$badchan->save();

			$key = strtolower( $mask );
			$this->db_badchans[$key] = $badchan;

			return $this->db_badchans[$key];
		}


		function remove_badchan( $mask )
		{
			$badchan = $this->get_badchan( $mask );
			if( $badchan == false )
				return false;

			$key = strtolower( $mask );
			unset( $this->db_badchans[$key] );
			$badchan->delete();

			return true;
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
