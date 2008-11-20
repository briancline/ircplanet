<?php

	define( 'MAXLEN_USERINFOLINE', 150 );
	define( 'MAXLEN_USERNAME',      20 );
	define( 'MAXLEN_USEREMAIL',    100 );
	
	require_once('db_record.php');
	
	class DB_User extends DB_Record
	{
		protected $_table_name = 'accounts';
		protected $_key_field = 'account_id';
		protected $_insert_timestamp_field = 'create_date';
		protected $_update_timestamp_field = 'update_date';
		
		protected $account_id;
		protected $name;
		protected $password;
		protected $email;
		protected $info_line;
		protected $lastseen_ts = 0;
		protected $register_ts = 0;
		protected $suspend = 0;
		protected $no_purge = 0;
		protected $auto_op = 0;
		protected $auto_voice = 0;
		protected $enforce_nick = 0;
		
		protected function record_construct()
		{
			if(func_num_args() == 0)
				return;
			
			$name = func_get_arg(0);
		}
		
		protected function record_destruct()
		{
			
		}
		
		public function get_id()             { return $this->account_id; }
		public function get_name()           { return $this->name; }
		public function get_password()       { return $this->password; }
		public function get_email()          { return $this->email; }
		public function get_info_line()      { return $this->info_line; }
		public function get_register_ts()    { return $this->register_ts; }
		public function get_lastseen_ts()    { return $this->lastseen_ts; }
		
		public function has_info_line()      { return !empty($this->info_line); }
		public function is_suspended()       { return 1 == $this->suspend; }
		public function is_permanent()       { return 1 == $this->no_purge; }
		public function auto_ops()           { return 1 == $this->auto_op; }
		public function auto_voices()        { return 1 == $this->auto_voice; }
		public function enforces_nick()      { return 1 == $this->enforce_nick; }
		
		public function set_name($s)         { $this->name = $s; }
		public function set_password($s)     { $this->password = $s; }
		public function set_email($s)        { $this->email = $s; }
		public function set_info_line($s)    { $this->info_line = $s; }
		public function set_suspend($b)      { $this->suspend = $b ? 1 : 0; }
		public function set_permanent($b)    { $this->no_purge = $b ? 1 : 0; }
		public function set_auto_op($b)      { $this->auto_op = $b ? 1 : 0; }
		public function set_auto_voice($b)   { $this->auto_voice = $b ? 1 : 0; }
		public function set_enforce_nick($b) { $this->enforce_nick = $b ? 1 : 0; }
		public function update_lastseen()    { $this->lastseen_ts = time(); }
	}

?>
