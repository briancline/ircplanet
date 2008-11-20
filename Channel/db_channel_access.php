<?php

	class DB_Channel_Access extends DB_Record
	{
		protected $_table_name = 'channel_access';
		protected $_key_field = 'access_id';
		
		protected $user_id;
		protected $chan_id;
		protected $level;
		protected $suspend = 0;
		protected $protect = 0;
		protected $auto_op = 1;
		protected $auto_voice = 0;
		
		protected function record_construct()   { }
		protected function record_destruct()    { }
		
		public function get_user_id()        { return $this->user_id; }
		public function get_chan_id()        { return $this->chan_id; }
		public function get_level()          { return $this->level; }
		
		public function is_suspended()       { return 1 == $this->suspend; }
		public function is_protected()       { return 1 == $this->protect; }
		public function auto_ops()           { return 1 == $this->auto_op; }
		public function auto_voices()        { return 1 == $this->auto_voice; }
		
		public function set_chan_id($n)      { $this->chan_id = $n; }
		public function set_user_id($n)      { $this->user_id = $n; }
		public function set_level($n)        { $this->level = $n; }
		public function set_suspend($b)      { $this->suspend = $b ? 1 : 0; }
		public function set_protect($b)      { $this->protect = $b ? 1 : 0; }
		public function set_auto_op($b)      { $this->auto_op = $b ? 1 : 0; }
		public function set_auto_voice($b)   { $this->auto_voice = $b ? 1 : 0; }
	}
	
?>