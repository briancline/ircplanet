<?php

	define( 'MAXLEN_USERINFOLINE', 150 );
	define( 'MAXLEN_USERNAME',      20 );
	define( 'MAXLEN_USEREMAIL',    100 );

	class DB_User
	{
		var $id;
		var $name;
		var $password;
		var $email;
		var $info_line;
		var $lastseen_ts = 0;
		var $register_ts = 0;
		var $suspend = 0;
		var $no_purge = 0;
		var $auto_op = 0;
		var $auto_voice = 0;
		var $enforce_nick = 0;
		
		function __construct( $name )
		{
			$this->name = $name;
			$this->register_ts = time();
		}
		
		function get_id()             { return $this->id; }
		function get_name()           { return $this->name; }
		function get_password()       { return $this->password; }
		function get_email()          { return $this->email; }
		function get_info_line()      { return $this->info_line; }
		function get_register_ts()    { return $this->register_ts; }
		function get_lastseen_ts()    { return $this->lastseen_ts; }
		
		function has_info_line()      { return !empty($this->info_line); }
		function is_suspended()       { return 1 == $this->suspend; }
		function is_permanent()       { return 1 == $this->no_purge; }
		function auto_ops()           { return 1 == $this->auto_op; }
		function auto_voices()        { return 1 == $this->auto_voice; }
		function enforces_nick()      { return 1 == $this->enforce_nick; }
		
		function set_password($s)     { $this->password = $s; }
		function set_email($s)        { $this->email = $s; }
		function set_info_line($s)    { $this->info_line = $s; }
		function set_suspend($b)      { $this->suspend = $b ? 1 : 0; }
		function set_permanent($b)    { $this->no_purge = $b ? 1 : 0; }
		function set_auto_op($b)      { $this->auto_op = $b ? 1 : 0; }
		function set_auto_voice($b)   { $this->auto_voice = $b ? 1 : 0; }
		function set_enforce_nick($b) { $this->enforce_nick = $b ? 1 : 0; }
		function update_lastseen()    { $this->lastseen_ts = time(); }
		

		function load_from_row( $row )
		{
			foreach( $row as $var => $value )
				$this->$var = $value;
		}
		
		function get_update_fieldlist()
		{
			$fields = get_class_vars( __CLASS__ );
			$list = '';
			foreach( $fields as $field => $val )
			{
				if( $field != 'id' )
				{
					if( !empty($list) )
						$list .= ', ';
					
					$list .= "`". $field ."` = '". addslashes($this->$field) ."'";
				}
			}
			
			return $list;
		}
		
		function get_insert_fieldlist()
		{
			$fields = get_class_vars( __CLASS__ );
			$list = '';
			foreach( $fields as $field => $val )
			{
				if( $field != 'id' )
				{
					if( !empty($list) )
						$list .= ', ';
					
					$list .= "`$field`";
				}
			}
			
			return $list;
		}
		
		
		function get_insert_valuelist()
		{
			$fields = get_class_vars( __CLASS__ );
			$list = '';
			foreach( $fields as $field => $val )
			{
				if( $field != 'id' )
				{
					if( !empty($list) )
						$list .= ', ';
					
					$list .= "'". addslashes($this->$field) ."'";
				}
			}
			
			return $list;
		}
		
		
		function save( $field = '' )
		{
			if( $this->id > 0 )
			{
				$fieldlist = $this->get_update_fieldlist();
				db_query( "update `accounts` set $fieldlist where id = ". $this->id );
			}
			else
			{
				$fields = $this->get_insert_fieldlist();
				$values = $this->get_insert_valuelist();
				db_query( "insert into `accounts` ($fields) values ($values)" );
				$this->id = mysql_insert_id();
			}
			
		}
		
	}

?>