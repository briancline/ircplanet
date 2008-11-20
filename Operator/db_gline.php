<?php

	class DB_Gline extends DB_Record
	{
		protected $_table_name = 'os_glines';
		protected $_key_field = 'gline_id';
		
		protected $gline_id;
		protected $set_ts = 0;
		protected $expire_ts = 0;
		protected $mask;
		protected $reason;
		
		protected function record_construct() { }
		protected function record_destruct()  { }
		
		public function get_set_ts()          { return $this->set_ts; }
		public function get_expire_ts()       { return $this->expire_ts; }
		public function get_remaining_secs()  { return $this->get_expire_ts() - time(); }
		public function get_mask()            { return $this->mask; }
		public function get_reason()          { return $this->reason; }
		public function is_expired()          { return $this->expire_ts < time(); }
		
		public function set_ts($n)            { $this->set_ts = $n; }
		public function set_duration($n)      { $this->expire_ts = time() + $n; }
		public function set_mask($s)          { $this->mask = fix_host_mask($s); }
		public function set_reason($s)        { $this->reason = $s; }
		
		public function matches( $host )
		{
			if( is_object($host) )
				return fnmatch( $this->mask, $host->get_gline_host() ) 
					|| fnmatch( $this->mask, $host->get_gline_ip );
			else
				return fnmatch( $this->mask, $host );
		}	
	}
	
?>
