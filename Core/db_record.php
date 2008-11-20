<?php

	/**
	 * MySQL Record base abstraction class.
	 *
	 */
	class DB_Record
	{
		protected $_table_name = 'unknown';
		protected $_key_field = 'unknown';
		protected $_exclude_from_insert = array();
		protected $_exclude_from_update = array();
		protected $_update_timestamp_field = '';
		protected $_insert_timestamp_field = '';
		protected $_record_exists = false;
		
		
		function __construct($id = 0)
		{
			$this->_exclude_from_insert[] = $this->_key_field;
			$this->_exclude_from_update[] = $this->_key_field;
			
			
			if($id > 0)
			{
				if(is_array($id))
				{
					$row = $id;
				}
				else
				{
					$res = db_query(
						"select * ".
						"from `$this->_table_name` ".
						"where `$this->_key_field` = '$id'");
					
					if($res && mysql_num_rows($res) == 1)
						$row = mysql_fetch_assoc($res);
					else
						return false;
				}
				
				if(!empty($row))
				{
					$this->load_from_row($row);
				}
			}
			
			if(!method_exists($this, 'record_construct'))
				die("Cannot find the ". get_class($this) ." record constructor (record_construct).");
			if(!method_exists($this, 'record_destruct'))
				die("Cannot find the ". get_class($this) ." record destructor (record_destruct).");
			
			$this->record_construct(func_get_args());
		}
		
		
		function __destruct()
		{
			$this->record_destruct();
		}
		
		
		private function is_field_excluded($field, $check_array)
		{
			return (
				   $field[0] == '_' 
				|| in_array($field, $check_array) 
				|| $field == $this->_update_timestamp_field 
				|| $field == $this->_insert_timestamp_field
			);
		}
		
		
		private function get_key_value()
		{
			$key_name = $this->_key_field;
			return $this->$key_name;
		}
		
		
		public function get_update_ts()
		{
			$fld = $this->_update_timestamp_field;
			
			if(empty($fld))
				return 0;
			
			return $this->$fld;
		}
		
		
		public function get_create_ts()
		{
			$fld = $this->_create_timestamp_field;
			
			if(empty($fld))
				return 0;
			
			return $this->$fld;
		}
		
		
		public function needs_refresh()
		{
			if(empty($this->_update_timestamp_field))
				return false;
			
			$res = db_query(
				"select `$this->_update_timestamp_field` ".
				"from `$this->_table_name` ".
				"where `$this->_key_field` = '$id'");
			
			if(!$res || mysql_num_rows($res) != 1)
				return false;
			
			$old_ts = $this->get_update_ts();
			$new_ts = mysql_result($res, 1);
			
			$child_refresh = true;
			if(method_exists($this, 'record_needs_refresh'))
				$child_refresh = $this->record_needs_refresh();
			
			if($new_ts > $old_ts)
				return (true && $child_refresh);
		}
		
		
		public function load_from_row($row)
		{
			foreach($row as $column => $value)
			{
				if($column == $this->_insert_timestamp_field || $column == $this->_update_timestamp_field)
					$value = strtotime($value);
				
				$this->$column = $value;
			}
			
			$this->_record_exists = true;
		}
		
		
		public function record_exists()
		{
			return $this->_record_exists;
		}
		
		
		private function get_update_fieldlist()
		{
			$fields = get_object_vars($this);
			$list = '';
			
			foreach($fields as $field => $value)
			{
				if(!$this->is_field_excluded($field, $this->_exclude_from_update))
				{
					if(!empty($list))
						$list .= ', ';
					
					$list .= "`$field` = '". addslashes($value) ."'";
				}
			}
			
			return $list;
		}
		
		
		private function get_insert_fieldlist()
		{
			$fields = get_object_vars($this);
			$list = '';
			
			foreach($fields as $field => $value)
			{
				if(!$this->is_field_excluded($field, $this->_exclude_from_insert))
				{
					if(!empty($list))
						$list .= ', ';
					
					$list .= "`$field`";
				}
			}
			
			return $list;
		}
		
		
		private function get_insert_valuelist()
		{
			$fields = get_object_vars($this);
			$list = '';
			
			foreach($fields as $field => $value)
			{
				if(!$this->is_field_excluded($field, $this->_exclude_from_insert))
				{
					if(!empty($list))
						$list .= ', ';
					
					$list .= "'". addslashes($value) ."'";
				}
			}
			
			return $list;
		}
		
		
		function save()
		{
			$key_name = $this->_key_field;
			$key_value = addslashes($this->get_key_value());
			
			if(!$this->record_exists())
			{
				$fields = $this->get_insert_fieldlist();
				$values = $this->get_insert_valuelist();
				$i_field = $this->_insert_timestamp_field;
				
				if(!empty($i_field))
				{
					$fields .= ", `$i_field`";
					$values .= ", NOW()";
					
					$this->$i_field = time();
				}
				
				db_query("insert into `$this->_table_name` ($fields) values ($values)", true);
				
				$this->$key_name = mysql_insert_id();
				$this->_record_exists = true;
			}
			else
			{
				$fields = $this->get_update_fieldlist();
				$u_field = $this->_update_timestamp_field;

				if(!empty($u_field))
				{
					if(!empty($fields))
						$fields .= ', ';
					
					$fields .= "`$u_field` = NOW()";
					
					$this->$u_field = time();
				}
				
				db_query("update `$this->_table_name` set $fields where `$key_name` = '$key_value'", true);
				$this->_record_exists = true;
			}
			
			if(method_exists($this, 'record_save'))
				$this->record_save();
		}
		
		
		function refresh()
		{
			$id = $this->get_key_value();
			$res = db_query(
				"select * ".
				"from `$this->_table_name` ".
				"where `$this->_key_field` = '$id'");
			
			if(!$res || mysql_num_rows($res) != 1)
				return false;
			
			$row = mysql_fetch_assoc($res);
			$this->load_from_row($row);
			
			if(method_exists($this, 'record_refresh'))
				$this->record_refresh();
			
			return true;
		}
		
		
		/**
		 * Deletes record from the table.
		 *
		 */
		function delete()
		{
			$key_value = $this->get_key_value();
			if($key_value != 0)
				db_query("delete from `$this->_table_name` where `$this->_key_field` = '$key_value'", true);
			
			$this->_record_exists = false;
			
			if(method_exists($this, 'record_delete'))
				$this->record_delete();
		}
	}
	
?>
