<?php

	$CHANNELUSER_MODES = array(
		'o' => array( 'const' => 'CUMODE_OP',          'uint' => 0x0001 ),
		'v' => array( 'const' => 'CUMODE_VOICE',       'uint' => 0x0002 )
	);
	

	class ChannelUser
	{
		var $numeric;
		var $modes;
		
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