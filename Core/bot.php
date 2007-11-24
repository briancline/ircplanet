<?php
	
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
			if( is_object($target) && get_class($target) == 'User' )
				$target = $target->get_numeric();
			
			$this->net->sendf( FMT_PRIVMSG, $this->numeric, $target, $text );
			$this->last_spoke = time();
		}

		function notice( $target, $text )
		{
			if( is_object($target) && get_class($target) == 'User' )
				$target = $target->get_numeric();
			
			$this->net->sendf( FMT_NOTICE, $this->numeric, $target, $text );
		}

		function messagef( $target, $format )
		{
			if( is_object($target) && get_class($target) == 'User' )
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
			if( is_object($target) && get_class($target) == 'User' )
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
			if( get_class($user_num) == 'User' )
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