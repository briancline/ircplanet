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
	
	require_once( 'p10.php' );
	require_once( 'user.php' );

	class Bot extends User
	{
		var $net;
		
		function __construct( $num, $nick, $ident, $host, $ip, $start_ts, $desc, $modes = "", &$instance )
		{
			$this->numeric = $num;
			$this->nick = $nick;
			$this->ident = $ident;
			$this->host = $host;
			$this->ip = $ip;
			$this->start_ts = $start_ts;
			$this->desc = $desc;
			$this->add_modes( $modes );
			$this->net = $instance;
		}
		
		function is_bot() { return true; }
		
		function message( $target, $text )
		{
			if( is_object($target) && is_user($target) )
				$target = $target->get_numeric();
			
			$this->net->sendf( FMT_PRIVMSG, $this->numeric, $target, $text );
			$this->last_spoke = time();
		}

		function notice( $target, $text )
		{
			if( is_object($target) && is_user($target) )
				$target = $target->get_numeric();
			
			$this->net->sendf( FMT_NOTICE, $this->numeric, $target, $text );
		}

		function messagef( $target, $format )
		{
			if( is_object($target) && is_user($target) )
				$target = $target->get_numeric();
			
			$args = array();
			$format = addslashes( $format );
			for( $i = 2; $i < func_num_args(); ++$i )
				$args[] = addslashes( func_get_arg($i) );
			
			$arglist = join( "', '", $args );
			eval( "\$notice_text = sprintf('$format', '$arglist');" );
			
			$notice_text = stripslashes( $notice_text );
			$this->net->sendf( FMT_PRIVMSG, $this->numeric, $target, $notice_text );
			$this->last_spoke = time();
		}
		
		function noticef( $target, $format )
		{
			if( is_object($target) && is_user($target) )
				$target = $target->get_numeric();
			
			$args = array();
			$format = addslashes( $format );
			for( $i = 2; $i < func_num_args(); ++$i )
				$args[] = addslashes( func_get_arg($i) );
			
			$arglist = join( "', '", $args );
			eval( "\$notice_text = sprintf('$format', '$arglist');" );
			
			$notice_text = stripslashes( $notice_text );
			$this->net->sendf( FMT_NOTICE, $this->numeric, $target, $notice_text );
		}
		
		function send_syntax( $target, $command )
		{
			$this->noticef( $target, "%sSyntax:%s %s %s",
				BOLD_START, 
				BOLD_END,
				$command,
				$this->net->get_command_syntax( $command )
			);
		}
		
		function send_noaccess( $target )
		{
			$this->notice( $target, "You do not have sufficient permissions to use that command." );
		}
		
		function op( $chan_name, $num_list )
		{
			$numerics = array();
			$chan = $this->net->get_channel( $chan_name );
			
			if( !$chan )
				return;
			
			if( !is_array($num_list) )
			{
				$num_list = array();
				$arg_count = func_num_args();

				for( $i = 1; $i < $arg_count; ++$i )
					$num_list[] = func_get_arg($i);
			}
			
			for( $i = 0; $i < count($num_list); ++$i )
			{
				if( strlen($num_list[$i]) == 0 )
					continue;
					
				$numerics[] = $num_list[$i];
				$chan->add_op( $num_list[$i] );
				
				if( count($numerics) == MAX_MODES_PER_LINE || $i == (count($num_list) - 1) )
				{
					$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan->get_name(), 
						'+'. str_repeat('o', count($numerics)) .' '. join(" ", $numerics),
						$chan->get_ts() );
				}
			}
		}

		function deop( $chan_name, $num_list )
		{
			$numerics = array();
			$chan = $this->net->get_channel( $chan_name );
			
			if( !$chan )
				return;
			
			if( !is_array($num_list) )
			{
				$num_list = array();
				$arg_count = func_num_args();

				for( $i = 1; $i < $arg_count; ++$i )
					$num_list[] = func_get_arg($i);
			}
			
			for( $i = 0; $i < count($num_list); ++$i )
			{
				if( strlen($num_list[$i]) == 0 )
					continue;
					
				$numerics[] = $num_list[$i];
				$chan->remove_op( $num_list[$i] );
				
				if( count($numerics) == MAX_MODES_PER_LINE || $i == (count($num_list) - 1) )
				{
					$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan->get_name(), 
						'-'. str_repeat('o', count($numerics)) .' '. join(" ", $numerics),
						$chan->get_ts() );
				}
			}
		}

		function voice( $chan_name, $num_list )
		{
			$numerics = array();
			$chan = $this->net->get_channel( $chan_name );
			
			if( !$chan )
				return;
			
			if( !is_array($num_list) )
			{
				$num_list = array();
				$arg_count = func_num_args();

				for( $i = 1; $i < $arg_count; ++$i )
					$num_list[] = func_get_arg($i);
			}
			
			for( $i = 0; $i < count($num_list); ++$i )
			{
				if( strlen($num_list[$i]) == 0 )
					continue;
					
				$numerics[] = $num_list[$i];
				$chan->add_voice( $num_list[$i] );
				
				if( count($numerics) == MAX_MODES_PER_LINE || $i == (count($num_list) - 1) )
				{
					$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan->get_name(), 
						'+'. str_repeat('v', count($numerics)) .' '. join(" ", $numerics),
						$chan->get_ts() );
				}
			}
		}
		
		function devoice( $chan_name, $num_list )
		{
			$numerics = array();
			$chan = $this->net->get_channel( $chan_name );
			
			if( !$chan )
				return;
			
			if( !is_array($num_list) )
			{
				$num_list = array();
				$arg_count = func_num_args();

				for( $i = 1; $i < $arg_count; ++$i )
					$num_list[] = func_get_arg($i);
			}
			
			for( $i = 0; $i < count($num_list); ++$i )
			{
				if( strlen($num_list[$i]) == 0 )
					continue;
					
				$numerics[] = $num_list[$i];
				$chan->remove_voice( $num_list[$i] );
				
				if( count($numerics) == MAX_MODES_PER_LINE || $i == (count($num_list) - 1) )
				{
					$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan->get_name(), 
						'-'. str_repeat('v', count($numerics)) .' '. join(" ", $numerics),
						$chan->get_ts() );
				}
			}
		}
		
		
		function ban( $chan_name, $mask )
		{
			$masks = array();
			$mask_list = array();
			$chan = $this->net->get_channel( $chan_name );
			
			if( !$chan )
				return;
			
			if( is_array($mask) )
			{
				$mask_list = $mask;
			}
			else
			{
				$arg_count = func_num_args();

				for( $i = 1; $i < $arg_count; ++$i )
					$mask_list[] = func_get_arg($i);
			}
			
			for( $i = 0; $i < count($mask_list); ++$i )
			{
				if( strlen($mask_list[$i]) == 0 )
					continue;
					
				$masks[] = $mask_list[$i];
				$chan->remove_ban( $mask_list[$i] );
				
				if( count($masks) == MAX_MODES_PER_LINE || $i == (count($mask_list) - 1) )
				{
					$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan->get_name(), 
						'+'. str_repeat('b', count($masks)) .' '. join(" ", $masks),
						$chan->get_ts() );
				}
			}
		}
		
		
		function unban( $chan_name, $mask )
		{
			$masks = array();
			$mask_list = array();
			$chan = $this->net->get_channel( $chan_name );
			
			if( !$chan )
				return;
			
			if( is_array($mask) )
			{
				$mask_list = $mask;
			}
			else
			{
				$arg_count = func_num_args();

				for( $i = 1; $i < $arg_count; ++$i )
					$mask_list[] = func_get_arg($i);
			}
			
			for( $i = 0; $i < count($mask_list); ++$i )
			{
				if( strlen($mask_list[$i]) == 0 )
					continue;
					
				$masks[] = $mask_list[$i];
				$chan->remove_ban( $mask_list[$i] );
				
				if( count($masks) == MAX_MODES_PER_LINE || $i == (count($mask_list) - 1) )
				{
					$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan->get_name(), 
						'-'. str_repeat('b', count($masks)) .' '. join(" ", $masks),
						$chan->get_ts() );
				}
			}
		}
		
		
		function invite( $nick, $chan_name )
		{
			$this->net->sendf( FMT_INVITE, $this->get_numeric(), $nick, $chan_name );
		}
		
		
		function topic( $chan_name, $topic, $chan_ts = 0 )
		{
			if( TOPIC_BURSTING && $chan_ts == 0 )
				return;

			if( TOPIC_BURSTING )
				$this->net->sendf( FMT_TOPIC, $this->get_numeric(), $chan_name, $chan_ts, time(), $topic );
			else
				$this->net->sendf( FMT_TOPIC, $this->get_numeric(), $chan_name, $topic );
		}
		
		
		function clear_modes( $chan_name )
		{
			$chan = $this->net->get_channel( $chan_name );
			$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan_name, '-psmntilk *', $chan->get_ts() );
