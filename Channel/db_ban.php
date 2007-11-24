<?php

	class DB_Ban extends DB_Record
	{
		protected $_table_name = 'channel_bans';
		protected $_key_field = 'ban_id';
		
		protected $ban_id;
		protected $chan_id;
		protected $user_id;
		protected $set_ts = 0;
		protected $expire_ts = 0;
		protected $level = 75;
		protected $mask;
		protected $reason;
		
		protected function record_construct() { }
		protected function record_destruct()  { }
		
		public function get_user_id()         { return $this->user_id; }
		public function get_chan_id()         { return $this->chan_id; }
		public function get_set_ts()          { return $this->set_ts; }
		public function get_expire_ts()       { return $this->expire_ts; }
		public function get_level()           { return $this->level; }
		public function get_mask()            { return $this->mask; }
		public function get_reason()          { return $this->reason; }
		
		public function set_chan_id($n)       { $this->chan_id = $n; }
		public function set_user_id($n)       { $this->user_id = $n; }
		public function set_ts($n)            { $this->set_ts = $n; }
		public function set_duration($n)      { $this->expire_ts = time() + $n; }
		public function set_mask($s)          { $this->mask = fix_host_mask($s); }
		public function set_level($n)         { $this->level = $n; }
		public function set_reason($s)        { $this->reason = $s; }
		
		public function matches( $host )
		{
			if( is_object($host) )
				return fnmatch( $this->mask, $host->get_full_mask() );
			else
				return fnmatch( $this->mask, $host );
		}	
	}
	
?>