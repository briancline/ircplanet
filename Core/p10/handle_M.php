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
