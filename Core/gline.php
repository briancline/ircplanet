<?php

	class Gline
	{
		var $mask;
		var $expire_ts;
		var $reason;
		
		function __construct( $mask, $duration, $reason )
		{
			$this->mask = $mask;
			$this->expire_ts = time() + $duration;
			$this->reason = $reason;
		}
		
		function get_mask()          { return $this->mask; }
		function get_expire_ts()     { return $this->expire_ts; }
		function get_duration()      { return $this->expire_ts - time(); }
		function get_reason()        { return $this->reason; }
		function is_expired()        { return (time() >= $this->expire_ts); }
		
		function matches( $host )
		{
			if( is_object($host) )
				return fnmatch( $this->mask, $host->get_gline_host() );
			else
				return fnmatch( $this->mask, $host );
		}
		
		function __toString() { return $this->mask; }
	}

?>