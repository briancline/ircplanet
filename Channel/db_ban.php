<?php

	class DB_Ban
	{
		var $chan_id;
		var $user_id;
		var $set_ts = 0;
		var $expire_ts = 0;
		var $level = 75;
		var $mask;
		var $reason;
		
		function __construct( $chan_id, $user_id, $mask, $duration = 1800, $level = 75, $reason = '' )
		{
			$mask = fix_host_mask( $mask );

			$this->chan_id = $chan_id;
			$this->user_id = $user_id;
			$this->set_ts = time();
			$this->expire_ts = time() + $duration;
			$this->mask = $mask;
			$this->level = $level;
			$this->reason = $reason;
		}
		
		function get_user_id()        { return $this->user_id; }
		function get_chan_id()        { return $this->chan_id; }
		function get_set_ts()         { return $this->set_ts; }
		function get_expire_ts()      { return $this->expire_ts; }
		function get_level()          { return $this->level; }
		function get_mask()           { return $this->mask; }
		function get_reason()         { return $this->reason; }
		
		function set_ts($n)           { $this->set_ts = $n; }
		function set_level($n)        { $this->level = $n; }
		function set_reason($s)       { $this->reason = $s; }
		
		
		function matches( $host )
		{
			if( is_object($host) )
				return fnmatch( $this->mask, $host->get_full_mask() );
			else
				return fnmatch( $this->mask, $host );
		}
		
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