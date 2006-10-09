<?php

	class DB_Channel_Access
	{
		var $user_id;
		var $chan_id;
		var $level;
		var $suspend = 0;
		var $protect = 0;
		var $auto_op = 1;
		var $auto_voice = 0;
		
		function __construct( $user_id, $chan_id )
		{
			$this->user_id = $user_id;
			$this->chan_id = $chan_id;
		}
		
		function get_user_id()        { return $this->user_id; }
		function get_chan_id()        { return $this->chan_id; }
		function get_level()          { return $this->level; }
		
		function is_suspended()       { return 1 == $this->suspend; }
		function is_protected()       { return 1 == $this->protect; }
		function auto_ops()           { return 1 == $this->auto_op; }
		function auto_voices()        { return 1 == $this->auto_voice; }
		
		function set_level($n)        { $this->level = $n; }
		function set_suspend($b)      { $this->suspend = $b ? 1 : 0; }
		function set_protect($b)      { $this->protect = $b ? 1 : 0; }
		function set_auto_op($b)      { $this->auto_op = $b ? 1 : 0; }
		function set_auto_voice($b)   { $this->auto_voice = $b ? 1 : 0; }


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
				if( !empty($list) )
					$list .= ', ';
				
				$list .= "`". $field ."` = '". addslashes($this->$field) ."'";
			}
			
			return $list;
		}
		
		
		function get_insert_fieldlist()
		{
			$fields = get_class_vars( __CLASS__ );
			$list = '';
			foreach( $fields as $field => $val )
			{
				if( !empty($list) )
					$list .= ', ';
				
				$list .= "`$field`";
			}
			
			return $list;
		}
		
		
		function get_insert_valuelist()
		{
			$fields = get_class_vars( __CLASS__ );
			$list = '';
			foreach( $fields as $field => $val )
			{
				if( !empty($list) )
					$list .= ', ';
				
				$list .= "'". addslashes($this->$field) ."'";
			}
			
			return $list;
		}

	}
?>