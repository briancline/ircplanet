<?php
	
	require_once( 'db_channel_access.php' );
	require_once( 'db_ban.php' );
	
	define( 'MAXLEN_CHAN_PURPOSE',        200 );
	define( 'MAXLEN_CHAN_URL',            255 );
	define( 'MAXLEN_CHAN_DEFAULT_TOPIC',  255 );
	define( 'MAXLEN_CHAN_DEFAULT_MODES',   20 );
	define( 'MIN_CHAN_AUTOLIMIT_BUFFER',    1 );
	define( 'MAX_CHAN_AUTOLIMIT_BUFFER',  100 );
	define( 'MIN_CHAN_AUTOLIMIT_WAIT',      1 );
	define( 'MAX_CHAN_AUTOLIMIT_WAIT',    300 );
	
	class DB_Channel
	{
		var $id = 0;
		var $name;
		var $register_ts;
		var $create_ts;
		var $purpose;

		var $def_topic;
		var $def_modes;
		var $info_lines = 0;
		var $suspend = 0;
		var $no_purge = 0;
		var $auto_op = 1;
		var $auto_op_all = 0;
		var $auto_voice = 0;
		var $auto_voice_all = 0;
		var $auto_limit = 0;
		var $auto_limit_buffer = 5;
		var $auto_limit_wait = 30;
		var $strict_op = 0;
		var $strict_voice = 0;
		var $strict_modes = 0;
		var $strict_topic = 0;
		var $no_op = 0;
		var $no_voice = 0;
		
		var $levels = array();
		var $bans = array();
		
		function __construct( $name, $owner_id = 0 )
		{
			$this->name = $name;
			$this->register_ts = time();
			
			if( $owner_id > 0 )
			{
				$this->save();
				$owner = new DB_Channel_Access( $owner_id, $this->id );
				$owner->set_level( 500 );
				$this->add_access( $owner );
			}
		}
		

		function has_default_topic()       { return !empty($this->def_topic); }
		function has_default_modes()       { return !empty($this->def_modes); }
		function shows_info_lines()        { return 1 == $this->info_lines; }
		function is_suspended()            { return 1 == $this->suspend; }
		function is_permanent()            { return 1 == $this->no_purge; }
		function auto_ops()                { return 1 == $this->auto_op; }
		function auto_ops_all()            { return 1 == $this->auto_op_all; }
		function auto_voices()             { return 1 == $this->auto_voice; }
		function auto_voices_all()         { return 1 == $this->auto_voice_all; }
		function auto_limits()             { return 1 == $this->auto_limit; }
		function strict_ops()              { return 1 == $this->strict_op; }
		function strict_voices()           { return 1 == $this->strict_voice; }
		function strict_modes()            { return 1 == $this->strict_modes; }
		function strict_topic()            { return 1 == $this->strict_topic; }
		function no_ops()                  { return 1 == $this->no_op; }
		function no_voices()               { return 1 == $this->no_voice; }
		
		function get_id()                  { return $this->id; }
		function get_name()                { return $this->name; }
		function get_register_ts()         { return $this->register_ts; }
		function get_create_ts()           { return $this->create_ts; }
		function get_purpose()             { return $this->purpose; }
		function get_default_topic()       { return $this->def_topic; }
		function get_default_modes()       { return $this->def_modes; }
		function get_auto_limit_buffer()   { return $this->auto_limit_buffer; }
		function get_auto_limit_wait()     { return $this->auto_limit_wait; }
		
		function has_pending_autolimit()   { return $this->_alimit_pending; }
		function set_pending_autolimit($b) { $this->_alimit_pending = $b; }
		
		function set_create_ts($n)         { $this->create_ts = $n; }
		function set_purpose($s)           { $this->purpose = $s; }
		function set_default_topic($s)     { $this->def_topic = $s; }
		function set_default_modes($s)     { $this->def_modes = $s; }
		function set_info_lines($b)        { $this->info_lines = $b ? 1 : 0; }
		function set_suspend($b)           { $this->suspend = $b ? 1 : 0; }
		function set_permanent($b)         { $this->no_purge = $b ? 1 : 0; }
		function set_auto_op($b)           { $this->auto_op = $b ? 1 : 0; }
		function set_auto_op_all($b)       { $this->auto_op_all = $b ? 1 : 0; }
		function set_auto_voice($b)        { $this->auto_voice = $b ? 1 : 0; }
		function set_auto_voice_all($b)    { $this->auto_voice_all = $b ? 1 : 0; }
		function set_auto_limit($b)        { $this->auto_limit = $b ? 1 : 0; }
		function set_auto_limit_buffer($n) { $this->auto_limit_buffer = $n; }
		function set_auto_limit_wait($n)   { $this->auto_limit_wait = $n; }
		function set_strict_op($b)         { $this->strict_op = $b ? 1 : 0; }
		function set_strict_voice($b)      { $this->strict_voice = $b ? 1 : 0; }
		function set_strict_modes($b)      { $this->strict_modes = $b ? 1 : 0; }
		function set_strict_topic($b)      { $this->strict_topic = $b ? 1 : 0; }
		function set_no_op($b)             { $this->no_op = $b ? 1 : 0; }
		function set_no_voice($b)          { $this->no_voice = $b ? 1 : 0; }
		
		
		function load_from_row( $row )
		{
			foreach( $row as $var => $value )
				$this->$var = $value;
		}
		
		function add_access( $access_obj )
		{
			$user_id = $access_obj->get_user_id();
			$this->levels[$user_id] = $access_obj;
		}
		
		function remove_access( $user_id )
		{
			if( array_key_exists($user_id, $this->levels) )
				unset( $this->levels[$user_id] );
		}
		
		
		function get_level_by_id( $user_id )
		{
			if( array_key_exists($user_id, $this->levels) )
				return $this->levels[$user_id]->get_level();
			
			return 0;
		}
		
		
		function add_ban( $ban_obj )
		{
			$mask = strtolower( $ban_obj->get_mask() );
			$this->bans[$mask] = $ban_obj;
		}
		
		function get_ban( $mask )
		{
			$mask = strtolower( $mask );
			if( array_key_exists($mask, $this->bans) )
				return $this->bans[$mask];
			
			return false;
		}
		
		function remove_ban( $mask )
		{
			$mask = strtolower( $mask );
			if( array_key_exists($mask, $this->bans) )
				unset( $this->bans[$mask] );
		}
		
		function clear_bans()
		{
			$this->bans = array();
		}
		
		function count_matching_bans( $mask )
		{
			if( is_object($mask) )
				return $this->count_matching_bans( $mask->get_full_mask() );
			
			$match_count = 0;
			$mask = strtolower( $mask );
			
			foreach( $this->bans as $mask_iter => $ban )
			{
				if( fnmatch($mask_iter, $mask) )
					$match_count++;
			}
			
			return $match_count;
		}
		
		function has_matching_bans( $mask )
		{
			if( is_object($mask) )
				return $this->has_matching_bans( $mask->get_full_mask() );
			
			$mask = strtolower( $mask );
			
			foreach( $this->bans as $mask_iter => $ban )
			{
				if( fnmatch($mask_iter, $mask) )
					return true;
			}
			
			return false;
		}
		
		function get_matching_ban( $mask )
		{
			if( is_object($mask) )
				return $this->get_matching_ban( $mask->get_full_mask() );
			
			$mask = strtolower( $mask );
			
			foreach( $this->bans as $mask_iter => $ban )
			{
				if( fnmatch($mask_iter, $mask) )
					return $ban;
			}
			
			return false;
		}
		
		
		function get_matching_bans( $mask = '*' )
		{
			if( is_object($mask) )
				return $this->get_matching_bans( $mask->get_full_mask() );
			
			$matches = array();
			$mask = strtolower( $mask );
			
			foreach( $this->bans as $mask_iter => $ban )
			{
				if( $mask == '*' || fnmatch($mask, $mask_iter) )
					$matches[$mask_iter] = $ban;
			}
			
			if( count($matches) == 0 )
				return false;
			
			return $matches;
		}
		
		
		function get_update_fieldlist()
		{
			$fields = get_class_vars( __CLASS__ );
			$list = '';
			foreach( $fields as $field => $val )
			{
				if( $field != 'id' && $field != 'levels' && $field != 'bans' && $field[0] != '_' )
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
				if( $field != 'id' && $field != 'levels' && $field != 'bans' && $field[0] != '_' )
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
				if( $field != 'id' && $field != 'levels' && $field != 'bans' && $field[0] != '_' )
				{
					if( !empty($list) )
						$list .= ', ';
					
					$list .= "'". addslashes($this->$field) ."'";
				}
			}
			
			return $list;
		}
		
		
		function save()
		{
			if( $this->id != 0 )
			{
				$fields = $this->get_update_fieldlist();
				db_query( "update `channels` set $fields where `id` = '". $this->id ."'" );
				
				if( count($this->levels) > 0 )
					db_query( "delete from `channel_access` where `chan_id` = '". $this->id ."'" );

				db_query( "delete from `channel_bans` where `chan_id` = '". $this->id ."'" );
			}
			else
			{
				$fields = $this->get_insert_fieldlist();
				$values = $this->get_insert_valuelist();
				db_query( "insert into `channels` ($fields) values ($values)" );
				
				$this->id = mysql_insert_id();
			}
			
			foreach( $this->levels as $user_id => $access )
			{
				if( empty($user_id) || $user_id == 0 || $this->id == 0 )
					continue;
				
				$fields = $access->get_insert_fieldlist();
				$values = $access->get_insert_valuelist();
				db_query( "insert into `channel_access` ($fields) values ($values)" );
			}
			
			foreach( $this->bans as $mask => $ban )
			{
				if( empty($mask) || $ban == 0 || $this->id == 0 )
					continue;
				
				$fields = $ban->get_insert_fieldlist();
				$values = $ban->get_insert_valuelist();
				db_query( "insert into `channel_bans` ($fields) values ($values)" );
			}
		}
		
		
		function delete()
		{
			db_query( "delete from `channels` where `id` = '". $this->id ."'" );
			db_query( "delete from `channel_access` where `chan_id` = '". $this->id ."'" );
			db_query( "delete from `channel_bans` where `chan_id` = '". $this->id ."'" );
			$this->id = 0;
		}
		
	}
	
?>