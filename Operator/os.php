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
	require( SERVICE_DIR .'/db_jupe.php' );
	
	
	class OperatorService extends Service
	{
		var $pending_events = array();
		var $db_glines = array();
		var $db_jupes = array();
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
			}
			
			if( defined('COMP_GLINE') && COMP_GLINE == true )
			{
				if(!defined('COMP_DURATION'))
					die('comp_gline is enabled, but comp_duration was not defined!');
				if(convert_duration(COMP_DURATION) == false)
					die('The duration specified in comp_duration is invalid!');
				if(!defined('COMP_REASON') || COMP_REASON == '')
					die('comp_gline is enabled, but comp_reason was not defined!');
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
			
			$bot_num = $this->default_bot->get_numeric();
			foreach( $this->default_bot->channels as $chan_name )
			{
				$chan = $this->get_channel( $chan_name );
				
				if( !$chan->is_op($bot_num) )
					$this->op( $chan->get_name(), $bot_num );
			}

			foreach( $this->pending_events as $event )
			{
				extract( $event );
				$this->default_bot->messagef( $chan_name, '[%'. (NICKLENGTH + $margin) .'s] %s %s',
					$source, $event_name, $misc);

				$this->pending_events = array();
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


		function load_jupes()
		{
			$res = db_query( 'select * from os_jupes order by jupe_id asc' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$jupe = new DB_Jupe( $row );
				
				if( $jupe->is_expired() )
				{
					$jupe->delete();
					continue;
				}

				$jupe_key = strtolower( $jupe->get_server() );
				$this->db_jupes[$jupe_key] = $jupe;
			}

			debugf( 'Loaded %d jupes.', count($this->db_jupes) );
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


		function get_db_jupe( $server )
		{
			$jupe_key = strtolower( $server );
			if( array_key_exists($jupe_key, $this->db_jupes) )
				return $this->db_jupes[$jupe_key];

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


		function service_add_jupe( $server, $duration, $last_mod, $reason )
		{
			$jupe = $this->get_db_jupe($server);
			
			if( !$jupe )
				return false;

			$db_jupe = new DB_Jupe();
			$db_jupe->set_server( $jupe->get_server() );
			$db_jupe->set_duration( $jupe->get_expire_ts() - time() );
			$db_jupe->set_last_mod( $jupe->get_last_mod() );
			$db_jupe->set_ts( time() );
			$db_jupe->set_reason( $jupe->get_reason() );
			$db_jupe->set_active( $jupe->is_active() );
			$db_jupe->save();

			$jupe_key = strtolower( $server );
			$this->db_jupes[$jupe_key] = $jupe;
		}


		function service_remove_jupe( $server )
		{
			$jupe = $this->get_db_jupe($server);

			if(!$jupe)
				return false;
			
			$jupe->delete();
			$jupe_key = strtolower( $server );
			unset( $this->db_jupes[$jupe_key] );
		}

		
		/**
		 * is_blacklisted_dns is a generic function to provide extensibility
		 * for easily checking DNS based blacklists. It has three arguments:
		 * 	host:    The IP address of the host you wish to check.
		 * 	suffix:    The DNS suffix for the DNSBL service.
		 *    pos_resp:  An array containing responses that should be considered
		 * 	           a positive match. If not provided, will assume that ANY
		 * 	           successful DNS resolution against the DNSBL should be
		 * 	           considered a positive match.
		 * 
		 * For example:
		 * 	is_blacklisted_dns( '1.2.3.4', 'dnsbl.com' )
		 * 		Returns true if 4.3.2.1.dnsbl.com returns any DNS resolution.
		 * 	is_blacklisted_dns( '1.2.3.4', 'dnsbl.com', '127.0.0.2' )
		 * 		Returns true if 4.3.2.1.dnsbl.com contains '127.0.0.2' in its 
		 * 		response.
		 * 	is_blacklisted_dns( '1.2.3.4', 'dnsbl.com', array('127.0.0.2', '127.0.0.3'))
		 * 		Returns true if 4.3.2.1.dnsbl.com contains either 127.0.0.2 or 
		 * 		127.0.0.3 in its response.
		 */
		function is_blacklisted_dns( $host, $dns_suffix, $pos_responses = -1 )
		{
			if( is_private_ip($host) )
			{
				debugf('%s is a private address. No DNSBL check necessary', $host);
				return false;
			}
			
			$start_ts = microtime( true );
			
			/**
			 * DNS blacklists work by storing records for ipaddr.dnsbl.com,
			 * but with DNS all octets are reversed. So to check if 1.2.3.4
			 * is blacklisted in a DNSBL, we need to query for the hostname
			 * 4.3.2.1.dnsbl.com.
			 */
			$octets = explode( '.', $host );
			$reverse_octets = implode( '.', array_reverse($octets) );
			$lookup_addr = $reverse_octets .'.'. $dns_suffix .'.';
			debugf( 'DNSBL checking %s', $lookup_addr );
			$dns_result = gethostbyname( $lookup_addr );
			
			$end_ts = microtime( true );
			debugf( 'DNSBL check time elapsed: %0.4f seconds', $end_ts - $start_ts );
			
			/**
			 * gethostbyname returns the original, unmodified host name if
			 * DNS resolution failed. So we will assume it resolved if we
			 * receive a response that's different from the input.
			 */
			$resolved = ( $dns_result != $lookup_addr );
			
			// If it didn't resolve, don't check anything
			if( !$resolved )
				return false;
			
			// Check for any successful resolution
			if( $resolved && $pos_responses == -1 || empty($pos_responses) )
				return true;
			
			// Check for a match against the provided string
			if( is_string($pos_responses) && !empty($pos_responses)
			 		&& $dns_result == ('127.0.0.'. $pos_responses) )
				return true;
			
			// Check for a match within the provided array
			if( is_array($pos_responses) )
			{
				foreach( $pos_responses as $tmp_match )
				{
					$tmp_match = '127.0.0.'. $tmp_match;
					if( $tmp_match == $dns_result )
						return true;
				}
			}
			
			// All checks failed; host tested negative.
			return false;
		}
		
		
		function is_tor_host( $host )
		{
			/**
			 * The TOR DNSBL will return 127.0.0.1 as the address for a host
			 * if it is a Tor server or exit node, and 127.0.0.2 if the host
			 * is neither but one exists on the same class C subnet. We don't
			 * care if there's one on the subnet, only if the host we query
			 * for is actually a Tor server or exit node.
			 * 
			 * For more information on the TOR DNSBL, please see
			 * http://www.sectoor.de/tor.php.
			 */
			
			/**
			 * We use multiple Tor DNSBLs because sometimes you'll get a
			 * false negative if one DNSBL isn't 100% up-to-date. Rare,
			 * but not impossible.
			 */
			$blacklists = array(
				'tor.dnsbl.sectoor.de' => array( 1 ),
				'tor.dan.me.uk'        => array( 100 ),
				'tor.ahbl.org'         => array( 2 )
			);

			foreach( $blacklists as $dns_suffix => $responses )
			{
				if( is_blacklisted_dns($host, $dns_suffix, $responses) )
					return true;
			}
			
			return false;
		}
		
		
		function is_compromised_host( $host )
		{
			/**
			 * To determine if a host is compromised, check a myriad of public
			 * DNSBL services (some are IRC-centric) to see if they are listed.
			 */
			$blacklists = array(
				'ircbl.ahbl.org'      => array( 2 ),
				'dnsbl.dronebl.org'   => array(),
				'dnsbl.proxybl.org'   => array( 2 ),
				'rbl.efnetrbl.org'    => array( 1, 2, 3, 4 ),
				'dnsbl.swiftbl.net'   => array( 2, 3, 4, 5 ),
				'cbl.abuseat.org'     => array( 2 )
				'xbl.spamhaus.org'    => array(),
				'drone.abuse.ch'      => array( 2, 3, 4, 5 ),
				'httpbl.abuse.ch'     => array( 2, 3, 4 ),
				'spam.abuse.ch'       => array( 2 ),
			);
			
			foreach( $blacklists as $dns_suffix => $responses )
			{
				if( is_blacklisted_dns($host, $dns_suffix, $responses) )
					return true;
			}
			
			return false;
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

			if( $is_command )
				$channel = COMMAND_CHANNEL;
			else
				$channel = EVENT_CHANNEL;
			
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
			
			if(!$this->finished_burst)
			{
				$this->pending_events[] = array(
					'chan_name'   => $channel,
					'margin'      => $margin,
					'source'      => $source,
					'event_name'  => $event_name,
					'misc'        => $misc );
			}

			$bot->messagef( $channel, '[%'. (NICKLENGTH + $margin) .'s] %s %s',
				$source, $event_name, $misc);

/*
			if($this->finished_burst)
				$bot->messagef( $channel, "[%". (NICKLENGTH + $margin) ."s] %s %s", $source, $event_name, $misc);
*/
			
			return true;
		}
	}
	
	$os = new OperatorService();

?>
