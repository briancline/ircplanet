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

	$CHANNELUSER_MODES = array(
		'o' => array( 'const' => 'CUMODE_OP',          'uint' => 0x0001 ),
		'v' => array( 'const' => 'CUMODE_VOICE',       'uint' => 0x0002 )
	);
	

	class ChannelUser
	{
		var $numeric;
		var $modes = 0;
		
		function __construct( $numeric, $modes )
		{
			$this->numeric = $numeric;
			$this->add_modes( $modes );
		}
		
		
		static function is_valid_mode( $mode )
		{
			global $CHANNELUSER_MODES;
			return in_array( $mode, $CHANNELUSER_MODES );
		}
		
		static function is_valid_mode_int( $mode )
		{
			global $CHANNELUSER_MODES;
			foreach( $CHANNELUSER_MODES as $c => $i )
				if( $i['uint'] == $mode )
					return true;
			
			return false;
		}

		function add_modes( $str )
		{
			global $CHANNELUSER_MODES;
			foreach( $CHANNELUSER_MODES as $c => $i )
				if( strpos($str, $c) !== false ) $this->add_mode( $i['uint'] );
		}
		
		function add_mode( $mode )
		{
			global $CHANNELUSER_MODES;
			if( !is_int($mode) )
				return $this->add_mode( $CHANNELUSER_MODES[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && !$this->has_mode($mode) )
				$this->modes |= $mode;
		}
		
		function remove_mode( $mode )
		{
			global $CHANNELUSER_MODES;
			if( !is_int($mode) )
				return $this->remove_mode( $CHANNELUSER_MODES[$mode]['uint'] );
			if( $this->is_valid_mode_int($mode) && $this->has_mode($mode) )
				$this->modes &= ~$mode;
		}
		
		function clear_modes()
		{
			$this->modes = 0;
		}
		
		function has_mode( $mode )
		{
			global $CHANNELUSER_MODES;
			if( !is_int($mode) )
				return $this->has_mode( $CHANNELUSER_MODES[$mode]['uint'] );
			
			return( ($this->modes & $mode) == $mode );
		}
		
		function get_modes()
		{
			global $CHANNELUSER_MODES;

			$modes = '';
			foreach( $CHANNELUSER_MODES as $c => $i )
				if( $this->has_mode($c) ) $modes .= $c;
			
			return $modes;
		}
		
		function is_op()    { return $this->has_mode( CUMODE_OP ); }
		function is_voice() { return $this->has_mode( CUMODE_VOICE ); }
	}


	foreach( $CHANNELUSER_MODES as $c => $i )
	{
		if( !defined($i['const']) )
			define( $i['const'], $i['uint'] );
	}


?>
