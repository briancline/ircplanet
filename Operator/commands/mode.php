<?php

	if( !($chan = $this->get_channel($chan_name)) )
	{
		$bot->noticef( $user, "Nobody is on channel %s.", $chan_name );
		return false;
	}
	
	$modes = $pargs[2];
	$mode_str = '';
	$mode_arg = 3;
	$mode_add = true;
	$mode_args = array();
	$add_modes = $rem_modes = array();
	$new_key = $new_limit = '';
	
	for( $m = 0; $m < strlen($modes); ++$m )
	{
		$mode = $modes[$m];
		
		switch($mode)
		{
			case '+':  $mode_add = true;  $mode_str .= $mode; break;
			case '-':  $mode_add = false; $mode_str .= $mode; break;
			case 'i':
			case 'm':
			case 'n':
			case 'p':
			case 'r':
			case 's':
			case 't':
				$mode_str .= $mode;

				if( $mode_add )
					$add_modes[] = $mode;
				else
					$rem_modes[] = $mode;
				
				break;
			
			case 'k':
				if( $mode_add )
				{
					if( $cmd_num_args < $mode_arg )
					{
						$bot->notice( $user, 'You did not specify a key!' );
						return false;
					}
					
					$mode_str .= $mode;
					$add_modes[] = $mode;

					$new_key = $pargs[$mode_arg];
					$mode_args[] = $pargs[$mode_arg++];
					break;
				}
				else
				{
					$mode_args[] = '*';
					$rem_modes[] = $mode;
					$mode_str .= $mode;
					break;
				}
			
			case 'l':
				if( $mode_add )
				{
					if( $cmd_num_args < $mode_arg )
					{
						$bot->notice( $user, 'You did not specify a limit!' );
						return false;
					}
					
					$new_limit = $pargs[$mode_arg];
					if( $new_limit <= 0 || !is_numeric($new_limit) )
					{
						$new_limit = 0;
						break;
					}
					
					$mode_str .= $mode;
					$add_modes[] = $mode;

					$mode_args[] = $pargs[$mode_arg++];
					break;
				}
				else
				{
					$rem_modes[] = $mode;
					$mode_str .= $mode;
					break;
				}
			
			case 'o':
			case 'v':
			case 'b':
				$bot->notice( $user, 'The mode command cannot be used to change ops, voices, or bans.' );
				return false;
				break;
			
			default:
				$bot->noticef( $user, '%s is not a valid channel mode!', $mode );
				return false;
				break;
		}
	}
	
	if( strlen($mode_str) > 0 )
	{
		if( !eregi('^[+-]', $mode_str) )
			$mode_str = '+'. $mode_str;
		if( count($mode_args) > 0 )
			$mode_str .= ' '. join(' ', $mode_args);
		
		$this->mode( $chan_name, $mode_str );
	}
	
	if( count($add_modes) > 0 )
		$chan->add_modes( join('', $add_modes) );
	if( count($rem_modes) > 0 )
		$chan->remove_modes( join('', $rem_modes) );
	if( strlen($new_key) > 0 )
		$chan->set_key( $new_key );
	if( $new_limit > 0 )
		$chan->set_limit( $new_limit );
	
//	$bot->noticef( $user, '%s modes are now: %s %s %s', $chan->get_name(), $chan->get_modes(), $chan->get_limit(), $chan->get_key() );
	
?>