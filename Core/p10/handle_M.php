<?php

	/**
	 * AEBIO M brian :-i
	 * AEBIO M brian :+id
	 * AEBIO M brian :-d
	 * Vs M #radio +o AEBIO 0
	 * AEBIO M #radio +v AEBIO
	 * AEBIO M #radio -v AEBIO
	 * AEBIO M #radio -o+smv AEBIO AEBIO
	 * AEBIO M #coder-com +ilk 50 haha
	 */
	
	$target = $args[2];
	$is_chan = ($target[0] == '#');
	$readable_args = array();
	
	if( $is_chan )
	{
		$modes = $args[3];
		$mode_arg = 4;
		$chan = $this->channels[$chan_key];
		$add = '';
		
		for( $i = 0; $i < strlen($modes); ++$i )
		{
			$mode = $modes[$i];
			
			if( $mode == '+' ) {
				$add = true;
			}
			else if( $mode == '-' ) {
				$add = false;
			}
			else if( $mode == 'l' )
			{
				if( $add ) {
					$limit = $args[$mode_arg++];
					$chan->add_mode( $mode );
					$chan->set_limit( $limit );
					$readable_args[] = $limit;
				}
				else {
					$chan->remove_mode( $mode );
					$chan->set_limit( 0 );
				}
			}
			else if( $mode == 'k' )
			{
				if( $add ) {
					$key = $args[$mode_arg++];
					$chan->add_mode( $mode );
					$chan->set_key( $key );
					$readable_args[] = $key;
				}
				else {
					$key = $args[$mode_arg++];
					$chan->remove_mode( $mode );
					$chan->set_key( '' );
					$readable_args[] = $key;
				}
			}
			else if( $mode == 'o' )
			{
				$numeric = $args[$mode_arg++];
				if( $add )
					$chan->add_op( $numeric );
				else
					$chan->remove_op( $numeric );
					
				$user = $this->get_user( $numeric );
				$readable_args[] = $user->get_nick();
			} 
			else if( $mode == 'v' )
			{
				$numeric = $args[$mode_arg++];
				if( $add )
					$chan->add_voice( $numeric );
				else
					$chan->remove_voice( $numeric );
					
				$user = $this->get_user( $numeric );
				$readable_args[] = $user->get_nick();
			}
			else if( $mode == 'b' )
			{
				$mask = $args[$mode_arg++];
				if( $add )
					$chan->add_ban( $mask, time() );
				else
					$chan->remove_ban( $mask );
					
				$readable_args[] = $mask;
			}
			else
			{
				if( $add )
					$chan->add_mode( $mode );
				else
					$chan->remove_mode( $mode );
			}
		}
	}
	else
	{
		$user = $this->get_user_by_nick( $target );
		$modes = $args[3];
		
		$modes = $args[3];
		$add = '';
		
		for( $i = 0; $i < strlen($modes); ++$i )
		{
			$mode = $modes[$i];
			
			if( $mode == '+' ) {
				$add = true;
			}
			else if( $mode == '-' ) {
				$add = false;
			}
			else {
				if( $add )
					$user->add_mode( $mode );
				else
					$user->remove_mode( $mode );
			}
		}
	}
	
	$readable_args = join( ' ', $readable_args );
	
?>