<?php
/*
 * ircPlanet Services for ircu
 * Copyright (c) 2005 Brian Cline.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:

 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of ircPlanet nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without 
 *    specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

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
		protected $_recordExists = false;
		
		
		function __construct($id = 0)
		{
			$this->_exclude_from_insert[] = $this->_key_field;
			$this->_exclude_from_update[] = $this->_key_field;
			
			
			if ($id > 0) {
				if (is_array($id)) {
					$row = $id;
				}
				else {
					$res = db_query(
						"select * ".
						"from `$this->_table_name` ".
						"where `$this->_key_field` = '$id'");
					
					if ($res && mysql_num_rows($res) == 1)
						$row = mysql_fetch_assoc($res);
					else
						return false;
				}
				
				if (!empty($row)) {
					$this->loadFromRow($row);
				}
			}
			
			if (!method_exists($this, 'recordConstruct'))
				die("Cannot find the ". get_class($this) ." record constructor (recordConstruct).");
			if (!method_exists($this, 'recordDestruct'))
				die("Cannot find the ". get_class($this) ." record destructor (recordDestruct).");
			
			$this->recordConstruct(func_get_args());
		}
		
		
		function __destruct()
		{
			$this->recordDestruct();
		}
		
		
		private function isFieldExcluded($field, $check_array)
		{
			return (
				   $field[0] == '_' 
				|| in_array($field, $check_array) 
				|| $field == $this->_update_timestamp_field 
				|| $field == $this->_insert_timestamp_field
			);
		}
		
		
		private function getKeyValue()
		{
			$key_name = $this->_key_field;
			return $this->$key_name;
		}
		
		
		public function getUpdateTs()
		{
			$fld = $this->_update_timestamp_field;
			
			if (empty($fld))
				return 0;
			
			return $this->$fld;
		}
		
		
		public function getCreateTs()
		{
			$fld = $this->_create_timestamp_field;
			
			if (empty($fld))
				return 0;
			
			return $this->$fld;
		}
		
		
		public function needsRefresh()
		{
			if (empty($this->_update_timestamp_field))
				return false;
			
			$res = db_query(
				"select `$this->_update_timestamp_field` ".
				"from `$this->_table_name` ".
				"where `$this->_key_field` = '$id'");
			
			if (!$res || mysql_num_rows($res) != 1)
				return false;
			
			$old_ts = $this->getUpdateTs();
			$new_ts = mysql_result($res, 1);
			
			$child_refresh = true;
			if (method_exists($this, 'record_needsRefresh'))
				$child_refresh = $this->record_needsRefresh();
			
			if ($new_ts > $old_ts)
				return (true && $child_refresh);
		}
		
		
		public function loadFromRow($row)
		{
			foreach ($row as $column => $value) {
				if ($column == $this->_insert_timestamp_field || $column == $this->_update_timestamp_field)
					$value = strtotime($value);
				
				$this->$column = $value;
			}
			
			$this->_recordExists = true;
		}
		
		
		public function recordExists()
		{
			return $this->_recordExists;
		}
		
		
		private function getUpdateFieldlist()
		{
			$fields = get_object_vars($this);
			$list = '';
			
			foreach ($fields as $field => $value) {
				if (!$this->isFieldExcluded($field, $this->_exclude_from_update)) {
					if (!empty($list))
						$list .= ', ';
					
					$list .= "`$field` = '". addslashes($value) ."'";
				}
			}
			
			return $list;
		}
		
		
		private function getInsertFieldlist()
		{
			$fields = get_object_vars($this);
			$list = '';
			
			foreach ($fields as $field => $value) {
				if (!$this->isFieldExcluded($field, $this->_exclude_from_insert)) {
					if (!empty($list))
						$list .= ', ';
					
					$list .= "`$field`";
				}
			}
			
			return $list;
		}
		
		
		private function getInsertValuelist()
		{
			$fields = get_object_vars($this);
			$list = '';
			
			foreach ($fields as $field => $value) {
				if (!$this->isFieldExcluded($field, $this->_exclude_from_insert)) {
					if (!empty($list))
						$list .= ', ';
					
					$list .= "'". addslashes($value) ."'";
				}
			}
			
			return $list;
		}
		
		
		function save()
		{
			$key_name = $this->_key_field;
			$key_value = '';
			
			if (!$this->recordExists()) {
				$fields = $this->getInsertFieldlist();
				$values = $this->getInsertValuelist();
				$i_field = $this->_insert_timestamp_field;
				
				if (!empty($i_field)) {
					$fields .= ", `$i_field`";
					$values .= ", NOW()";
					
					$this->$i_field = time();
				}
				
				db_query("insert into `$this->_table_name` ($fields) values ($values)");
				
				$this->$key_name = mysql_insert_id();
				$this->_recordExists = true;
			}
			else {
				$key_value = addslashes($this->getKeyValue());
				$fields = $this->getUpdateFieldlist();
				$u_field = $this->_update_timestamp_field;

				if (!empty($u_field)) {
					if (!empty($fields))
						$fields .= ', ';
					
					$fields .= "`$u_field` = NOW()";
					
					$this->$u_field = time();
				}
				
				db_query("update `$this->_table_name` set $fields where `$key_name` = '$key_value'");
				$this->_recordExists = true;
			}
			
			if (method_exists($this, 'recordSave'))
				$this->recordSave();
		}
		
		
		function refresh()
		{
			$id = $this->getKeyValue();
			$res = db_query(
				"select * ".
				"from `$this->_table_name` ".
				"where `$this->_key_field` = '$id'");
			
			if (!$res || mysql_num_rows($res) != 1)
				return false;
			
			$row = mysql_fetch_assoc($res);
			$this->loadFromRow($row);
			
			if (method_exists($this, 'record_refresh'))
				$this->record_refresh();
			
			return true;
		}
		
		
		/**
		 * Deletes record from the table.
		 *
		 */
		function delete()
		{
			$key_value = $this->getKeyValue();
			if ($key_value != 0)
				db_query("delete from `$this->_table_name` where `$this->_key_field` = '$key_value'");
			
			$this->_recordExists = false;
			
			if (method_exists($this, 'recordDelete'))
				$this->recordDelete();
		}
	}
	