//			$this->net->sendf( FMT_CLEARMODES, $this->get_numeric(), $chan_name, 'ntpsmikl' );
		}

		function mode( $chan_name, $modes )
		{
			$chan = $this->net->get_channel( $chan_name );
			$this->net->sendf( FMT_MODE, $this->get_numeric(), $chan_name, $modes, $chan->get_ts() );
		}
		
		function kick( $chan_name, $numeric, $reason )
		{
			$this->net->remove_channel_user( $chan_name, $numeric );
			$this->net->sendf( FMT_KICK, $this->get_numeric(), $chan_name, $numeric, $reason );
		}
		
		function join( $chan_name )
		{
			$chan = $this->net->get_channel( $chan_name );
			$this->net->sendf( FMT_JOIN, $this->get_numeric(), $chan_name, $chan->get_ts() );
			$this->net->add_channel_user( $chan_name, $this->get_numeric() );
		}

		function part( $chan_name, $reason = "" )
		{
			if(empty($reason))
				$this->net->sendf( FMT_PART, $this->get_numeric(), $chan_name );
			else 
				$this->net->sendf( FMT_PART_REASON, $this->get_numeric(), $chan_name, $reason );
			
			$this->net->remove_channel_user( $chan_name, $this->get_numeric() );
		}
	
		function kill( $user_num, $reason = 'So long...')
		{
			if( is_user($user_num) )
				$user_num = $user_num->get_numeric();
			
			if( !($user = $this->net->get_user($user_num)) )
				return false;
			
			$my_serv = $this->net->get_server( $this->get_server_numeric() );
			$this->net->sendf( FMT_KILL, $this->get_numeric(), $user_num, 
				$this->get_nick(), $reason );
			$this->net->remove_user( $user_num );
		}
	}

?>
