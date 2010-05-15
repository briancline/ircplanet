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
			$args = func_get_args();
			$target = array_shift( $args );
			$format = array_shift( $args );
			
			if( is_object($target) && is_user($target) )
				$target = $target->get_numeric();
			
			$notice_text = irc_vsprintf( $format, $args );
			$this->net->sendf( FMT_PRIVMSG, $this->numeric, $target, $notice_text );
			$this->last_spoke = time();
		}
		
		function noticef( $target, $format )
		{
			$args = func_get_args();
			$target = array_shift( $args ); // Remove target 
			$format = array_shift( $args ); // Remove format
			
			if( is_object($target) && is_user($target) )
				$target = $target->get_numeric();
			
			$notice_text = irc_vsprintf( $format, $args );
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
		
		function op( $chan_name, $num_list )       { return $this->net->perform_chan_user_mode($this->get_numeric(), $chan_name, '+', 'o', $num_list); }
		function deop( $chan_name, $num_list )     { return $this->net->perform_chan_user_mode($this->get_numeric(), $chan_name, '-', 'o', $num_list); }
		function voice( $chan_name, $num_list )    { return $this->net->perform_chan_user_mode($this->get_numeric(), $chan_name, '+', 'v', $num_list); }
		function devoice( $chan_name, $num_list )  { return $this->net->perform_chan_user_mode($this->get_numeric(), $chan_name, '-', 'v', $num_list); }
		function ban( $chan_name, $num_list )      { return $this->net->perform_chan_user_mode($this->get_numeric(), $chan_name, '+', 'b', $num_list); }
		function unban( $chan_name, $num_list )    { return $this->net->perform_chan_user_mode($this->get_numeric(), $chan_name, '-', 'b', $num_list); }


		function invite( $nick, $chan_name )
		{
			$this->net->sendf( FMT_INVITE, $this->get_numeric(), $nick, $chan_name );
		}
		
		
		function topic( $chan_name, $topic, $chan_ts = 0 )
		{
			if( TOPIC_BURSTING && $chan_ts == 0 )
			{
				$chan = $this->net->get_channel( $chan_name );

				if( !$chan )
					return;

				$chan_ts = $chan->get_ts();
			}

			if( TOPIC_BURSTING )
				$this->net->sendf( FMT_TOPIC, $this->get_numeric(), $chan_name, $chan_ts, time(), $topic );
			else
				$this->net->sendf( FMT_TOPIC, $this->get_numeric(), $chan_name, $topic );
		}
		
		
		function clear_modes( $chan_name )
		{
			$chan = $this->net->get_channel( $chan_name );
			$this->net->send_mode_line( sprintf(FMT_MODE, $this->get_numeric(), $chan_name, '-psmntilkrD *', $chan->get_ts()) );
		}

		function mode( $chan_name, $modes )
		{
			$this->net->send_mode( $this, $chan_name, $modes );
		}
		
		function kick( $chan_name, $numeric, $reason )
		{
			$this->net->remove_channel_user( $chan_name, $numeric );
			$this->net->sendf( FMT_KICK, $this->get_numeric(), $chan_name, $numeric, $reason );
		}
		
		function join( $chan_name, $create_ts = 0 )
		{
			$chan = $this->net->get_channel( $chan_name );

			if( !$chan )
			{
				if( $create_ts == 0 )
					$create_ts = time();
				
				$this->net->sendf( FMT_CREATE, $this->get_numeric(), $chan_name, $create_ts );
				$this->net->add_channel( $chan_name, $create_ts );

				$chan = $this->net->get_channel( $chan_name );
				$chan->add_user( $this->get_numeric(), 'o' );
			}
			else
			{
				$this->net->sendf( FMT_JOIN, $this->get_numeric(), $chan_name, $chan->get_ts() );
				$this->net->add_channel_user( $chan_name, $this->get_numeric() );
			}
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


