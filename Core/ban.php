<?php

	class Ban
	{
		var $mask;
		var $setby;
		var $ts;
		
		function __construct( $mask, $ts = 0, $setby = "" )
		{
			if( $ts == 0 )
				$ts = time();
			
			$ex_pos = strpos( $mask, '!' );
			$at_pos = strpos( $mask, '@' );
			$ident = substr( $mask, $ex_pos, $at_pos - $ex_pos );

			if( strlen($ident) > IDENT_LEN )
				$mask = substr($mask, 0, $ex_pos) .'!*'. right($ident, IDENT_LEN) .'@'. substr($mask, $at_pos);
			
			$this->mask = $mask;
			$this->setby = $setby;
			$this->ts = $ts;
		}
		
		function matches( $host )
		{
			if( is_object($host) )
				return fnmatch( $this->mask, $host->get_full_mask() );
			else
				return fnmatch( $this->mask, $host );
		}
	}

?>